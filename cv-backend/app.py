import os, sys, base64, datetime, time, threading
import numpy as np, cv2
from flask import Flask, request, jsonify
from flask_cors import CORS

sys.path.append(os.path.abspath(os.path.dirname(__file__)))

from config.database import SessionLocal, engine
from models.models import Base, User, PegawaiDetail, Kehadiran, CctvMonitoringLog, DeteksiAktivitas, ZonaKamera
from ai.vision import process_frame_zone, process_frame_zone_simulated, SLOT_COLORS_HEX, reset_detection_state

# Create tables on startup
Base.metadata.create_all(bind=engine)

app = Flask(__name__)
CORS(app)

def get_db():
    return SessionLocal()


# ─── PERSISTENT BACKGROUND MONITORING ENGINE ─────────────────────────────────
# Keeps surveillance running even if the browser reloads, switches pages, or logs out.
# Only stops when /api/stop-monitoring is explicitly called.
_MONITOR_LOCK = threading.Lock()
_MONITOR_STATE = {
    "active": False,
    "simulate": False,
    "started_at": None,
    "last_browser_frame_ts": 0.0,
    "detected_count": 0,
    "detections": [],
    "annotated_image": None,
    "thread": None
}


def _make_simulation_frame():
    """Generate a synthetic dark grid frame for server-side simulation."""
    frame = np.full((360, 640, 3), (42, 23, 15), dtype=np.uint8)
    for x in range(0, 640, 40):
        cv2.line(frame, (x, 0), (x, 360), (55, 35, 25), 1)
    for y in range(0, 360, 40):
        cv2.line(frame, (0, y), (640, y), (55, 35, 25), 1)
    ts_str = datetime.datetime.now().strftime("%H:%M:%S")
    cv2.putText(frame, f"BACKGROUND CCTV SIMULATION | {ts_str}", (12, 22),
                cv2.FONT_HERSHEY_SIMPLEX, 0.45, (140, 140, 140), 1)
    return frame


def _background_monitoring_worker():
    """
    Background worker that runs while _MONITOR_STATE['active'] is True.
    If the browser is closed/logged out (no frames pushed in > 2.0s), it automatically
    captures from local camera (cv2.VideoCapture(0)) or falls back to simulation so
    monitoring never stops until explicitly halted via /api/stop-monitoring.
    """
    cap = None
    try:
        while True:
            with _MONITOR_LOCK:
                if not _MONITOR_STATE["active"]:
                    break
                last_browser_ts = _MONITOR_STATE["last_browser_frame_ts"]
                force_sim = _MONITOR_STATE["simulate"]

            now_ts = time.time()
            # If browser is actively sending frames (< 2.0s ago), let browser feed drive it
            if now_ts - last_browser_ts < 2.0:
                if cap is not None:
                    cap.release()
                    cap = None
                time.sleep(0.3)
                continue

            # Browser is NOT sending frames (reloading, on another menu, or logged out)
            frame = None
            use_sim = force_sim

            if not use_sim:
                if cap is None:
                    cap = cv2.VideoCapture(0)
                    if cap.isOpened():
                        cap.set(cv2.CAP_PROP_FRAME_WIDTH, 640)
                        cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 360)
                if cap is not None and cap.isOpened():
                    ok, grabbed = cap.read()
                    if ok and grabbed is not None:
                        frame = grabbed
                    else:
                        use_sim = True
                else:
                    use_sim = True

            if frame is None:
                use_sim = True
                frame = _make_simulation_frame()

            db = get_db()
            try:
                if use_sim:
                    annotated, detections, count = process_frame_zone_simulated(db, frame)
                else:
                    annotated, detections, count = process_frame_zone(db, frame)

                b64_img = None
                if use_sim:
                    _, buf = cv2.imencode('.jpg', annotated, [cv2.IMWRITE_JPEG_QUALITY, 70])
                    b64_img = f"data:image/jpeg;base64,{base64.b64encode(buf).decode('utf-8')}"

                with _MONITOR_LOCK:
                    if _MONITOR_STATE["active"]:
                        _MONITOR_STATE["detected_count"] = count
                        _MONITOR_STATE["detections"] = detections
                        if b64_img:
                            _MONITOR_STATE["annotated_image"] = b64_img
            except Exception as e:
                print(f"[BackgroundMonitor] Error: {e}")
            finally:
                db.close()

            time.sleep(0.45)
    finally:
        if cap is not None:
            cap.release()


