from sqlalchemy import Column, Integer, String, JSON
from app.database import Base

class User(Base):
    __tablename__ = "users"
    id = Column(Integer, primary_key=True, index=True)
    username = Column(String(50), unique=True, index=True)
    password_hash = Column(String(255))
    role = Column(String(20), default="student")  # Roles: student, teacher, admin, security
    permissions = Column(JSON, default={})  # e.g., {"view_threats": true, "manage_users": false}