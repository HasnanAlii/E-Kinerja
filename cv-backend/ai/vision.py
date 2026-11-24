import os
import json
import base64
import time
import cv2
import datetime
import numpy as np
from ultralytics import YOLO
from sqlalchemy.orm import Session, joinedload
from models.models import (
    CctvMonitoringLog,
    DeteksiAktivitas,
    Kehadiran,
    PegawaiDetail,
    ZonaKamera,
    ZonaAktivasiHarian,
)

# Initialize YOLOv8 (auto-download on first run)
model = YOLO('yolov8n.pt')

# ─── DETECTION & SMOOTHING PARAMETERS ─────────────────────────────────────────
CONF_THRESHOLD = 0.50       # Minimum YOLO confidence to accept as a real person
IOU_THRESHOLD = 0.45        # NMS IoU threshold
MIN_BOX_AREA_PCT = 1.2      # Minimum bounding box area (% of frame) to reject tiny noise
MAX_BOX_AREA_PCT = 88.0     # Maximum bounding box area (% of frame) to reject full-screen glitches
MIN_ASPECT_RATIO = 0.30     # Minimum height / width ratio for a human body
MAX_ASPECT_RATIO = 4.20     # Maximum height / width ratio for a human body
MIN_ZONE_OVERLAP = 0.20     # Minimum fraction of person box overlapping the zone (or center inside)
CONFIRM_STREAK = 2          # Consecutive positive frames required before marking 'di_meja'
APPEARANCE_SIM_THRESHOLD = 0.58  # Minimum visual feature similarity to match the employee's daily signature

# In-memory state for performance caching & temporal smoothing
_STREAK_COUNTER = {}        # { pegawai_id: consecutive_frames_detected }
_LAST_ACTIVITY_LOG = {}     # { pegawai_id: timestamp_float }
_CACHE = {
    "ts": 0.0,
    "zones": [],
    "pegawai_map": {}
}

def invalidate_zone_cache():
    """Call when zones are added/updated/deleted so next frame reloads immediately."""
    _CACHE["ts"] = 0.0

def reset_detection_state():
    """Call when monitoring stops so streaks reset cleanly."""
    _STREAK_COUNTER.clear()


# ─── COLORS ──────────────────────────────────────────────────────────────────
SLOT_COLORS_BGR = [
    (46, 204, 113),   # green
    (52, 152, 219),   # blue
    (60,  76, 231),   # red (BGR)
    (15, 196, 241),   # yellow
    (182, 89, 155),   # purple
    (156, 188,  26),  # teal
    (34, 126, 230),   # orange
]

SLOT_COLORS_HEX = [
    '#2ECC71', '#3498DB', '#E74C3C', '#F1C40F',
    '#9B59B6', '#1ABC9C', '#E67E22',
]

def get_bgr(idx):  return SLOT_COLORS_BGR[idx % len(SLOT_COLORS_BGR)]
def get_hex(idx):  return SLOT_COLORS_HEX[idx % len(SLOT_COLORS_HEX)]


