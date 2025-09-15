AI-Powered Smart School Safety and Performance Monitoring System
This project is a web-based application for school safety and performance monitoring, featuring AI-powered threat detection, attendance tracking, performance analytics, and quiz generation. It includes a FastAPI backend with MySQL and a React frontend with Vite and Tailwind CSS. The system supports role-based access control (RBAC) for admins, teachers, security personnel, and students, with internationalization (i18n) support.
Features

Role-Based Access:
Admin: Manage users, view threats, seating arrangements, and performance predictions.
Security: Monitor threat alerts and left-behind objects.
Teacher: Generate quizzes and view class performance.
Student: Take quizzes and view personal performance.


AI Integration: Threat detection and performance analytics (placeholder endpoints).
IoT Integration: Attendance tracking (placeholder endpoints).
Internationalization: English translations with extensible language support.
Security: JWT authentication, password hashing, and permission-based access.

Prerequisites
Before setting up the project, ensure you have the following installed:

Python 3.8+:
Windows: Download from python.org or Microsoft Store.
Mac: Install via Homebrew (brew install python) or python.org.


Node.js 18+:
Windows/Mac: Download from nodejs.org or use Homebrew on Mac (brew install node).


MySQL 8.0+:
Windows: Download from mysql.com or use WSL.
Mac: Install via Homebrew (brew install mysql).


Git: For cloning the repository.
Code Editor: VS Code recommended.

Project Structure
AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System/
├── backend/              # FastAPI backend
│   ├── app/
│   │   ├── models/       # SQLAlchemy models
│   │   ├── routers/      # API endpoints
│   │   ├── seeders/      # Database seeders
│   │   ├── database.py   # Database configuration
│   │   ├── main.py       # FastAPI entry point
│   ├── .env              # Environment variables
├── frontend/             # React frontend
│   ├── src/
│   │   ├── components/   # React components
│   │   ├── locales/      # Translation files
│   │   ├── routes/       # React Router routes
│   │   ├── services/     # API services
│   ├── .env             # Frontend environment variables
├── README.md            # This file

Setup Instructions
1. Clone the Repository
Clone the project to your local machine:
git clone <repository-url>
cd AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System

2. Set Up the Backend (FastAPI)
The backend requires Python, MySQL, and dependencies.
Windows

Install Python:

Download and install Python 3.8+ from python.org.
Ensure Python is added to PATH during installation.
Verify: python --version


Install MySQL:

Download MySQL Community Server from mysql.com.
Follow the installer, set a root password, and start the MySQL service.
Verify: mysql -u root -p


Set Up Virtual Environment:
cd backend
python -m venv venv
.\venv\Scripts\activate


Install Dependencies:
pip install fastapi uvicorn sqlalchemy pymysql python-dotenv python-jose[cryptography] passlib[bcrypt] python-multipart


Configure Environment Variables:

Create a backend/.env file:DB_USER=root
DB_PASSWORD=your_mysql_password
DB_HOST=localhost
DB_NAME=school_safety_db
SECRET_KEY=your-secret-key-32-characters-long


Generate a SECRET_KEY:python -c "import secrets; print(secrets.token_hex(16))"




Set Up MySQL Database:

Create the database:mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS school_safety_db;"




Create Database Tables:
python -c "from app.database import Base, engine; Base.metadata.create_all(bind=engine)"


Seed Default Admin User:

Creates an admin user (admin/admin123):python -m app.seeders.seed_admin




Run the Backend:
uvicorn app.main:app --host 0.0.0.0 --port 8000


Access API docs at http://localhost:8000/docs.



Mac

Install Python:

Install via Homebrew:brew install python


Verify: python3 --version


Install MySQL:

Install via Homebrew:brew install mysql
brew services start mysql


Set up root password:mysql_secure_installation


Verify: mysql -u root -p


Set Up Virtual Environment:
cd backend
python3 -m venv venv
source venv/bin/activate


Install Dependencies:
pip install fastapi uvicorn sqlalchemy pymysql python-dotenv python-jose[cryptography] passlib[bcrypt] python-multipart


Configure Environment Variables:

Create a backend/.env file:DB_USER=root
DB_PASSWORD=your_mysql_password
DB_HOST=localhost
DB_NAME=school_safety_db
SECRET_KEY=your-secret-key-32-characters-long


Generate a SECRET_KEY:python3 -c "import secrets; print(secrets.token_hex(16))"




Set Up MySQL Database:

Create the database:mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS school_safety_db;"




Create Database Tables:
python3 -c "from app.database import Base, engine; Base.metadata.create_all(bind=engine)"


Seed Default Admin User:

Creates an admin user (admin/admin123):python3 -m app.seeders.seed_admin




Run the Backend:
uvicorn app.main:app --host 0.0.0.0 --port 8000


Access API docs at http://localhost:8000/docs.



3. Set Up the Frontend (React)
The frontend requires Node.js and npm.
Windows/Mac

Install Node.js:

Windows: Download from nodejs.org.
Mac: Install via Homebrew:brew install node


Verify: node --version and npm --version


Install Dependencies:
cd frontend
npm install


Configure Environment Variables:

Create a frontend/.env file:VITE_API_URL=http://localhost:8000




Run the Frontend:
npm run dev


Access the app at http://localhost:5173.



4. Using the Application

Log In:

Open http://localhost:5173/login.
Use the default admin credentials:
Username: admin
Password: admin123


Change the password after first login (recommended).


Admin Features:

Navigate to /dashboard/admin/users to manage users (add, edit, delete).
Assign roles (admin, teacher, security, student) and permissions (e.g., view_threats, manage_users).


Test API:

Use the API docs (http://localhost:8000/docs) to test endpoints like /auth/login, /auth/users.



5. Troubleshooting

Backend Errors:
MySQL Connection: Ensure MySQL is running and credentials in .env are correct.
ModuleNotFoundError: Verify you're in the virtual environment (source venv/bin/activate or .\venv\Scripts\activate) and dependencies are installed.
Table Issues: Recreate tables if schema errors occur:mysql -u root -p school_safety_db -e "DROP TABLE users;"
python3 -c "from app.database import Base, engine; Base.metadata.create_all(bind=engine)"




Frontend Errors:
API Connection: Ensure VITE_API_URL matches the backend URL.
Port Conflict: Change the port in npm run dev if 5173 is in use (--port 5174).


Seeding Issues: If seed_admin.py fails, check MySQL connection and table schema.

6. Security Notes

Change Default Password: Update the admin password after setup.
Production Setup:
Disable seeding in production (SEED_ADMIN=false in .env).
Use HTTPS for backend/frontend.
Store SECRET_KEY securely.


Database Backup: Before schema changes, back up:mysqldump -u root -p school_safety_db > backup.sql



7. Extending the Project

Add Features: Implement endpoints for attendance, quizzes, and AI model integration in backend/app/routers/.
More Languages: Add translation files (e.g., frontend/src/locales/es.json) for i18n.
IoT/AI: Add endpoints for IoT data (e.g., /api/attendance/mark) and AI model outputs.

8. Contributing

Fork the repository and create feature branches.
Follow coding standards (PEP 8 for Python, ESLint for JavaScript).
Submit pull requests with clear descriptions.

For issues or contributions, contact the team via [repository issues](/issues).


Python completelu Uninstall in windows
choco uninstall python -y

Then reinstall python
choco install python -y