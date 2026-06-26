# E-Kinerja — Employee Performance & Real-Time AI Monitoring System

A modern employee performance and attendance management web application integrated with **real-time AI CCTV surveillance** using YOLOv8 to monitor and track workspace presence.

---

## 🏗️ System Architecture

The project consists of two decoupled components that run concurrently:

| Component | Stack | Default Port | Description |
|---|---|---|---|
| **Web Portal (Core Application)** | Laravel 12 + Vite (Alpine.js) + TailwindCSS | `8000` | Administrative dashboard, attendance reports, user management, and video monitoring layout |
| **CV Backend (AI Engine)** | Python (Flask) + Ultralytics YOLOv8 + OpenCV | `5000` | Object detection pipeline, camera zone processing, live stream processing, and DB logs generator |

---

## 📋 System Requirements

Ensure the following environments and dependencies are installed on your host system:

| Dependency | Minimum Version | Recommended Version | Purpose |
|---|---|---|---|
| **PHP** | `>= 8.2` | `8.2.x` / `8.3.x` | Laravel Framework |
| **Composer** | `>= 2.0` | `2.6.x` | PHP package manager |
| **Node.js** | `>= 18.0` | `20.x (LTS)` | Frontend asset compilation |
| **npm** | `>= 9.0` | `10.x` | Node package manager |
| **Python** | `>= 3.10` | `3.10.x` / `3.11.x` | AI pipeline / Flask Backend |
| **MySQL / MariaDB** | `>= 8.0` | `8.0+` / `10.4+` | Relational database storage |

---

## 🛠️ Step-by-Step Installation

### Phase 1: Web Portal Setup (Laravel)

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd E-Kinerja
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Configure Environment Variables**
   Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
   Open the `.env` file and configure your database details:
   ```env
   APP_NAME="E-Kinerja"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=e_kinerja
   DB_USERNAME=root
   DB_PASSWORD=your_secure_password
   ```

4. **Initialize Database**
   Create a new database inside MySQL:
   ```sql
   CREATE DATABASE e_kinerja CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **Generate Application Encryption Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Database Migrations & Seeds**
   ```bash
   php artisan migrate --seed
   ```
   *Note: This command seeds the database with default accounts:*
   * **Admin Role:** `admin@gmail.com` | Password: `password`
   * **Employee Role:** `pegawai@gmail.com` | Password: `password`

7. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

---

### Phase 2: CV Backend Setup (Python)

The Python service manages video frame processing, zone mapping, and records CCTV presence logs directly into the shared database.

1. **Navigate to the Backend Directory**
   ```bash
   cd cv-backend
   ```

2. **Initialize a Virtual Environment**
   Using a virtual environment prevents version conflicts with your global python libraries:
   ```bash
   python3 -m venv venv
   ```

3. **Activate the Virtual Environment**
   * **Linux / macOS:**
     ```bash
     source venv/bin/activate
     ```
   * **Windows (PowerShell):**
     ```powershell
     venv\Scripts\Activate.ps1
     ```
   * **Windows (CMD):**
     ```cmd
     venv\Scripts\activate.bat
     ```

4. **Install Python Dependencies**
   ```bash
   pip install -r requirements.txt
   ```
   > ℹ️ **Note:** This installation processes PyTorch and YOLOv8 weights which may range up to **2 GB**. Ensure a stable internet connection.

5. **Run CCTV Table Migrations**
   Go back to the **project root directory** (or run from root) to execute database migrations for the Python service tables (`cctv_monitoring_logs`, `deteksi_aktivitas`, `zona_kameras`):
   ```bash
   # From project root:
   ./cv-backend/venv/bin/python cv-backend/migrations/run_migrations.py
   ```
   > ⚠️ **Warning:** If you refresh or reset your Laravel database (`php artisan migrate:fresh`), you **must** rerun this Python script to recreate the custom AI surveillance tables, as Laravel's native migrations do not manage them.

---

## 🚀 Running the Application

To run the complete workspace, execute the following commands in **three separate terminal sessions** from the project root:

### 📺 Terminal 1: Laravel Web Server
```bash
php artisan serve
```
*Served at `http://localhost:8000`*

### 🎨 Terminal 2: Vite Dev Server (Frontend Assets)
```bash
npm run dev
```
*Compiles resources in real-time*

### 🧠 Terminal 3: CV Backend (AI Microservice)
* **Linux / macOS:**
  ```bash
  ./cv-backend/venv/bin/python cv-backend/app.py
  ```
* **Windows:**
  ```cmd
  venv\Scripts\python cv-backend\app.py
  ```
*Served at `http://localhost:5000` (or the port defined in your environment)*

---

## 🔍 CCTV Monitoring Workflow

1. Authenticate as a user with the **Supervisor / Manager** (Atasan) role.
2. Navigate to the **Attendance Management** (Manajemen Kehadiran) section in the dashboard.
3. Access **Configure Zone** (Atur Zona) to draw and map specific seating/workspace regions for each employee within the camera frame.
4. Click **Start Camera** (Mulai Kamera) to initiate the live stream feed and trigger real-time presence verification.

> 📝 **Note:** The AI CCTV system is **read-only** regarding official check-in/out records. It populates tracking logs (`cctv_monitoring_logs`) for active office hours presence tracking, but does **not** overwrite official check-in/check-out events.

---

## 🛠️ Troubleshooting

| Issue / Error | Root Cause | Solution |
|---|---|---|
| **Camera Feed displays a Black Screen** | The Flask service is inactive, or unreachable on Port `5000`. | Verify the Python backend is running. Check browser dev tools (F12) for CORS or connection errors. |
| **Python Database Connection Failures** | Incorrect credential configurations. | The CV Backend reads the root Laravel `.env` automatically. Verify the `DB_*` keys are correct. |
| **Table `cctv_monitoring_logs` missing error** | Tables were dropped or not initialized after Laravel migrations. | Run `./cv-backend/venv/bin/python cv-backend/migrations/run_migrations.py`. |
| **`AttributeError: 'Conv' object has no attribute 'bn'`** | PyTorch version compatibility discrepancy with newer Python runtimes (e.g., Python 3.14+). | Handled by a patch in `ultralytics/nn/tasks.py`. Use the recommended Python version (`3.10.x` or `3.11.x`). |
| **Port `5000` already in use** | A local application (e.g. macOS AirPlay Receiver) is using port 5000. | Start Flask with a different port: `PORT=5001 ./cv-backend/venv/bin/python cv-backend/app.py` |

---

## 📄 License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).
