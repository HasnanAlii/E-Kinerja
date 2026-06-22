import os
import cv2
import datetime
import numpy as np
from ultralytics import YOLO
from sqlalchemy.orm import Session
from models.models import CctvMonitoringLog, DeteksiAktivitas, Kehadiran, PegawaiDetail, ZonaKamera

# Initialize YOLOv8 (auto-download on first run)
model = YOLO('yolov8n.pt')

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


# ─── ZONE MATCHING ────────────────────────────────────────────────────────────
def match_zone(cx_pct, cy_pct, zones):
    """Return the ZonaKamera whose rectangle contains the point (cx_pct, cy_pct)."""
    for z in zones:
        if z.x1_pct <= cx_pct <= z.x2_pct and z.y1_pct <= cy_pct <= z.y2_pct:
            return z
    return None


# ─── DRAW ZONES ON FRAME ──────────────────────────────────────────────────────
def draw_zones(frame, zones, pegawai_map, active_ids=None):
    """
    Draw semi-transparent colored rectangles for every configured zone.
    active_ids: set of pegawai_ids currently detected → show pulsing border.
    """
    h, w = frame.shape[:2]
    overlay = frame.copy()
    active_ids = active_ids or set()

    for i, z in enumerate(zones):
        x1 = int(z.x1_pct / 100 * w)
        y1 = int(z.y1_pct / 100 * h)
        x2 = int(z.x2_pct / 100 * w)
        y2 = int(z.y2_pct / 100 * h)
        color = get_bgr(i)
        pegawai = pegawai_map.get(z.pegawai_id)
        name = pegawai.user.name.split()[0] if pegawai and pegawai.user else f"Pegawai {i+1}"
        label = z.label or f"Meja {chr(65+i)}"

        # Semi-transparent fill
        cv2.rectangle(overlay, (x1, y1), (x2, y2), color, -1)

        # Zone border — thicker if active
        thickness = 3 if z.pegawai_id in active_ids else 1
        cv2.rectangle(frame, (x1, y1), (x2, y2), color, thickness)

        # Zone label bar at top
        bar_h = 30
        cv2.rectangle(frame, (x1, y1), (x2, y1 + bar_h), color, -1)
        cv2.putText(frame, f"{label}: {name}", (x1 + 6, y1 + 20),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.55, (255, 255, 255), 2)

    # Blend overlay for transparency
    cv2.addWeighted(overlay, 0.12, frame, 0.88, 0, frame)
    return frame


# ─── UPDATE DB HELPER ─────────────────────────────────────────────────────────
def _update_monitoring(db, pegawai_id, present, current_time, today):
    cctv_log = db.query(CctvMonitoringLog).filter(
        CctvMonitoringLog.pegawai_id == pegawai_id,
        CctvMonitoringLog.tanggal == today
    ).first()

    if present:
        if not cctv_log:
            cctv_log = CctvMonitoringLog(
                pegawai_id=pegawai_id, tanggal=today,
                waktu_mulai=current_time, waktu_selesai=current_time,
                durasi_stay=0, status_terakhir='di_meja', last_seen=current_time
            )
            db.add(cctv_log)
            db.flush()
        else:
            td = (current_time - cctv_log.last_seen).total_seconds()
            if td < 60:   # only update if not a long gap (reconnect scenario)
                # Calculate total duration from session start — exact and drift-free
                total_secs = int((current_time - cctv_log.waktu_mulai).total_seconds())
                cctv_log.durasi_stay = max(cctv_log.durasi_stay, total_secs)
            cctv_log.status_terakhir = 'di_meja'
            cctv_log.waktu_selesai = current_time
            cctv_log.last_seen = current_time


    else:
        if cctv_log:
            td = (current_time - cctv_log.last_seen).total_seconds()
            if td > 10:
                cctv_log.status_terakhir = 'keluar_ruangan'


