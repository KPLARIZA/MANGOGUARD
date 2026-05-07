import firebase_admin
from firebase_admin import credentials, auth
from app.core.config import settings
import logging

logger = logging.getLogger(__name__)


def init_firebase():
    """Initialize Firebase Admin SDK"""
    try:
        # Create credentials from environment variables
        cred = credentials.Certificate({
            "type": "service_account",
            "project_id": settings.FIREBASE_PROJECT_ID,
            "private_key_id": "",
            "private_key": settings.FIREBASE_PRIVATE_KEY.replace('\\n', '\n'),
            "client_email": settings.FIREBASE_CLIENT_EMAIL,
            "client_id": "",
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url": f"https://www.googleapis.com/robot/v1/metadata/x509/{settings.FIREBASE_CLIENT_EMAIL}"
        })
        
        # Initialize Firebase Admin
        firebase_admin.initialize_app(cred)
        logger.info("✅ Firebase Admin SDK initialized")
        
    except Exception as e:
        logger.error(f"❌ Firebase initialization failed: {e}")
        raise


async def verify_firebase_token(id_token: str) -> dict:
    """Verify Firebase ID token and return user info"""
    try:
        decoded_token = auth.verify_id_token(id_token)
        return {
            "uid": decoded_token["uid"],
            "email": decoded_token.get("email"),
            "email_verified": decoded_token.get("email_verified", False),
            "name": decoded_token.get("name"),
            "picture": decoded_token.get("picture")
        }
    except Exception as e:
        logger.error(f"❌ Firebase token verification failed: {e}")
        raise


async def get_user_by_uid(uid: str) -> dict:
    """Get user information from Firebase by UID"""
    try:
        user = auth.get_user(uid)
        return {
            "uid": user.uid,
            "email": user.email,
            "email_verified": user.email_verified,
            "display_name": user.display_name,
            "photo_url": user.photo_url,
            "disabled": user.disabled
        }
    except Exception as e:
        logger.error(f"❌ Failed to get user by UID: {e}")
        raise 