from fastapi import APIRouter, HTTPException, Depends
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel
from typing import Optional
import logging

from app.core.firebase import verify_firebase_token
from app.core.database import get_supabase
from app.models.user import UserCreate, UserResponse

logger = logging.getLogger(__name__)

router = APIRouter()
security = HTTPBearer()


class TokenRequest(BaseModel):
    id_token: str


class TokenResponse(BaseModel):
    access_token: str
    token_type: str = "bearer"
    user: UserResponse


@router.post("/verify", response_model=TokenResponse)
async def verify_token(token_request: TokenRequest):
    """Verify Firebase ID token and return user info"""
    try:
        # Verify Firebase token
        user_info = await verify_firebase_token(token_request.id_token)
        
        # Get or create user in Supabase
        supabase = get_supabase()
        
        # Check if user exists
        user_data = supabase.table("users").select("*").eq("firebase_uid", user_info["uid"]).execute()
        
        if not user_data.data:
            # Create new user
            new_user = UserCreate(
                firebase_uid=user_info["uid"],
                email=user_info["email"],
                name=user_info.get("name", ""),
                role="farmer"  # Default role
            )
            
            user_data = supabase.table("users").insert(new_user.dict()).execute()
            user = user_data.data[0]
        else:
            user = user_data.data[0]
        
        # Create JWT token (simplified for now)
        access_token = token_request.id_token  # In production, create a proper JWT
        
        return TokenResponse(
            access_token=access_token,
            user=UserResponse(**user)
        )
        
    except Exception as e:
        logger.error(f"Token verification failed: {e}")
        raise HTTPException(status_code=401, detail="Invalid token")


async def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security)) -> dict:
    """Get current authenticated user"""
    try:
        token = credentials.credentials
        user_info = await verify_firebase_token(token)
        
        # Get user from Supabase
        supabase = get_supabase()
        user_data = supabase.table("users").select("*").eq("firebase_uid", user_info["uid"]).execute()
        
        if not user_data.data:
            raise HTTPException(status_code=404, detail="User not found")
        
        return user_data.data[0]
        
    except Exception as e:
        logger.error(f"Authentication failed: {e}")
        raise HTTPException(status_code=401, detail="Invalid authentication credentials") 