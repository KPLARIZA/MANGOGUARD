from supabase import create_client, Client
from influxdb_client import InfluxDBClient
from app.core.config import settings
import logging

logger = logging.getLogger(__name__)

# Global database clients
supabase: Client = None
influxdb: InfluxDBClient = None


async def init_db():
    """Initialize database connections"""
    global supabase, influxdb
    
    try:
        # Initialize Supabase
        supabase = create_client(settings.SUPABASE_URL, settings.SUPABASE_KEY)
        logger.info("✅ Supabase connection established")
        
        # Initialize InfluxDB
        influxdb = InfluxDBClient(
            url=settings.INFLUXDB_URL,
            token=settings.INFLUXDB_TOKEN,
            org=settings.INFLUXDB_ORG
        )
        logger.info("✅ InfluxDB connection established")
        
    except Exception as e:
        logger.error(f"❌ Database initialization failed: {e}")
        raise


def get_supabase() -> Client:
    """Get Supabase client"""
    if supabase is None:
        raise RuntimeError("Supabase client not initialized")
    return supabase


def get_influxdb() -> InfluxDBClient:
    """Get InfluxDB client"""
    if influxdb is None:
        raise RuntimeError("InfluxDB client not initialized")
    return influxdb


async def close_db():
    """Close database connections"""
    global supabase, influxdb
    
    if influxdb:
        influxdb.close()
        logger.info("🔌 InfluxDB connection closed")
    
    # Supabase doesn't need explicit closing
    logger.info("🔌 Supabase connection closed") 