def _ensure_background_worker(simulate=False):
    with _MONITOR_LOCK:
        _MONITOR_STATE["active"] = True
        _MONITOR_STATE["simulate"] = bool(simulate)
        if not _MONITOR_STATE["started_at"]:
            _MONITOR_STATE["started_at"] = datetime.datetime.now().strftime("%H:%M:%S")
        t = _MONITOR_STATE.get("thread")
        if t is None or not t.is_alive():
            t = threading.Thread(target=_background_monitoring_worker, daemon=True)
            _MONITOR_STATE["thread"] = t
            t.start()


# ─── HEALTH ─────────────────────────────────────────────────────────────────
@app.route('/api/health', methods=['GET'])
def health():
    return jsonify({"status": "running", "time": str(datetime.datetime.now())})


# ─── KARYAWAN ────────────────────────────────────────────────────────────────
@app.route('/api/karyawan', methods=['GET'])
def get_karyawan():
    db = get_db()
    try:
        rows = db.query(PegawaiDetail).join(User).all()
        return jsonify([{
            "id": p.id, "name": p.user.name if p.user else "-",
            "jabatan": p.jabatan or "-", "nip": p.nip or "-"
        } for p in rows])
    finally:
        db.close()


# ─── ZONE CRUD ───────────────────────────────────────────────────────────────
@app.route('/api/zones', methods=['GET'])
def get_zones():
    """Return all configured zones with employee info."""
    db = get_db()
    try:
        zones = db.query(ZonaKamera).all()
        all_pegawai = db.query(PegawaiDetail).join(User).all()
        pegawai_map = {p.id: p for p in all_pegawai}

        data = []
        for i, z in enumerate(zones):
            p = pegawai_map.get(z.pegawai_id)
            data.append({
                "id": z.id,
                "pegawai_id": z.pegawai_id,
                "pegawai_name": p.user.name if p and p.user else "-",
                "jabatan": p.jabatan or "-" if p else "-",
                "x1_pct": z.x1_pct, "y1_pct": z.y1_pct,
                "x2_pct": z.x2_pct, "y2_pct": z.y2_pct,
                "label": z.label or f"Meja {chr(65+i)}",
                "warna": z.warna or SLOT_COLORS_HEX[i % len(SLOT_COLORS_HEX)]
            })

        # Include employees WITHOUT a zone
        configured_ids = {z.pegawai_id for z in zones}
        unzoned = []
        for i, p in enumerate(all_pegawai):
            if p.id not in configured_ids:
                unzoned.append({
                    "pegawai_id": p.id,
                    "pegawai_name": p.user.name if p.user else "-",
                    "jabatan": p.jabatan or "-",
                    "has_zone": False,
                    "warna": SLOT_COLORS_HEX[(len(zones) + i) % len(SLOT_COLORS_HEX)]
                })

        return jsonify({"zones": data, "unzoned": unzoned})
    finally:
        db.close()


@app.route('/api/zones', methods=['POST'])
def save_zone():
    """
    Create or update a zone for an employee.
    Body: { pegawai_id, x1_pct, y1_pct, x2_pct, y2_pct, label? }
    All pct values are 0-100.
    """
    data = request.json
    required = ['pegawai_id', 'x1_pct', 'y1_pct', 'x2_pct', 'y2_pct']
    if not data or not all(k in data for k in required):
        return jsonify({"error": f"Missing fields: {required}"}), 400

    db = get_db()
    try:
        pegawai_id = int(data['pegawai_id'])

        # Validate pegawai exists
        pegawai = db.query(PegawaiDetail).filter(PegawaiDetail.id == pegawai_id).first()
        if not pegawai:
            return jsonify({"error": "Pegawai tidak ditemukan"}), 404

        # Clamp values 0-100
        def clamp(v): return max(0.0, min(100.0, float(v)))
        x1, y1, x2, y2 = clamp(data['x1_pct']), clamp(data['y1_pct']), \
                          clamp(data['x2_pct']), clamp(data['y2_pct'])

        # Ensure x1 < x2, y1 < y2
        x1, x2 = min(x1, x2), max(x1, x2)
        y1, y2 = min(y1, y2), max(y1, y2)

        # Count existing zones to pick color
        zone_count = db.query(ZonaKamera).count()

        zone = db.query(ZonaKamera).filter(ZonaKamera.pegawai_id == pegawai_id).first()
        if zone:
            zone.x1_pct, zone.y1_pct = x1, y1
            zone.x2_pct, zone.y2_pct = x2, y2
            zone.label = data.get('label') or zone.label
            zone.updated_at = datetime.datetime.now()
        else:
            color_idx = zone_count % len(SLOT_COLORS_HEX)
            zone = ZonaKamera(
                pegawai_id=pegawai_id, x1_pct=x1, y1_pct=y1, x2_pct=x2, y2_pct=y2,
                label=data.get('label', f"Meja {chr(65 + zone_count)}"),
                warna=SLOT_COLORS_HEX[color_idx]
            )
            db.add(zone)

        db.commit()
        name = pegawai.user.name if pegawai.user else "-"
        return jsonify({"success": True, "message": f"Zona untuk {name} berhasil disimpan."})
    except Exception as e:
        db.rollback()
        return jsonify({"error": str(e)}), 500
    finally:
        db.close()


