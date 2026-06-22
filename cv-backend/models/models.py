from sqlalchemy import Column, Integer, String, Date, Time, DateTime, ForeignKey, Float
from sqlalchemy.dialects.mysql import BIGINT
from sqlalchemy.orm import relationship
from config.database import Base
import datetime

class User(Base):
    __tablename__ = 'users'
    
    id = Column(BIGINT(unsigned=True), primary_key=True, index=True)
    name = Column(String(255), unique=True, nullable=False)
    email = Column(String(255), unique=True, nullable=False)
    password = Column(String(255), nullable=False)
    
    pegawai_detail = relationship("PegawaiDetail", back_populates="user", uselist=False)

class PegawaiDetail(Base):
    __tablename__ = 'pegawai_details'
    
    id = Column(BIGINT(unsigned=True), primary_key=True, index=True)
    user_id = Column(BIGINT(unsigned=True), ForeignKey('users.id', ondelete='CASCADE'), nullable=False)
    bidang_id = Column(BIGINT(unsigned=True), nullable=True)
    atasan_id = Column(BIGINT(unsigned=True), nullable=True)
    nip = Column(String(255), nullable=True)
    jabatan = Column(String(255), nullable=True)
    foto = Column(String(255), nullable=True)
    status = Column(String(255), nullable=True)
    tanggal_masuk = Column(Date, nullable=True)
    
    user = relationship("User", back_populates="pegawai_detail")
    kehadirans = relationship("Kehadiran", back_populates="pegawai")
    cctv_logs = relationship("CctvMonitoringLog", back_populates="pegawai")
    zona_kamera = relationship("ZonaKamera", back_populates="pegawai", uselist=False)

class Kehadiran(Base):
    __tablename__ = 'kehadirans'
    
    id = Column(BIGINT(unsigned=True), primary_key=True, index=True)
    pegawai_id = Column(BIGINT(unsigned=True), ForeignKey('pegawai_details.id', ondelete='CASCADE'), nullable=False)
    tanggal = Column(Date, nullable=False)
    jenis = Column(String(50), nullable=True) # 'hadir', 'izin', 'sakit', 'cuti', 'alpha'
    check_in = Column(Time, nullable=True)
    check_out = Column(Time, nullable=True)
    created_at = Column(DateTime, default=datetime.datetime.now)
    updated_at = Column(DateTime, default=datetime.datetime.now, onupdate=datetime.datetime.now)
    
    pegawai = relationship("PegawaiDetail", back_populates="kehadirans")

# --- NEW TABLES FOR CCTV MONITORING ---
class CctvMonitoringLog(Base):
    __tablename__ = 'cctv_monitoring_logs'
    
    id = Column(BIGINT(unsigned=True), primary_key=True, index=True)
    pegawai_id = Column(BIGINT(unsigned=True), ForeignKey('pegawai_details.id', ondelete='CASCADE'), nullable=False)
    tanggal = Column(Date, default=datetime.date.today, nullable=False)
    waktu_mulai = Column(DateTime, default=datetime.datetime.now, nullable=False)
    waktu_selesai = Column(DateTime, nullable=True)
    durasi_stay = Column(Integer, default=0) # in seconds
    status_terakhir = Column(String(50), default='keluar_ruangan') # 'di_meja', 'keluar_ruangan'
    last_seen = Column(DateTime, default=datetime.datetime.now)
    
    pegawai = relationship("PegawaiDetail", back_populates="cctv_logs")

class DeteksiAktivitas(Base):
    __tablename__ = 'deteksi_aktivitas'
    
    id = Column(BIGINT(unsigned=True), primary_key=True, index=True)
    pegawai_id = Column(BIGINT(unsigned=True), ForeignKey('pegawai_details.id', ondelete='CASCADE'), nullable=False)
    waktu_deteksi = Column(DateTime, default=datetime.datetime.now, nullable=False)
    hasil_deteksi = Column(String(255), nullable=False)
    confidence = Column(Float, nullable=False)


# --- ZONE-BASED SEAT RECOGNITION ---
class ZonaKamera(Base):
    """
    Stores each employee's desk zone as percentage (0-100) of the camera frame.
    Zone is resolution-independent: x1_pct=10, y1_pct=5, x2_pct=45, y2_pct=90
    means the zone spans 10%-45% horizontally and 5%-90% vertically.
    """
    __tablename__ = 'zona_kameras'

    id = Column(BIGINT(unsigned=True), primary_key=True, index=True)
    pegawai_id = Column(
        BIGINT(unsigned=True),
        ForeignKey('pegawai_details.id', ondelete='CASCADE'),
        nullable=False,
        unique=True  # one zone per employee
    )
    x1_pct = Column(Float, nullable=False, default=0.0)   # left boundary (0-100)
    y1_pct = Column(Float, nullable=False, default=0.0)   # top boundary (0-100)
    x2_pct = Column(Float, nullable=False, default=50.0)  # right boundary (0-100)
    y2_pct = Column(Float, nullable=False, default=100.0) # bottom boundary (0-100)
    label = Column(String(100), nullable=True)             # e.g., "Meja A"
    warna = Column(String(20), nullable=True, default='#2ECC71')  # hex color for UI
    updated_at = Column(DateTime, default=datetime.datetime.now, onupdate=datetime.datetime.now)

    pegawai = relationship("PegawaiDetail", back_populates="zona_kamera")
