import os, sys, base64, datetime
import numpy as np, cv2
from flask import Flask, request, jsonify
from flask_cors import CORS

sys.path.append(os.path.abspath(os.path.dirname(__file__)))

from config.database import SessionLocal, engine
from models.models import Base, User, PegawaiDetail, Kehadiran, CctvMonitoringLog, DeteksiAktivitas, ZonaKamera
from ai.vision import process_frame_zone, process_frame_zone_simulated, SLOT_COLORS_HEX

# Create tables on startup
Base.metadata.create_all(bind=engine)

app = Flask(__name__)
CORS(app)

def get_db():
    return SessionLocal()


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

        _, buf = cv2.imencode('.jpg', annotated, [cv2.IMWRITE_JPEG_QUALITY, 85])
        b64_out = base64.b64encode(buf).decode('utf-8')

        return jsonify({
            "success": True,
            "detected_count": count,
            "detections": detections,
            "annotated_image": f"data:image/jpeg;base64,{b64_out}"
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


if __name__ == '__main__':
    port = int(os.getenv("PORT", 5000))
    app.run(host="0.0.0.0", port=port, debug=True)
