from fastapi import APIRouter, Depends, HTTPException
from app.dependencies.auth import get_current_user

router = APIRouter(prefix="/dashboard", tags=["dashboard"])

@router.get("/threats")
def get_threats(current_user: dict = Depends(get_current_user)):
    if current_user["role"] not in ["admin", "security"]:
        raise HTTPException(status_code=403, detail="Insufficient permissions")
    return {"message": "Threat data for authorized users", "user": current_user}