# ─── DAILY APPEARANCE FEATURE EXTRACTION & MATCHING ──────────────────────────
def extract_person_features(frame, x1, y1, x2, y2):
    """
    Extract daily visual appearance features (ciri-ciri karyawan) when entering the zone:
    - Upper-body / clothing & head-shoulder HSV color histogram (normalized)
    - Lower-body / torso HSV color histogram
    - Mean BGR color of upper torso & body aspect ratio
    """
    h, w = frame.shape[:2]
    cx1, cy1 = max(0, int(x1)), max(0, int(y1))
    cx2, cy2 = min(w, int(x2)), min(h, int(y2))
    crop = frame[cy1:cy2, cx1:cx2]
    if crop.size == 0 or crop.shape[0] < 12 or crop.shape[1] < 12:
        return None

    ch, cw = crop.shape[:2]
    # Focus on central 70% horizontally to exclude background around the person
    mx1 = int(cw * 0.15)
    mx2 = max(mx1 + 4, int(cw * 0.85))

    # Upper region (head + shoulders + shirt: 10% to 55% height)
    uy1, uy2 = int(ch * 0.10), max(int(ch * 0.10) + 4, int(ch * 0.55))
    upper_roi = crop[uy1:uy2, mx1:mx2]

    # Mid-lower region (55% to 90% height)
    ly1, ly2 = int(ch * 0.50), max(int(ch * 0.50) + 4, int(ch * 0.90))
    lower_roi = crop[ly1:ly2, mx1:mx2]

    if upper_roi.size == 0:
        upper_roi = crop
    if lower_roi.size == 0:
        lower_roi = crop

    hsv_upper = cv2.cvtColor(upper_roi, cv2.COLOR_BGR2HSV)
    hsv_lower = cv2.cvtColor(lower_roi, cv2.COLOR_BGR2HSV)

    # 8x4 Hue-Saturation histogram + 4 Value bins for upper and lower body
    hist_u_hs = cv2.calcHist([hsv_upper], [0, 1], None, [8, 4], [0, 180, 0, 256]).flatten()
    hist_u_v = cv2.calcHist([hsv_upper], [2], None, [4], [0, 256]).flatten()
    hist_l_hs = cv2.calcHist([hsv_lower], [0, 1], None, [8, 4], [0, 180, 0, 256]).flatten()

    vec = np.concatenate([hist_u_hs * 1.4, hist_u_v * 0.8, hist_l_hs * 1.0]).astype(np.float32)
    norm = float(np.linalg.norm(vec))
    if norm > 1e-6:
        vec = vec / norm

    mean_bgr = [round(float(v), 1) for v in upper_roi.mean(axis=(0, 1)).tolist()]
    aspect = round(float(ch) / max(1.0, float(cw)), 3)

    return {
        "hist": [round(float(v), 5) for v in vec.tolist()],
        "mean_bgr": mean_bgr,
        "aspect": aspect,
        "captured_at": datetime.datetime.now().strftime("%H:%M:%S"),
    }


def make_person_thumbnail_b64(frame, x1, y1, x2, y2):
    """Create a compact base64 JPEG thumbnail of the detected employee on first zone entry."""
    h, w = frame.shape[:2]
    cx1, cy1 = max(0, int(x1)), max(0, int(y1))
    cx2, cy2 = min(w, int(x2)), min(h, int(y2))
    crop = frame[cy1:cy2, cx1:cx2]
    if crop.size == 0:
        return None
    thumb = cv2.resize(crop, (96, 120), interpolation=cv2.INTER_AREA)
    ok, buf = cv2.imencode('.jpg', thumb, [cv2.IMWRITE_JPEG_QUALITY, 75])
    if not ok:
        return None
    return f"data:image/jpeg;base64,{base64.b64encode(buf).decode('utf-8')}"


def compare_person_features(saved_json_str, current_feat):
    """
    Compare stored daily visual signature against currently detected person in the zone.
    Returns similarity score in [0.0, 1.0].
    """
    if not saved_json_str or not current_feat:
        return 1.0
    try:
        saved = json.loads(saved_json_str) if isinstance(saved_json_str, str) else saved_json_str
        h1 = np.array(saved.get("hist", []), dtype=np.float32)
        h2 = np.array(current_feat.get("hist", []), dtype=np.float32)
        if h1.size == 0 or h1.size != h2.size:
            return 1.0
        n1 = float(np.linalg.norm(h1))
        n2 = float(np.linalg.norm(h2))
        if n1 < 1e-6 or n2 < 1e-6:
            return 1.0
        cos_sim = float(np.dot(h1 / n1, h2 / n2))
        return max(0.0, min(1.0, cos_sim))
    except Exception:
        return 1.0


