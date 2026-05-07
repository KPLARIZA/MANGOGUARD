from fastapi import APIRouter
from app.api.v1.endpoints import auth, users, traps, detections, analytics, alerts

api_router = APIRouter()

# Include all endpoint routers
api_router.include_router(auth.router, prefix="/auth", tags=["Authentication"])
api_router.include_router(users.router, prefix="/users", tags=["Users"])
api_router.include_router(traps.router, prefix="/traps", tags=["Traps"])
api_router.include_router(detections.router, prefix="/detections", tags=["Detections"])
api_router.include_router(analytics.router, prefix="/analytics", tags=["Analytics"])
api_router.include_router(alerts.router, prefix="/alerts", tags=["Alerts"]) 