# ─── MAIN: ZONE-BASED DETECTION ──────────────────────────────────────────────
def process_frame_zone(db: Session, frame):
    """
    Zone-based employee recognition:
    1. Load all configured zones from DB.
    2. Run YOLOv8 person detection on the frame.
    3. For each detected bounding box, check which zone its center falls in.
    4. Assign the matched pegawai and update DB accordingly.
    5. Draw all zone overlays + detection boxes on the frame.
    """
    current_time = datetime.datetime.now()
    today = datetime.date.today()
    h, w = frame.shape[:2]

    # Load zones + employees
    zones = db.query(ZonaKamera).all()
    all_pegawai = db.query(PegawaiDetail).all()
    pegawai_map = {p.id: p for p in all_pegawai}

    # Track which pegawai are detected this frame
    detected_pegawai_ids = set()
    detections = []

    # Draw zone backgrounds first
    frame = draw_zones(frame, zones, pegawai_map)

    # Run YOLOv8
    results = model.track(source=frame, persist=True, classes=[0], verbose=False)

    if results and len(results) > 0:
        boxes = results[0].boxes or []

        for box in boxes:
            track_id = int(box.id[0].item()) if box.id is not None else 0
            conf = float(box.conf[0].item()) if box.conf is not None else 0.0
            x1, y1, x2, y2 = [int(v) for v in box.xyxy[0].tolist()]

            # Center as percentage
            cx_pct = ((x1 + x2) / 2 / w) * 100
            cy_pct = ((y1 + y2) / 2 / h) * 100

            matched_zone = match_zone(cx_pct, cy_pct, zones)

            if matched_zone:
                pegawai_id = matched_zone.pegawai_id
                pegawai = pegawai_map.get(pegawai_id)
                name = pegawai.user.name if pegawai and pegawai.user else "Pegawai"
                zone_idx = zones.index(matched_zone)
                color = get_bgr(zone_idx)
                label_text = name
                detected_pegawai_ids.add(pegawai_id)

                # Log detection event
                db.add(DeteksiAktivitas(
                    pegawai_id=pegawai_id, waktu_deteksi=current_time,
                    hasil_deteksi=f"{name} terdeteksi di zona {matched_zone.label or 'meja'}",
                    confidence=conf
                ))
            else:
                # Person detected but outside any zone
                color = (180, 180, 180)
                label_text = f"Tidak dikenal #{track_id}"

            # Draw detection box
            cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
            label = f"{label_text} ({conf:.0%})"
            (lw, lh), _ = cv2.getTextSize(label, cv2.FONT_HERSHEY_SIMPLEX, 0.55, 2)
            cv2.rectangle(frame, (x1, y2 - lh - 10), (x1 + lw + 8, y2), color, -1)
            cv2.putText(frame, label, (x1 + 4, y2 - 5),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.55, (0, 0, 0), 2)

            detections.append({
                "track_id": track_id,
                "pegawai_id": matched_zone.pegawai_id if matched_zone else None,
                "pegawai_name": label_text,
                "confidence": conf,
                "zone_matched": matched_zone.label if matched_zone else None
            })

    # Re-draw zone borders with active highlighting
    frame = draw_zones(frame.copy(), zones, pegawai_map, detected_pegawai_ids)

    # Update DB for ALL employees
    zone_pegawai_ids = {z.pegawai_id for z in zones}
    for pegawai_id in zone_pegawai_ids:
        present = pegawai_id in detected_pegawai_ids
        _update_monitoring(db, pegawai_id, present, current_time, today)

    # Employees with NO zone configured → mark status unknown
    # (Don't change their status — leave as-is)

    db.commit()
    return frame, detections, len(detected_pegawai_ids)


