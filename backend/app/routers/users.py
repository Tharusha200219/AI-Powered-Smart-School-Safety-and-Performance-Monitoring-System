from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from app.database import get_db
from app.models.user import User
from app.schemas.user import UserCreate, UserOut, UserUpdate
from app.dependencies.auth import get_current_user, check_permission, get_password_hash
from typing import List

router = APIRouter(prefix="/auth/users", tags=["users"])

# Default permissions for roles
DEFAULT_PERMISSIONS = {
    "admin": {
        "view_threats": True,
        "manage_users": True,
        "view_performance": True,
        "manage_seating": True,
        "generate_quizzes": True
    },
    "security": {
        "view_threats": True,
        "view_objects": True
    },
    "teacher": {
        "view_performance": True,
        "generate_quizzes": True
    },
    "student": {
        "take_quiz": True,
        "view_own_performance": True
    }
}

@router.post("/", response_model=UserOut)
def create_user(user: UserCreate, db: Session = Depends(get_db), current_user: dict = Depends(check_permission("manage_users"))):
    db_user = db.query(User).filter(User.username == user.username).first()
    if db_user:
        raise HTTPException(status_code=400, detail="Username already registered")
    hashed_password = get_password_hash(user.password)
    # Assign default permissions based on role
    permissions = DEFAULT_PERMISSIONS.get(user.role, {})
    db_user = User(
        username=user.username,
        password_hash=hashed_password,
        role=user.role,
        permissions=permissions
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user

@router.get("/", response_model=List[UserOut])
def list_users(db: Session = Depends(get_db), current_user: dict = Depends(check_permission("manage_users"))):
    return db.query(User).all()

@router.put("/{user_id}", response_model=UserOut)
def update_user(user_id: int, user_update: UserUpdate, db: Session = Depends(get_db), current_user: dict = Depends(check_permission("manage_users"))):
    db_user = db.query(User).filter(User.id == user_id).first()
    if not db_user:
        raise HTTPException(status_code=404, detail="User not found")
    if user_update.username:
        db_user.username = user_update.username
    if user_update.role:
        db_user.role = user_update.role
        # Update permissions based on new role
        db_user.permissions = DEFAULT_PERMISSIONS.get(user_update.role, db_user.permissions)
    if user_update.permissions:
        db_user.permissions = {**db_user.permissions, **user_update.permissions}
    db.commit()
    db.refresh(db_user)
    return db_user

@router.delete("/{user_id}")
def delete_user(user_id: int, db: Session = Depends(get_db), current_user: dict = Depends(check_permission("manage_users"))):
    db_user = db.query(User).filter(User.id == user_id).first()
    if not db_user:
        raise HTTPException(status_code=404, detail="User not found")
    db.delete(db_user)
    db.commit()
    return {"detail": "User deleted"}