@app.route('/api/zones/<int:pegawai_id>', methods=['DELETE'])
def delete_zone(pegawai_id):
    db = get_db()
    try:
        zone = db.query(ZonaKamera).filter(ZonaKamera.pegawai_id == pegawai_id).first()
        if not zone:
            return jsonify({"error": "Zona tidak ditemukan"}), 404
        db.delete(zone)
        db.commit()
        return jsonify({"success": True})
    finally:
        db.close()


# ─── MONITORING STATUS & START ───────────────────────────────────────────────
@app.route('/api/monitoring-status', methods=['GET'])
def monitoring_status():
    """Return whether persistent monitoring is currently active on the server."""
    with _MONITOR_LOCK:
        return jsonify({
            "active": _MONITOR_STATE["active"],
            "simulate": _MONITOR_STATE["simulate"],
            "started_at": _MONITOR_STATE["started_at"],
            "detected_count": _MONITOR_STATE["detected_count"],
            "detections": _MONITOR_STATE["detections"]
        })


@app.route('/api/start-monitoring', methods=['POST'])
def start_monitoring():
    """Explicitly start persistent monitoring session."""
    data = request.json or {}
    simulate = bool(data.get('simulate', False))
    _ensure_background_worker(simulate=simulate)
    with _MONITOR_LOCK:
        return jsonify({
            "success": True,
            "active": _MONITOR_STATE["active"],
            "simulate": _MONITOR_STATE["simulate"],
            "started_at": _MONITOR_STATE["started_at"]
        })


# ─── PROCESS FRAME ───────────────────────────────────────────────────────────
@app.route('/api/process-frame', methods=['POST'])
def process_frame():
    """
    Process a camera frame using zone-based employee recognition.
    Body: { image: <base64 JPEG>, simulate: bool }
    """
    data = request.json
    if not data or 'image' not in data:
        return jsonify({"error": "Missing image"}), 400

    img_b64 = data['image']
    simulate = data.get('simulate', False)

    # Mark persistent monitoring active & update browser heartbeat timestamp
    with _MONITOR_LOCK:
        _MONITOR_STATE["last_browser_frame_ts"] = time.time()
    _ensure_background_worker(simulate=simulate)

    if ',' in img_b64:
        img_b64 = img_b64.split(',')[1]

    db = get_db()
    try:
        img_bytes = base64.b64decode(img_b64)
        np_arr = np.frombuffer(img_bytes, np.uint8)
        frame = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)

        if frame is None:
            return jsonify({"error": "Invalid image"}), 400

        if simulate:
            annotated, detections, count = process_frame_zone_simulated(db, frame)
        else:
            annotated, detections, count = process_frame_zone(db, frame)

        # Return annotated_image only if requested or in simulation mode to save bandwidth
        include_image = data.get('include_image', True)
        b64_img = None
        if include_image or simulate:
            _, buf = cv2.imencode('.jpg', annotated, [cv2.IMWRITE_JPEG_QUALITY, 72])
            b64_img = f"data:image/jpeg;base64,{base64.b64encode(buf).decode('utf-8')}"

        with _MONITOR_LOCK:
            _MONITOR_STATE["detected_count"] = count
            _MONITOR_STATE["detections"] = detections
            if b64_img:
                _MONITOR_STATE["annotated_image"] = b64_img

        return jsonify({
            "success": True,
            "detected_count": count,
            "detections": detections,
            "annotated_image": b64_img
        })
    except Exception as e:
        import traceback; traceback.print_exc()
        return jsonify({"error": str(e)}), 500
    finally:
        db.close()