# ─── GEOMETRY & FALSE-POSITIVE FILTERING ──────────────────────────────────────
def is_valid_person_box(x1, y1, x2, y2, frame_w, frame_h, conf):
    """
    Filter out false detections on empty chairs, monitors, shadows, or wall posters.
    Checks confidence, relative area, and human body aspect ratio.
    """
    if conf < CONF_THRESHOLD:
        return False

    bw = max(1, x2 - x1)
    bh = max(1, y2 - y1)
    frame_area = max(1, frame_w * frame_h)
    box_area_pct = (bw * bh / frame_area) * 100.0

    if box_area_pct < MIN_BOX_AREA_PCT or box_area_pct > MAX_BOX_AREA_PCT:
        return False

    aspect_ratio = bh / float(bw)
    if aspect_ratio < MIN_ASPECT_RATIO or aspect_ratio > MAX_ASPECT_RATIO:
        return False

    return True


def box_zone_overlap_ratio(bx1_pct, by1_pct, bx2_pct, by2_pct, z):
    """
    Calculate what fraction of the person's bounding box lies inside zone `z`
    (intersection area / person box area), plus check if upper-body/center is inside.
    """
    ix1 = max(bx1_pct, z.x1_pct)
    iy1 = max(by1_pct, z.y1_pct)
    ix2 = min(bx2_pct, z.x2_pct)
    iy2 = min(by2_pct, z.y2_pct)

    if ix2 <= ix1 or iy2 <= iy1:
        return 0.0

    inter_area = (ix2 - ix1) * (iy2 - iy1)
    box_area = max(0.001, (bx2_pct - bx1_pct) * (by2_pct - by1_pct))
    zone_area = max(0.001, (z.x2_pct - z.x1_pct) * (z.y2_pct - z.y1_pct))

    # Ratio relative to whichever is smaller (handles big zone vs small person or vice versa)
    return inter_area / min(box_area, zone_area)


def match_zone_box(bx1_pct, by1_pct, bx2_pct, by2_pct, zones):
    """
    Match a detected person box (in % coordinates) to the best configured zone.
    Requires either the person's torso center to be inside the zone OR significant
    bounding-box overlap (>= MIN_ZONE_OVERLAP).
    """
    cx_pct = (bx1_pct + bx2_pct) / 2.0
    # Torso point (slightly above vertical center is more reliable for seated workers)
    cy_torso_pct = by1_pct + (by2_pct - by1_pct) * 0.45
    cy_center_pct = (by1_pct + by2_pct) / 2.0

    best_zone = None
    best_score = 0.0

    for z in zones:
        overlap = box_zone_overlap_ratio(bx1_pct, by1_pct, bx2_pct, by2_pct, z)
        center_in = (
            z.x1_pct <= cx_pct <= z.x2_pct and
            (z.y1_pct <= cy_center_pct <= z.y2_pct or z.y1_pct <= cy_torso_pct <= z.y2_pct)
        )
        if center_in and overlap >= 0.12:
            score = overlap + 0.5
        elif overlap >= MIN_ZONE_OVERLAP:
            score = overlap
        else:
            score = 0.0

        if score > best_score:
            best_score = score
            best_zone = z

    return best_zone


