from pydantic import BaseModel, EmailStr
from typing import Optional
from datetime import datetime
from enum import Enum


class UserRole(str, Enum):
    ADMIN = "admin"
    FARMER = "farmer"
    RESEARCHER = "researcher"


class UserBase(BaseModel):
    email: EmailStr
    name: str
    role: UserRole = UserRole.FARMER


class UserCreate(UserBase):
    firebase_uid: str


class UserUpdate(BaseModel):
    name: Optional[str] = None
    role: Optional[UserRole] = None
    farm_location: Optional[str] = None
    phone: Optional[str] = None


class UserResponse(UserBase):
    id: int
    firebase_uid: str
    farm_location: Optional[str] = None
    phone: Optional[str] = None
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True


class UserInDB(UserResponse):
    pass 