# ─── STATS ───────────────────────────────────────────────────────────────────
@app.route('/api/stats', methods=['GET'])
def get_stats():
    db = get_db()
    today = datetime.date.today()
    now = datetime.datetime.now()
    try:
        # Hitung pegawai yang hadir dari data absen resmi
        total_hadir = db.query(Kehadiran).filter(
            Kehadiran.tanggal == today,
            Kehadiran.jenis == 'hadir'
        ).count()

        total_keluar = db.query(CctvMonitoringLog).filter(
            CctvMonitoringLog.tanggal == today,
            CctvMonitoringLog.status_terakhir == 'keluar_ruangan'
        ).count()

        # Hitung rata-rata durasi secara live (tambah elapsed jika masih di_meja)
        logs = db.query(CctvMonitoringLog).filter(
            CctvMonitoringLog.tanggal == today).all()
        live_durs = []
        for log in logs:
            dur = log.durasi_stay or 0
            if log.status_terakhir == 'di_meja' and log.last_seen:
                elapsed = (now - log.last_seen).total_seconds()
                if elapsed < 60:  # hanya tambah jika baru saja terdeteksi
                    dur = dur + int(elapsed)
            live_durs.append(dur)
        avg = sum(live_durs) / len(live_durs) if live_durs else 0

        return jsonify({
            "total_hadir": total_hadir,
            "total_keluar": total_keluar,
            "avg_stay_str": f"{int(avg//3600)}j {int((avg%3600)//60)}m"
        })
    finally:
        db.close()


# ─── LOG DETEKSI ─────────────────────────────────────────────────────────────
@app.route('/api/log-deteksi', methods=['GET'])
def get_log_deteksi():
    db = get_db()
    today = datetime.date.today()
    try:
        all_pegawai = db.query(PegawaiDetail).join(User).all()
        result = []

        for p in all_pegawai:
            log = db.query(CctvMonitoringLog).filter(
                CctvMonitoringLog.pegawai_id == p.id,
                CctvMonitoringLog.tanggal == today
            ).first()

            zone = db.query(ZonaKamera).filter(ZonaKamera.pegawai_id == p.id).first()
            dur = log.durasi_stay if log else 0
            status = log.status_terakhir if log else 'belum_terdeteksi'
            h, m = dur // 3600, (dur % 3600) // 60
            dur_str = f"{h} Jam {m} Menit" if h > 0 else (f"{m} Menit" if m > 0 else "0 Menit")

            status_label = {
                'di_meja': 'Di Meja Kerja',
                'keluar_ruangan': 'Keluar Ruangan',
                'belum_terdeteksi': 'Belum Terdeteksi'
            }.get(status, status)

            result.append({
                "pegawai_id": p.id,
                "nama": p.user.name if p.user else "-",
                "jabatan": p.jabatan or "-",
                "zona_label": zone.label if zone else "-",
                "zona_warna": zone.warna if zone else "#aaaaaa",
                "has_zone": zone is not None,
                "status_cctv": status_label,
                "status_raw": status,
                "durasi_stay_seconds": dur,            # raw seconds for live ticking
                "total_waktu": dur_str,
                "waktu_selesai": log.waktu_selesai.strftime("%H:%M") if log and log.waktu_selesai else "-"
            })

        return jsonify(result)
    finally:
        db.close()


