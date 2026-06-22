import os
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker, declarative_base
from dotenv import load_dotenv

# Read from Laravel's root .env
laravel_env_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../../.env'))
load_dotenv(laravel_env_path)

db_connection = os.getenv('DB_CONNECTION', 'mysql')
db_host = os.getenv('DB_HOST', '127.0.0.1')
db_port = os.getenv('DB_PORT', '3306')
db_database = os.getenv('DB_DATABASE', 'e_kinerja')
db_username = os.getenv('DB_USERNAME', 'root')
db_password = os.getenv('DB_PASSWORD', '')

import urllib.parse

# Construct Database URL
if db_connection == 'mysql':
    # pyMySQL is used as the driver
    safe_password = urllib.parse.quote_plus(db_password)
    DATABASE_URL = f"mysql+pymysql://{db_username}:{safe_password}@{db_host}:{db_port}/{db_database}"
else:
    # fallback to sqlite for development if needed
    DATABASE_URL = f"sqlite:///../database.sqlite"

engine = create_engine(DATABASE_URL, echo=False, pool_size=10, max_overflow=20)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
