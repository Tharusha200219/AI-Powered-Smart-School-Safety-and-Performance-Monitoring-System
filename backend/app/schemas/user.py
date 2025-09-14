from pydantic import BaseModel
from typing import Dict

class UserBase(BaseModel):
    username: str
    role: str = "student"
    permissions: Dict[str, bool] = {}

class UserCreate(UserBase):
    password: str

class UserUpdate(BaseModel):
    username: str | None = None
    role: str | None = None
    permissions: Dict[str, bool] | None = None

class UserOut(UserBase):
    id: int

    class Config:
        orm_mode = True

class Token(BaseModel):
    access_token: str
    token_type: str