#!/bin/bash

# Local Development Starter Script for NR BUDGET Planner
# This script starts a local PHP server with clean URL routing

echo "🚀 Starting NR BUDGET Planner Local Development Server"
echo "=================================================="

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed or not in PATH"
    echo "Please install PHP and try again"
    exit 1
fi

# Check if we're in the right directory
if [ ! -f "local-dev.php" ]; then
    echo "❌ Error: local-dev.php not found"
    echo "Please run this script from the project root directory"
    exit 1
fi

# Get PHP version
PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
echo "✅ PHP Version: $PHP_VERSION"

# Check if required files exist
echo "🔍 Checking required files..."
if [ ! -f "config/database.php" ]; then
    echo "⚠️  Warning: config/database.php not found"
    echo "   You may need to set up your database configuration"
fi

if [ ! -f "pages/income.php" ]; then
    echo "⚠️  Warning: pages/income.php not found"
    echo "   Some routes may not work properly"
fi

echo ""
echo "🌐 Starting local server..."
echo "📍 URL: http://localhost:8000"
echo "🔗 Clean URLs will work:"
echo "   • http://localhost:8000/income"
echo "   • http://localhost:8000/expenses"
echo "   • http://localhost:8000/dashboard"
echo "   • http://localhost:8000/login"
echo ""
echo "🛑 Press Ctrl+C to stop the server"
echo "=================================================="

# Start PHP development server
php -S localhost:8000 local-dev.php