# ─── DRAW ZONES ON FRAME ──────────────────────────────────────────────────────
def draw_zones(frame, zones, pegawai_map, active_ids=None, aktivasi_map=None):
    """
    Draw semi-transparent colored rectangles for every configured zone.
    If zone is NOT activated via today's referral code, draw it in muted grey with [DISABLE].
    """
    h, w = frame.shape[:2]
    overlay = frame.copy()
    active_ids = active_ids or set()
    aktivasi_map = aktivasi_map or {}

    for i, z in enumerate(zones):
        x1 = int(z.x1_pct / 100 * w)
        y1 = int(z.y1_pct / 100 * h)
        x2 = int(z.x2_pct / 100 * w)
        y2 = int(z.y2_pct / 100 * h)
        akt = aktivasi_map.get(z.pegawai_id)
        is_zone_enabled = bool(akt and akt.is_active)

        color = get_bgr(i) if is_zone_enabled else (110, 110, 110)
        pegawai = pegawai_map.get(z.pegawai_id)
        name = pegawai.user.name.split()[0] if pegawai and pegawai.user else f"Pegawai {i+1}"
        label = z.label or f"Meja {chr(65+i)}"

        # Semi-transparent fill
        cv2.rectangle(overlay, (x1, y1), (x2, y2), color, -1)

        # Zone border — thicker if active
        thickness = 3 if (is_zone_enabled and z.pegawai_id in active_ids) else 1
        cv2.rectangle(frame, (x1, y1), (x2, y2), color, thickness)

        # Zone label bar at top
        bar_h = 30
        base_label = label if name.lower() in label.lower() else f"{label}: {name}"
        if not is_zone_enabled:
            display_label = f"{base_label} [DISABLE]"
        elif akt and akt.ciri_visual:
            display_label = f"{base_label} [TERKUNCI]"
        else:
            display_label = base_label

        cv2.rectangle(frame, (x1, y1), (x2, y1 + bar_h), color, -1)
        cv2.putText(frame, display_label, (x1 + 6, y1 + 20),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.50, (255, 255, 255), 2)

    # Blend overlay for transparency
    cv2.addWeighted(overlay, 0.12, frame, 0.88, 0, frame)
    return frame


# ─── UPDATE DB HELPER ─────────────────────────────────────────────────────────
def _update_monitoring(db, pegawai_id, present, current_time, today, is_zone_enabled=True):
    # If employee has NOT activated their daily referral code, their zone is disabled
    # and they cannot be marked present.
    if not is_zone_enabled:
        return

    cctv_log = db.query(CctvMonitoringLog).filter(
        CctvMonitoringLog.pegawai_id == pegawai_id,
        CctvMonitoringLog.tanggal == today
    ).first()

    if present:
        # Kehadiran otomatis: check_in = pertama terdeteksi, check_out = terakhir terdeteksi
        keh = db.query(Kehadiran).filter(
            Kehadiran.pegawai_id == pegawai_id, Kehadiran.tanggal == today
        ).first()
        if not keh:
            db.add(Kehadiran(pegawai_id=pegawai_id, tanggal=today, jenis='hadir',
                             check_in=current_time.time(), check_out=current_time.time()))
        elif keh.jenis in (None, 'hadir', 'alpha'):
            keh.jenis = 'hadir'
            if keh.check_in is None:
                keh.check_in = current_time.time()
            keh.check_out = current_time.time()

        if not cctv_log:
            cctv_log = CctvMonitoringLog(
                pegawai_id=pegawai_id, tanggal=today,
                waktu_mulai=current_time, waktu_selesai=current_time,
                durasi_stay=0, status_terakhir='di_meja', last_seen=current_time
            )
            db.add(cctv_log)
            db.flush()
        else:
            # Guard: last_seen bisa None setelah stop-monitoring mereset sesi
            if cctv_log.last_seen is None:
                prev_dur = cctv_log.durasi_stay or 0
                cctv_log.waktu_mulai = current_time - datetime.timedelta(seconds=prev_dur)
            else:
                td = (current_time - cctv_log.last_seen).total_seconds()
                if td < 60:   # only update if not a long gap (reconnect scenario)
                    total_secs = int((current_time - cctv_log.waktu_mulai).total_seconds())
                    cctv_log.durasi_stay = max(cctv_log.durasi_stay, total_secs)
            cctv_log.status_terakhir = 'di_meja'
            cctv_log.waktu_selesai = current_time
            cctv_log.last_seen = current_time

    else:
        if cctv_log:
            if cctv_log.last_seen is None:
                cctv_log.status_terakhir = 'keluar_ruangan'
            else:
                td = (current_time - cctv_log.last_seen).total_seconds()
                if td > 10:
                    cctv_log.status_terakhir = 'keluar_ruangan'


