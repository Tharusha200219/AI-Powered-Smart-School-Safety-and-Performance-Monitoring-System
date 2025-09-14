from sqlalchemy.orm import Session
from app.database import SessionLocal
from app.models.user import User
from app.dependencies.auth import get_password_hash

def seed_admin():
    # Create a new database session
    db = SessionLocal()
    try:
        # Check if admin user already exists
        admin_username = "admin"
        if db.query(User).filter(User.username == admin_username).first():
            print(f"Admin user '{admin_username}' already exists. Skipping seeding.")
            return

        # Define default admin user
        admin_user = User(
            username=admin_username,
            password_hash=get_password_hash("admin123"),  # Default password
            role="admin",
            permissions={
                "view_threats": True,
                "manage_users": True,
                "view_performance": True,
                "manage_seating": True,
                "generate_quizzes": True
            }
        )

        # Add and commit to database
        db.add(admin_user)
        db.commit()
        print(f"Default admin user '{admin_username}' created successfully.")
    except Exception as e:
        print(f"Error seeding admin user: {e}")
        db.rollback()
    finally:
        db.close()

if __name__ == "__main__":
    seed_admin()