# School Management System

A comprehensive school management system built with Laravel framework for managing students, teachers, classes, security staff, and other school operations.

## 🚀 Quick Setup Guide

Follow these step-by-step instructions to get the project running on your local machine.

### Prerequisites

Before you begin, make sure you have the following installed on your system:

#### For Windows:

-   **PHP 8.2 or higher** - [Download from php.net](https://www.php.net/downloads)
-   **Composer** - [Download from getcomposer.org](https://getcomposer.org/download/)
-   **Node.js (18+)** - [Download from nodejs.org](https://nodejs.org/)
-   **Git** - [Download from git-scm.com](https://git-scm.com/)

#### For Mac:

```bash
# Install Homebrew if you haven't already
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install required packages
brew install php@8.2
brew install composer
brew install node
brew install git
```

### 📋 Setup Instructions

#### Step 1: Clone or Download the Project

```bash
# If using Git
git clone <repository-url>
cd school-management-system

# Or simply extract the downloaded zip file and navigate to the folder
cd path/to/your/project
```

#### Step 2: Install PHP Dependencies

```bash
composer install
```

#### Step 3: Install Node.js Dependencies

```bash
npm install
```

#### Step 4: Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Step 5: Configure Your Environment

Edit the `.env` file with your preferred settings:

```bash
APP_NAME="School Management System"
APP_URL=http://localhost:8000

# Database Configuration (SQLite is used by default)
DB_CONNECTION=sqlite

# For MySQL (optional)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=school_management
# DB_USERNAME=root
# DB_PASSWORD=your_password
```

#### Step 6: Database Setup

```bash
# Create SQLite database file (if using SQLite)
touch database/database.sqlite

# Run migrations and seeders
php artisan migrate --seed
```

#### Step 7: Build Frontend Assets

```bash
# Build assets for production
npm run build

# Or run in development mode with hot reload
npm run dev
```

#### Step 8: Start the Development Server

```bash
# Start Laravel development server
php artisan serve
```

Your application will be available at: **http://localhost:8000**

---

## 🔧 Alternative Setup Methods

### Option 1: Using Laravel Sail (Docker)

If you have Docker installed:

```bash
# Install dependencies first
composer install

# Start with Sail
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate --seed
```

### Option 2: All-in-One Development Command

After completing steps 1-6 above, you can use:

```bash
composer run dev
```

This will start:

-   Laravel development server
-   Queue worker
-   Application logs
-   Vite development server

---

## 🗂️ Project Structure

```
├── app/                    # Application logic
│   ├── Http/Controllers/   # Controllers
│   ├── Models/            # Eloquent models
│   ├── DataTables/        # DataTable classes
│   └── ...
├── database/              # Database files
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Public assets
├── resources/            # Views, CSS, JS
│   ├── views/           # Blade templates
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
└── routes/              # Application routes
```

---

## 🎯 Default Login Credentials

After seeding the database, you can log in with:

-   **Email:** admin@example.com
-   **Password:** password

_(Note: Change these credentials in production)_

---

## 🛠️ Common Issues & Solutions

### Issue: "Class not found" errors

**Solution:**

```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
```

### Issue: Permission denied (Mac/Linux)

**Solution:**

```bash
sudo chmod -R 755 storage/
sudo chmod -R 755 bootstrap/cache/
```

### Issue: NPM build fails

**Solution:**

```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Issue: Database connection error

**Solution:**

-   Verify your `.env` database settings
-   Make sure database exists
-   Check database credentials

---

## 📱 Features

-   **Student Management** - Register, manage student profiles and records
-   **Teacher Management** - Manage teacher information and assignments
-   **Class Management** - Organize classes and subjects
-   **Security Staff** - Manage security personnel
-   **User Roles & Permissions** - Role-based access control
-   **Responsive Design** - Works on desktop and mobile devices

---

## 🔄 Development Workflow

### Daily Development

```bash
# Start development environment
npm run dev          # In one terminal
php artisan serve    # In another terminal
```

### Code Updates

```bash
# After pulling new changes
composer install
npm install
php artisan migrate
npm run build
```

### Database Reset

```bash
# Reset and reseed database
php artisan migrate:fresh --seed
```

---

## 📞 Support

If you encounter any issues during setup:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify all prerequisites are installed
3. Ensure file permissions are correct
4. Make sure all environment variables are set

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