# ─── EFEKTIVITAS KERJA PER HARI ──────────────────────────────────────────────
@app.route('/api/efektivitas/<int:pegawai_id>/<string:tanggal>', methods=['GET'])
def get_efektivitas(pegawai_id, tanggal):
    """
    Return work effectiveness stats for a specific employee on a specific date.
    tanggal format: YYYY-MM-DD
    """
    db = get_db()
    try:
        # Parse date
        try:
            target_date = datetime.date.fromisoformat(tanggal)
        except ValueError:
            return jsonify({"error": "Format tanggal tidak valid, gunakan YYYY-MM-DD"}), 400

        # Get employee info
        pegawai = db.query(PegawaiDetail).filter(PegawaiDetail.id == pegawai_id).first()
        if not pegawai:
            return jsonify({"error": "Pegawai tidak ditemukan"}), 404

        # Get CCTV monitoring log for that day
        log = db.query(CctvMonitoringLog).filter(
            CctvMonitoringLog.pegawai_id == pegawai_id,
            CctvMonitoringLog.tanggal == target_date
        ).first()

        # Get Kehadiran (check-in/out from Laravel)
        kehadiran = db.query(Kehadiran).filter(
            Kehadiran.pegawai_id == pegawai_id,
            Kehadiran.tanggal == target_date
        ).first()

        # Calculate effective work duration
        durasi_stay = log.durasi_stay if log else 0
        waktu_mulai = log.waktu_mulai if log else None
        waktu_selesai = log.waktu_selesai if log else None
        status_terakhir = log.status_terakhir if log else 'belum_terdeteksi'

        # Format duration
        h = durasi_stay // 3600
        m = (durasi_stay % 3600) // 60
        s = durasi_stay % 60
        if h > 0:
            dur_str = f"{h} Jam {m} Menit"
        elif m > 0:
            dur_str = f"{m} Menit {s} Detik"
        else:
            dur_str = f"{s} Detik" if s > 0 else "0 Menit"

        # Work schedule: assume 8 hours (28800 seconds) = standard workday
        STANDAR_KERJA_DETIK = 8 * 3600  # 8 jam
        efektivitas_pct = min(100, round((durasi_stay / STANDAR_KERJA_DETIK) * 100, 1)) if durasi_stay > 0 else 0

        # Check-in / out from Kehadiran
        check_in_str = kehadiran.check_in.strftime("%H:%M") if kehadiran and kehadiran.check_in else None
        check_out_str = kehadiran.check_out.strftime("%H:%M") if kehadiran and kehadiran.check_out else None

        # Calculate total presence duration (check_in to check_out)
        durasi_hadir_str = "-"
        if kehadiran and kehadiran.check_in and kehadiran.check_out:
            from datetime import datetime as dt, timedelta
            dummy = datetime.date(2000, 1, 1)
            t_in  = dt.combine(dummy, kehadiran.check_in)
            t_out = dt.combine(dummy, kehadiran.check_out)
            diff  = t_out - t_in
            if diff.total_seconds() > 0:
                total_h = int(diff.total_seconds() // 3600)
                total_m = int((diff.total_seconds() % 3600) // 60)
                durasi_hadir_str = f"{total_h} Jam {total_m} Menit"

        # Zone info
        zone = db.query(ZonaKamera).filter(ZonaKamera.pegawai_id == pegawai_id).first()

        status_label = {
            'di_meja': 'Di Meja Kerja',
            'keluar_ruangan': 'Keluar Ruangan',
            'belum_terdeteksi': 'Belum Terdeteksi'
        }.get(status_terakhir, status_terakhir)

        return jsonify({
            "pegawai_id": pegawai_id,
            "nama": pegawai.user.name if pegawai.user else "-",
            "jabatan": pegawai.jabatan or "-",
            "tanggal": tanggal,
            "tanggal_label": f"{target_date.day} {target_date.strftime('%B %Y')}",
            "check_in": check_in_str,
            "check_out": check_out_str,
            "durasi_hadir": durasi_hadir_str,
            "durasi_di_meja_detik": durasi_stay,
            "durasi_di_meja": dur_str,
            "efektivitas_persen": efektivitas_pct,
            "standar_jam_kerja": "8 Jam",
            "status_terakhir": status_label,
            "status_raw": status_terakhir,
            "zona_label": zone.label if zone else "-",
            "waktu_mulai_deteksi": waktu_mulai.strftime("%H:%M:%S") if waktu_mulai else None,
            "waktu_selesai_deteksi": waktu_selesai.strftime("%H:%M:%S") if waktu_selesai else None,
            "ai_detected": log is not None,
        })
    finally:
        db.close()


# ─── STOP MONITORING ─────────────────────────────────────────────────────────
@app.route('/api/stop-monitoring', methods=['POST'])
def stop_monitoring():
    """
    Called ONLY when the supervisor explicitly clicks 'Hentikan Pengawasan'.
    Stops the persistent background worker, resets CCTV status of all employees
    back to 'belum_terdeteksi', and clears last_seen so the live ticker freezes.
    """
    with _MONITOR_LOCK:
        _MONITOR_STATE["active"] = False
        _MONITOR_STATE["simulate"] = False
        _MONITOR_STATE["started_at"] = None
        _MONITOR_STATE["detected_count"] = 0
        _MONITOR_STATE["detections"] = []
        _MONITOR_STATE["annotated_image"] = None

    db = get_db()
    today = datetime.date.today()
    try:
        logs = db.query(CctvMonitoringLog).filter(
            CctvMonitoringLog.tanggal == today
        ).all()

        reset_count = 0
        for log in logs:
            if log.status_terakhir in ('di_meja', 'keluar_ruangan'):
                log.status_terakhir = 'belum_terdeteksi'
                log.last_seen = None      # stop the live ticker
                reset_count += 1

        reset_detection_state()
        db.commit()
        return jsonify({
            "success": True,
            "message": f"Pengawasan dihentikan. {reset_count} status pegawai direset.",
            "reset_count": reset_count
        })
    except Exception as e:
        db.rollback()
        return jsonify({"error": str(e)}), 500
    finally:
        db.close()


if __name__ == '__main__':
    port = int(os.getenv("PORT", 5000))
    app.run(host="0.0.0.0", port=port, debug=True)

