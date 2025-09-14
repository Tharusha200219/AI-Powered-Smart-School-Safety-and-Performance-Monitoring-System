# AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System
**AI Smart School System**   Proof-of-concept for one classroom. Uses Python/React to detect bullying via audio/video, track attendance with NFC/QR wristbands, generate quizzes, store marks in SQLite, predict performance, and optimize seating. Alerts via Twilio. Built with free tools (OpenCV, Whisper) and GitHub Student Pack.


Project Setup Guide
This guide provides instructions to set up the project on macOS and Windows. The project uses FastAPI, SQLAlchemy, and other Python libraries to build a web application with MySQL as the database.
Prerequisites

Python 3.8+: Ensure Python 3.8 or higher is installed.
macOS: Run python3 --version to check. Install via Homebrew (brew install python) or from python.org.
Windows: Run python --version to check. Install from python.org or the Microsoft Store.


MySQL Server: A running MySQL server (local or cloud, e.g., AWS RDS). Install locally:
macOS: brew install mysql
Windows: Download from mysql.com or use a package manager like Chocolatey (choco install mysql).


Basic Familiarity: Knowledge of Python and virtual environments is recommended.

Setup Instructions
1. Create a Virtual Environment
A virtual environment isolates project dependencies.
macOS
python3 -m venv venv
source venv/bin/activate

Windows
python -m venv venv
venv\Scripts\activate

After activation, your terminal prompt should show (venv).
2. Install Dependencies
Install the required Python packages in the virtual environment.
macOS
pip install fastapi uvicorn sqlalchemy pymysql "python-jose[cryptography]" "passlib[bcrypt]" python-multipart

Note: The quotes around "python-jose[cryptography]" and "passlib[bcrypt]" prevent zsh from misinterpreting square brackets. To avoid quoting in the future, add this to your ~/.zshrc:
echo 'alias pip="noglob pip"' >> ~/.zshrc
source ~/.zshrc

Windows
pip install fastapi uvicorn sqlalchemy pymysql python-jose[cryptography] passlib[bcrypt] python-multipart

3. Verify Installation
Check that the packages are installed:
pip list

You should see:

fastapi
uvicorn
sqlalchemy
pymysql
python-jose
passlib
python-multipart

