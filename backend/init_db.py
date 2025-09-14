from app.database import Base, engine
from app.models.user import User  # Import models to ensure they’re registered

# Create all tables defined in models
Base.metadata.create_all(bind=engine)

print("Database tables created successfully!")