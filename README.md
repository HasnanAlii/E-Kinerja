# E-Kinerja — Sistem Manajemen Kinerja Pegawai

Aplikasi manajemen kinerja dan kehadiran pegawai berbasis web, dilengkapi dengan fitur **pengawasan CCTV AI real-time** menggunakan YOLOv8 untuk monitoring kehadiran di ruang kerja.

---

## Arsitektur Sistem

Sistem ini terdiri dari **dua komponen** yang harus berjalan bersamaan:

| Komponen | Teknologi | Port |
|---|---|---|
| Aplikasi Web Utama | Laravel 12 + Vite (Alpine.js) | `8000` |
| Layanan AI Monitoring CCTV | Python Flask + YOLOv8 | `5000` |

---

## Prasyarat

Pastikan semua software berikut sudah terinstal:

| Software | Versi Minimum |
|---|---|
| PHP | `>= 8.2` |
| Composer | `>= 2.x` |
| Node.js | `>= 18.x` |
| npm | `>= 9.x` |
| Python | `>= 3.10` |
| MySQL | `>= 8.0` |

---

## Instalasi

### 1. Clone Repositori

```bash
git clone <url-repository>
cd E-Kinerja
```

### 2. Install Dependensi PHP

```bash
composer install
```

### 3. Buat & Konfigurasi File Environment

```bash
cp .env.example .env
```

Edit file `.env`, sesuaikan konfigurasi database:

```env
APP_NAME="E-Kinerja"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_kinerja
DB_USERNAME=root
DB_PASSWORD=           # isi password MySQL Anda
```

Buat database di MySQL terlebih dahulu:

```sql
CREATE DATABASE e_kinerja CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Migrasi & Seeder Database (Laravel)

```bash
php artisan migrate --seed
```

Seeder akan membuat akun default berikut:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@gmail.com` | `password` |
| Pegawai | `pegawai@gmail.com` | `password` |

### 6. Install Dependensi Frontend

```bash
npm install
```

---

## Setup CV Backend (Python)

### 1. Buat Virtual Environment

```bash
cd cv-backend
python3 -m venv venv
```

### 2. Aktifkan Virtual Environment

```bash
# Linux / macOS
source venv/bin/activate

# Windows
venv\Scripts\activate
```

### 3. Install Dependensi Python

```bash
pip install -r requirements.txt
```

> **Catatan:** Proses ini akan mengunduh PyTorch dan Ultralytics YOLOv8 (~2 GB). Pastikan koneksi internet stabil.

### 4. Jalankan Migrasi Tabel CCTV

Setelah `php artisan migrate --seed` selesai, jalankan skrip ini dari **root direktori proyek**:

```bash
./cv-backend/venv/bin/python cv-backend/migrations/run_migrations.py
```

> ⚠️ **Penting:** Langkah ini **harus diulangi** setiap kali Anda menjalankan `php artisan migrate:fresh`, karena tabel CCTV (`cctv_monitoring_logs`, `deteksi_aktivitas`, `zona_kameras`) dikelola oleh skrip Python ini — bukan oleh migrasi Laravel.

---

## Menjalankan Aplikasi

Jalankan ketiga perintah berikut di **terminal terpisah** secara bersamaan:

**Terminal 1 — Laravel:**
```bash
php artisan serve
```

**Terminal 2 — Frontend (Vite):**
```bash
npm run dev
```

**Terminal 3 — CV Backend (Python):**
```bash
./cv-backend/venv/bin/python cv-backend/app.py
```

Akses aplikasi di `http://localhost:8000`

---

## Fitur Monitoring CCTV

1. Login sebagai **Atasan**
2. Buka menu **Manajemen Kehadiran**
3. Klik **Atur Zona** untuk mendefinisikan posisi duduk setiap pegawai di frame kamera
4. Aktifkan **Mulai Kamera** untuk memulai pengawasan AI secara live

> **Catatan:** Sistem monitoring CCTV bersifat **read-only** terhadap data absensi resmi. Kamera hanya mencatat log deteksi (`cctv_monitoring_logs`) dan **tidak mengubah** data absensi pegawai (check-in / check-out).

---

## Troubleshooting

| Masalah | Solusi |
|---|---|
| Layar kamera hitam | Pastikan CV Backend (`app.py`) sedang berjalan di port `5000` |
| Error koneksi DB pada Python | CV Backend membaca `.env` Laravel secara otomatis — periksa konfigurasi `DB_*` di `.env` |
| Tabel `cctv_monitoring_logs` tidak ditemukan | Jalankan ulang `./cv-backend/venv/bin/python cv-backend/migrations/run_migrations.py` |
| Error `AttributeError: 'Conv' object has no attribute 'bn'` | Error kompatibilitas Python 3.14+ dengan PyTorch — sudah ditangani oleh patch di `ultralytics/nn/tasks.py` |
| Port 5000 sudah digunakan | Jalankan dengan `PORT=5001 ./cv-backend/venv/bin/python cv-backend/app.py` |

---

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).