# ─── MAIN: ZONE-BASED DETECTION ──────────────────────────────────────────────
def process_frame_zone(db: Session, frame):
    """
    Zone-based employee recognition with Daily Referral Code & First-Entry Visual Lock:
    1. Run YOLOv8 person detection on the CLEAN raw frame.
    2. Filter out false positives.
    3. Match valid person bounding boxes against configured zones.
    4. Check if employee's zone is activated for today (ZonaAktivasiHarian.is_active).
       - If NOT active: zone is disabled, attendance is NOT recorded.
       - If active & first time entering zone today: extract & save employee's visual features
         (ciri_visual + snapshot_wajah) for today.
       - If active & ciri_visual already saved today: verify detected person matches saved features
         so other people entering the zone are rejected.
    """
    current_time = datetime.datetime.now()
    now_ts = time.time()
    today = datetime.date.today()
    h, w = frame.shape[:2]

    # Load zones + employees + today's daily referral activations
    zones = db.query(ZonaKamera).all()
    all_pegawai = db.query(PegawaiDetail).options(joinedload(PegawaiDetail.user)).all()
    pegawai_map = {p.id: p for p in all_pegawai}
    zone_idx_map = {z.pegawai_id: idx for idx, z in enumerate(zones)}

    aktivasi_rows = db.query(ZonaAktivasiHarian).filter(
        ZonaAktivasiHarian.tanggal == today
    ).all()
    aktivasi_map = {a.pegawai_id: a for a in aktivasi_rows}

    raw_detected_pegawai_ids = set()
    first_entry_candidates = {}  # { pegawai_id: (x1, y1, x2, y2, feat) }
    detections = []
    boxes_to_draw = []

    # 1. Run YOLOv8 on CLEAN frame
    results = model.predict(
        source=frame,
        classes=[0],
        conf=CONF_THRESHOLD,
        iou=IOU_THRESHOLD,
        imgsz=480,
        verbose=False
    )

    if results and len(results) > 0:
        boxes = results[0].boxes or []

        for idx, box in enumerate(boxes):
            conf = float(box.conf[0].item()) if box.conf is not None else 0.0
            x1, y1, x2, y2 = [int(v) for v in box.xyxy[0].tolist()]

            if not is_valid_person_box(x1, y1, x2, y2, w, h, conf):
                continue

            bx1_pct = (x1 / w) * 100.0
            by1_pct = (y1 / h) * 100.0
            bx2_pct = (x2 / w) * 100.0
            by2_pct = (y2 / h) * 100.0

            matched_zone = match_zone_box(bx1_pct, by1_pct, bx2_pct, by2_pct, zones)

            if matched_zone:
                pegawai_id = matched_zone.pegawai_id
                pegawai = pegawai_map.get(pegawai_id)
                name = pegawai.user.name if pegawai and pegawai.user else "Pegawai"
                zone_idx = zone_idx_map.get(pegawai_id, 0)
                akt = aktivasi_map.get(pegawai_id)
                is_zone_enabled = bool(akt and akt.is_active)

                # Case A: Zone is DISABLED (referral code not entered today)
                if not is_zone_enabled:
                    color = (120, 120, 120)
                    label_text = f"{name} (Zona Disable)"
                    boxes_to_draw.append((x1, y1, x2, y2, color, label_text))
                    detections.append({
                        "track_id": idx + 1,
                        "pegawai_id": None,
                        "pegawai_name": label_text,
                        "confidence": round(conf, 2),
                        "zone_matched": matched_zone.label,
                        "zone_disabled": True,
                        "identity_verified": False,
                        "box_pct": {
                            "x1": round(bx1_pct, 1),
                            "y1": round(by1_pct, 1),
                            "x2": round(bx2_pct, 1),
                            "y2": round(by2_pct, 1)
                        }
                    })
                    continue

                # Case B: Zone is ACTIVE — extract visual appearance features
                current_feat = extract_person_features(frame, x1, y1, x2, y2)
                if akt.ciri_visual:
                    sim_score = compare_person_features(akt.ciri_visual, current_feat)
                    if sim_score < APPEARANCE_SIM_THRESHOLD:
                        # Different person detected in this employee's zone!
                        color = (0, 0, 230)  # Red warning
                        label_text = f"Bukan {name.split()[0]}! ({sim_score:.0%})"
                        boxes_to_draw.append((x1, y1, x2, y2, color, label_text))
                        detections.append({
                            "track_id": idx + 1,
                            "pegawai_id": None,
                            "pegawai_name": label_text,
                            "confidence": round(conf, 2),
                            "similarity": round(sim_score, 2),
                            "zone_matched": matched_zone.label,
                            "zone_disabled": False,
                            "identity_verified": False,
                            "box_pct": {
                                "x1": round(bx1_pct, 1),
                                "y1": round(by1_pct, 1),
                                "x2": round(bx2_pct, 1),
                                "y2": round(by2_pct, 1)
                            }
                        })
                        continue
                else:
                    sim_score = 1.0
                    if current_feat:
                        first_entry_candidates[pegawai_id] = (x1, y1, x2, y2, current_feat)

                # Validated employee in active zone
                color = get_bgr(zone_idx)
                label_text = name
                raw_detected_pegawai_ids.add(pegawai_id)

                last_log_ts = _LAST_ACTIVITY_LOG.get(pegawai_id, 0.0)
                if now_ts - last_log_ts >= 15.0:
                    _LAST_ACTIVITY_LOG[pegawai_id] = now_ts
                    db.add(DeteksiAktivitas(
                        pegawai_id=pegawai_id, waktu_deteksi=current_time,
                        hasil_deteksi=f"{name} terdeteksi di zona {matched_zone.label or 'meja'} (Cocok: {sim_score:.0%})",
                        confidence=conf
                    ))

                boxes_to_draw.append((x1, y1, x2, y2, color, f"{label_text} ({conf:.0%})"))
                detections.append({
                    "track_id": idx + 1,
                    "pegawai_id": pegawai_id,
                    "pegawai_name": label_text,
                    "confidence": round(conf, 2),
                    "similarity": round(sim_score, 2),
                    "zone_matched": matched_zone.label,
                    "zone_disabled": False,
                    "identity_verified": True,
                    "box_pct": {
                        "x1": round(bx1_pct, 1),
                        "y1": round(by1_pct, 1),
                        "x2": round(bx2_pct, 1),
                        "y2": round(by2_pct, 1)
                    }
                })
            else:
                if conf >= 0.65:
                    color = (160, 160, 160)
                    label_text = "Di Luar Zona"
                    boxes_to_draw.append((x1, y1, x2, y2, color, f"{label_text} ({conf:.0%})"))
                    detections.append({
                        "track_id": idx + 1,
                        "pegawai_id": None,
                        "pegawai_name": label_text,
                        "confidence": round(conf, 2),
                        "zone_matched": None,
                        "box_pct": {
                            "x1": round(bx1_pct, 1),
                            "y1": round(by1_pct, 1),
                            "x2": round(bx2_pct, 1),
                            "y2": round(by2_pct, 1)
                        }
                    })

    # 4. Temporal smoothing & first-entry visual feature locking
    confirmed_pegawai_ids = set()
    zone_pegawai_ids = {z.pegawai_id for z in zones}
    for pegawai_id in zone_pegawai_ids:
        akt = aktivasi_map.get(pegawai_id)
        is_zone_enabled = bool(akt and akt.is_active)

        if is_zone_enabled and pegawai_id in raw_detected_pegawai_ids:
            _STREAK_COUNTER[pegawai_id] = min(CONFIRM_STREAK + 2, _STREAK_COUNTER.get(pegawai_id, 0) + 1)
        else:
            _STREAK_COUNTER[pegawai_id] = 0

        is_confirmed = is_zone_enabled and (_STREAK_COUNTER[pegawai_id] >= CONFIRM_STREAK)
        if is_confirmed:
            confirmed_pegawai_ids.add(pegawai_id)
            # Lock daily visual features on first confirmed entry into the zone today
            if akt and not akt.ciri_visual and pegawai_id in first_entry_candidates:
                cx1, cy1, cx2, cy2, feat = first_entry_candidates[pegawai_id]
                akt.ciri_visual = json.dumps(feat)
                akt.snapshot_wajah = make_person_thumbnail_b64(frame, cx1, cy1, cx2, cy2)
                akt.first_entered_at = current_time

        _update_monitoring(db, pegawai_id, is_confirmed, current_time, today, is_zone_enabled=is_zone_enabled)

    # 5. Draw zones ONCE with active highlighting, then draw person boxes
    active_visual_ids = confirmed_pegawai_ids | raw_detected_pegawai_ids
    frame = draw_zones(frame, zones, pegawai_map, active_visual_ids, aktivasi_map)

    for (x1, y1, x2, y2, color, label) in boxes_to_draw:
        cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
        (lw, lh), _ = cv2.getTextSize(label, cv2.FONT_HERSHEY_SIMPLEX, 0.52, 2)
        bar_y1 = max(0, y2 - lh - 10)
        cv2.rectangle(frame, (x1, bar_y1), (x1 + lw + 8, y2), color, -1)
        cv2.putText(frame, label, (x1 + 4, max(lh, y2 - 5)),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.52, (0, 0, 0), 2)

    db.commit()
    return frame, detections, len(active_visual_ids)


