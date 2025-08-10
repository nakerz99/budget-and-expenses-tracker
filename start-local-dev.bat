@echo off
REM Local Development Starter Script for NR BUDGET Planner (Windows)
REM This script starts a local PHP server with clean URL routing

echo 🚀 Starting NR BUDGET Planner Local Development Server
echo ==================================================

REM Check if PHP is available
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Error: PHP is not installed or not in PATH
    echo Please install PHP and try again
    pause
    exit /b 1
)

REM Check if we're in the right directory
if not exist "local-dev.php" (
    echo ❌ Error: local-dev.php not found
    echo Please run this script from the project root directory
    pause
    exit /b 1
)

REM Get PHP version
for /f "tokens=2 delims= " %%i in ('php -v ^| findstr "PHP"') do set PHP_VERSION=%%i
echo ✅ PHP Version: %PHP_VERSION%

REM Check if required files exist
echo 🔍 Checking required files...
if not exist "config\database.php" (
    echo ⚠️  Warning: config\database.php not found
    echo    You may need to set up your database configuration
)

if not exist "pages\income.php" (
    echo ⚠️  Warning: pages\income.php not found
    echo    Some routes may not work properly
)

echo.
echo 🌐 Starting local server...
echo 📍 URL: http://localhost:8000
echo 🔗 Clean URLs will work:
echo    • http://localhost:8000/income
echo    • http://localhost:8000/expenses
echo    • http://localhost:8000/dashboard
echo    • http://localhost:8000/login
echo.
echo 🛑 Press Ctrl+C to stop the server
echo ==================================================

REM Start PHP development server
php -S localhost:8000 local-dev.php
