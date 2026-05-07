from pydantic import BaseModel
from typing import Optional
from datetime import datetime
from enum import Enum


class TrapStatus(str, Enum):
    ACTIVE = "active"
    INACTIVE = "inactive"
    MAINTENANCE = "maintenance"
    OFFLINE = "offline"


class TrapType(str, Enum):
    CECID_FLY = "cecid_fly"
    FRUIT_FLY = "fruit_fly"
    LEAF_HOPPER = "leaf_hopper"
    GENERAL = "general"


class TrapBase(BaseModel):
    name: str
    trap_type: TrapType
    location_lat: float
    location_lng: float
    farm_id: int
    status: TrapStatus = TrapStatus.ACTIVE


class TrapCreate(TrapBase):
    device_id: str
    battery_level: Optional[float] = None


class TrapUpdate(BaseModel):
    name: Optional[str] = None
    location_lat: Optional[float] = None
    location_lng: Optional[float] = None
    status: Optional[TrapStatus] = None
    battery_level: Optional[float] = None
    last_maintenance: Optional[datetime] = None


class TrapResponse(TrapBase):
    id: int
    device_id: str
    battery_level: Optional[float] = None
    last_maintenance: Optional[datetime] = None
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True


class TrapHealth(BaseModel):
    trap_id: int
    battery_level: float
    signal_strength: Optional[float] = None
    last_seen: datetime
    status: TrapStatus 