# ─── SIMULATION MODE ─────────────────────────────────────────────────────────
def process_frame_zone_simulated(db: Session, frame):
    """
    Simulation: draws zones and simulates presence for each configured pegawai.
    Alternates one employee as 'absent' every 8 seconds to show dynamic behavior.
    """
    current_time = datetime.datetime.now()
    today = datetime.date.today()
    h, w = frame.shape[:2]

    zones = db.query(ZonaKamera).all()
    all_pegawai = db.query(PegawaiDetail).all()
    pegawai_map = {p.id: p for p in all_pegawai}
    detections = []

    if not zones:
        # No zones configured — draw instructions
        cv2.putText(frame, "Belum ada zona dikonfigurasi.", (30, h // 2 - 20),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.8, (255, 255, 255), 2)
        cv2.putText(frame, "Klik 'Atur Zona' untuk menandai posisi duduk.", (30, h // 2 + 20),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.7, (150, 150, 150), 1)
        db.commit()
        return frame, [], 0

    n = len(zones)
    absent_slot = (int(current_time.second / 8)) % n if n > 1 else -1
    detected_pegawai_ids = set()

    # Draw zone backgrounds
    frame = draw_zones(frame, zones, pegawai_map)

    for i, zone in enumerate(zones):
        pegawai = pegawai_map.get(zone.pegawai_id)
        name = pegawai.user.name if pegawai and pegawai.user else f"Pegawai {i+1}"
        first_name = name.split()[0]
        color = get_bgr(i)
        present = (i != absent_slot)

        # Zone pixel coords
        x1 = int(zone.x1_pct / 100 * w)
        y1 = int(zone.y1_pct / 100 * h)
        x2 = int(zone.x2_pct / 100 * w)
        y2 = int(zone.y2_pct / 100 * h)
        cx = (x1 + x2) // 2
        cy = (y1 + y2) // 2

        if present:
            # Draw simulated person silhouette in zone
            head_r = max(18, min(30, (x2-x1)//6))
            body_w = head_r + 8
            body_h = int((y2-y1) * 0.45)

            # head
            cv2.circle(frame, (cx, cy - head_r - 5), head_r, color, -1)
            # body
            cv2.rectangle(frame, (cx - body_w, cy - 5), (cx + body_w, cy + body_h), color, -1)

            # Detection box around silhouette
            det_x1 = cx - body_w - 10
            det_y1 = cy - (2 * head_r) - 10
            det_x2 = cx + body_w + 10
            det_y2 = cy + body_h + 10
            cv2.rectangle(frame, (det_x1, det_y1), (det_x2, det_y2), color, 2)

            # Name tag below box
            tag = f"{first_name} ✓ (99%)"
            (tw, th), _ = cv2.getTextSize(tag, cv2.FONT_HERSHEY_SIMPLEX, 0.55, 2)
            cv2.rectangle(frame, (det_x1, det_y2), (det_x1 + tw + 8, det_y2 + th + 10), color, -1)
            cv2.putText(frame, tag, (det_x1 + 4, det_y2 + th + 4),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.55, (0, 0, 0), 2)

            detected_pegawai_ids.add(zone.pegawai_id)
            detections.append({
                "track_id": i + 1, "pegawai_id": zone.pegawai_id,
                "pegawai_name": first_name, "confidence": 0.99,
                "zone_matched": zone.label or f"Meja {chr(65+i)}"
            })
        else:
            # Show "empty seat" marker
            cv2.putText(frame, f"{first_name}: keluar", (x1 + 10, cy),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.6, (100, 100, 100), 1)
            detections.append({
                "track_id": i + 1, "pegawai_id": zone.pegawai_id,
                "pegawai_name": first_name, "confidence": 0.0,
                "zone_matched": None
            })

        _update_monitoring(db, zone.pegawai_id, present, current_time, today)
        db.add(DeteksiAktivitas(
            pegawai_id=zone.pegawai_id, waktu_deteksi=current_time,
            hasil_deteksi=f"{'Terdeteksi di zona' if present else 'Keluar zona'} {zone.label or ''} [SIM]",
            confidence=0.99 if present else 0.0
        ))

    db.commit()
    return frame, detections, len(detected_pegawai_ids)
