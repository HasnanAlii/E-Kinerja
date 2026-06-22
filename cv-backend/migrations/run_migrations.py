import os
import sys

# Add path
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from config.database import engine, Base
from models.models import CctvMonitoringLog, DeteksiAktivitas

def create_tables():
    print("[MIGRATION] Creating missing tables in MySQL...")
    try:
        # This will create tables that do not exist yet.
        Base.metadata.create_all(bind=engine)
        print("[MIGRATION] Table initialization successful!")
    except Exception as e:
        print(f"[MIGRATION] Error creating tables: {e}")

if __name__ == '__main__':
    create_tables()