# ─── SIMULATION MODE ─────────────────────────────────────────────────────────
def process_frame_zone_simulated(db: Session, frame):
    """
    Simulation: draws zones and simulates presence ONLY for employees whose
    daily referral code has been activated today. Also locks simulated visual
    features on first entry.
    """
    current_time = datetime.datetime.now()
    today = datetime.date.today()
    h, w = frame.shape[:2]

    zones = db.query(ZonaKamera).all()
    all_pegawai = db.query(PegawaiDetail).all()
    pegawai_map = {p.id: p for p in all_pegawai}
    aktivasi_rows = db.query(ZonaAktivasiHarian).filter(
        ZonaAktivasiHarian.tanggal == today
    ).all()
    aktivasi_map = {a.pegawai_id: a for a in aktivasi_rows}
    detections = []

    if not zones:
        cv2.putText(frame, "Belum ada zona dikonfigurasi.", (30, h // 2 - 20),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.8, (255, 255, 255), 2)
        cv2.putText(frame, "Klik 'Atur Zona' untuk menandai posisi duduk.", (30, h // 2 + 20),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.7, (150, 150, 150), 1)
        db.commit()
        return frame, [], 0

    n = len(zones)
    absent_slot = (int(current_time.second / 8)) % n if n > 1 else -1
    detected_pegawai_ids = set()

    frame = draw_zones(frame, zones, pegawai_map, set(), aktivasi_map)

    for i, zone in enumerate(zones):
        pegawai = pegawai_map.get(zone.pegawai_id)
        name = pegawai.user.name if pegawai and pegawai.user else f"Pegawai {i+1}"
        first_name = name.split()[0]
        akt = aktivasi_map.get(zone.pegawai_id)
        is_zone_enabled = bool(akt and akt.is_active)
        color = get_bgr(i) if is_zone_enabled else (110, 110, 110)

        x1 = int(zone.x1_pct / 100 * w)
        y1 = int(zone.y1_pct / 100 * h)
        x2 = int(zone.x2_pct / 100 * w)
        y2 = int(zone.y2_pct / 100 * h)
        cx = (x1 + x2) // 2
        cy = (y1 + y2) // 2

        if not is_zone_enabled:
            cv2.putText(frame, f"{first_name}: ZONA DISABLE", (x1 + 10, cy),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.55, (140, 140, 140), 2)
            detections.append({
                "track_id": i + 1, "pegawai_id": None,
                "pegawai_name": f"{first_name} (Zona Disable)", "confidence": 0.0,
                "zone_matched": zone.label or f"Meja {chr(65+i)}",
                "zone_disabled": True,
                "box_pct": None
            })
            continue

        present = (i != absent_slot)

        if present:
            head_r = max(18, min(30, (x2-x1)//6))
            body_w = head_r + 8
            body_h = int((y2-y1) * 0.45)

            cv2.circle(frame, (cx, cy - head_r - 5), head_r, color, -1)
            cv2.rectangle(frame, (cx - body_w, cy - 5), (cx + body_w, cy + body_h), color, -1)

            det_x1 = cx - body_w - 10
            det_y1 = cy - (2 * head_r) - 10
            det_x2 = cx + body_w + 10
            det_y2 = cy + body_h + 10
            cv2.rectangle(frame, (det_x1, det_y1), (det_x2, det_y2), color, 2)

            if akt and not akt.ciri_visual:
                feat = extract_person_features(frame, det_x1, det_y1, det_x2, det_y2)
                if feat:
                    akt.ciri_visual = json.dumps(feat)
                    akt.snapshot_wajah = make_person_thumbnail_b64(frame, det_x1, det_y1, det_x2, det_y2)
                    akt.first_entered_at = current_time

            tag = f"{first_name} [TERKUNCI] (99%)"
            (tw, th), _ = cv2.getTextSize(tag, cv2.FONT_HERSHEY_SIMPLEX, 0.50, 2)
            cv2.rectangle(frame, (det_x1, det_y2), (det_x1 + tw + 8, det_y2 + th + 10), color, -1)
            cv2.putText(frame, tag, (det_x1 + 4, det_y2 + th + 4),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.50, (0, 0, 0), 2)

            detected_pegawai_ids.add(zone.pegawai_id)
            detections.append({
                "track_id": i + 1, "pegawai_id": zone.pegawai_id,
                "pegawai_name": first_name, "confidence": 0.99,
                "zone_matched": zone.label or f"Meja {chr(65+i)}",
                "zone_disabled": False,
                "identity_verified": True,
                "box_pct": {
                    "x1": round(det_x1 / w * 100, 1),
                    "y1": round(det_y1 / h * 100, 1),
                    "x2": round(det_x2 / w * 100, 1),
                    "y2": round(det_y2 / h * 100, 1)
                }
            })
        else:
            cv2.putText(frame, f"{first_name}: keluar", (x1 + 10, cy),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.6, (100, 100, 100), 1)
            detections.append({
                "track_id": i + 1, "pegawai_id": zone.pegawai_id,
                "pegawai_name": first_name, "confidence": 0.0,
                "zone_matched": None,
                "zone_disabled": False,
                "box_pct": None
            })

        _update_monitoring(db, zone.pegawai_id, present, current_time, today, is_zone_enabled=True)
        last_sim_ts = _LAST_ACTIVITY_LOG.get(zone.pegawai_id, 0.0)
        now_sim_ts = time.time()
        if now_sim_ts - last_sim_ts >= 15.0:
            _LAST_ACTIVITY_LOG[zone.pegawai_id] = now_sim_ts
            db.add(DeteksiAktivitas(
                pegawai_id=zone.pegawai_id, waktu_deteksi=current_time,
                hasil_deteksi=f"{'Terdeteksi di zona' if present else 'Keluar zona'} {zone.label or ''} [SIM]",
                confidence=0.99 if present else 0.0
            ))

    db.commit()
    return frame, detections, len(detected_pegawai_ids)
