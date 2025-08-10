# NR BUDGET Planner 💰

<div align="center">
  <img src="assets/logo.svg" alt="NR BUDGET Planner Logo" width="200" height="200">
  <h1>NR BUDGET Planner</h1>
  <p><strong>Smart Personal Finance Management System</strong></p>
  <p>Track income, expenses, savings, and bills with powerful analytics and forecasting</p>
  
  [![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
  [![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3+-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
  [![Chart.js](https://img.shields.io/badge/Chart.js-4.0+-FF6384?style=for-the-badge&logo=chart.js&logoColor=white)](https://chartjs.org)
</div>

---

## 🚀 Features

### 💳 **Multi-User System**
- Secure user registration with admin approval
- Individual PIN-based authentication (6-digit PIN)
- User-specific data isolation
- Admin notification system for new registrations

### 📊 **Comprehensive Budget Management**
- Monthly budget planning and tracking
- Income sources management with scheduling
- Detailed expense categorization with due dates
- Actual vs. budgeted expense comparison
- Weekly expense tracking

### 💰 **Advanced Financial Tracking**
- Real-time expense recording with quick actions
- Payment method tracking (Cash, Credit Card, Online, Savings)
- Savings account management with progress tracking
- Quick actions for daily expenses
- Expense categories with visual indicators

### 📅 **Bill & Subscription Management**
- Due date tracking for bills and subscriptions
- Overdue and upcoming bill notifications
- Payment status tracking with "Mark as Paid" functionality
- Bill type categorization (Utilities, Subscriptions, Credit Cards, Loans, Insurance)
- Automatic bill status updates

### 🏷️ **Expense Categories Management**
- Custom expense categories with color coding
- Visual indicators for categories with due dates
- Category-based expense organization
- Easy category management (add, edit, delete)

### 📈 **Analytics & Forecasting**
- Monthly financial reports and comparisons
- Previous month, current month, and next month forecasting
- Interactive charts and graphs with Chart.js
- Savings progress tracking and goals
- Budget vs. actual analysis

### 📱 **Mobile-First Design**
- Responsive Bootstrap 5.3 interface
- Mobile-optimized navigation with dropdown menus
- Touch-friendly controls and forms
- Clean, modern UI with consistent styling

### 🔔 **Smart Notifications**
- Admin notifications for new user registrations
- Bill due date reminders
- Payment status updates
- System-wide notification management

### 🗄️ **Database Management**
- Laravel-style migration system for easy deployment
- Database seeding with default data
- Safe database updates and schema management
- Automated database setup and reset functionality

### ⚙️ **Environment Configuration**
- Easy `.env` file configuration system
- Centralized application settings
- Environment-specific configurations
- Interactive setup wizard for quick configuration

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+ (Compatible with PHP 8.2+)
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5.3, Chart.js 4.0+
- **Icons**: Font Awesome 6.0
- **Security**: PIN-based authentication with hashing
- **URLs**: Clean URL routing with .htaccess support
- **Development**: PHP development server with custom router
- **Database**: Migration and seeder system for easy deployment
- **Configuration**: Environment-based configuration system

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- PHP 7.4 or higher (tested with PHP 7.4.33 and PHP 8.2.29)
- MySQL 8.0 or higher
- Web server (Apache/Nginx) or PHP development server
- Composer (optional, for dependency management)

---

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/nakerz99/budget-and-expenses-tracker.git
cd budget-and-expenses-tracker
```

### 2. Environment Configuration (Recommended)

#### Option A: Interactive Setup (Easiest)
```bash
# Run the interactive environment setup
php setup-env.php

# This will guide you through creating your .env file
# and test the database connection
```

#### Option B: Manual Setup
```bash
# Copy the example environment file
cp env.example .env

# Edit .env with your configuration
nano .env
```

### 3. Database Setup

#### Option A: Quick Setup with Migration System (Recommended)
```bash
# Run the complete database setup
php database/setup.php

# Or run individual components
php database/setup.php migrate    # Run migrations only
php database/setup.php seed       # Run seeders only
php database/setup.php status     # Check migration status
php database/setup.php reset      # Reset database (WARNING: deletes all data)
```

#### Option B: Manual Database Setup
```bash
# Create a new MySQL database
mysql -u root -p
CREATE DATABASE budget_planner;
USE budget_planner;

# Import the database schema
mysql -u root -p budget_planner < database.sql

# Run database updates (if needed)
mysql -u root -p budget_planner < update_database_safe.sql
mysql -u root -p budget_planner < update_bills.sql
mysql -u root -p budget_planner < update_categories_with_due_dates.sql
```

### 4. Test Configuration
```bash
# Test your environment configuration
php test-config.php

# This will verify all configuration systems are working
```

### 5. Start the Application

#### Option A: PHP Development Server (Recommended for Development)
```bash
php -S localhost:8080 -t . router.php
```

#### Option B: Laravel Valet (macOS)
```bash
# Install Valet if not already installed
composer global require laravel/valet
valet install

# Park the directory and use PHP 7.4
valet park
valet use php@7.4

# Access via http://budgetplanner.test
```

#### Option C: Apache/Nginx
1. Configure your web server to point to the project directory
2. Ensure mod_rewrite is enabled for Apache
3. Access the application via your web browser

### 6. Initial Setup
1. Visit `http://localhost:8080/register` to create the first admin account
2. The first registered user automatically becomes an admin
3. Login with your credentials and start managing your budget!

---

## ⚙️ Configuration

### Environment Variables

The application uses a `.env` file for configuration. Key variables include:

#### Database Configuration
```env
DB_HOST=localhost
DB_NAME=budget_planner
DB_USER=root
DB_PASS=your_password
DB_CHARSET=utf8mb4
DB_PORT=3306
```

#### Application Configuration
```env
APP_NAME="NR BUDGET Planner"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080
APP_TIMEZONE=Asia/Manila
```

#### Security Configuration
```env
SESSION_LIFETIME=3600
PIN_LENGTH=6
MAX_LOGIN_ATTEMPTS=5
LOGIN_TIMEOUT=300
```

#### Feature Limits
```env
MAX_EXPENSES_PER_MONTH=100
MAX_CATEGORIES_PER_USER=50
MAX_SAVINGS_ACCOUNTS=10
MAX_PAYMENT_METHODS=20
```

#### Notification Settings
```env
ENABLE_EMAIL_NOTIFICATIONS=false
ENABLE_BILL_REMINDERS=true
BILL_REMINDER_DAYS=3
ADMIN_NOTIFICATION_EMAIL=admin@budgetplanner.com
```

### Configuration Functions

The application provides helper functions for accessing configuration:

```php
// Get any configuration value
$value = config('DB_HOST', 'localhost');

// Get database configuration
$dbConfig = getDatabaseConfig();

// Get application configuration
$appConfig = getAppConfig();

// Get security configuration
$securityConfig = getSecurityConfig();

// Check environment
if (isDevelopment()) {
    // Development-specific code
}

if (isProduction()) {
    // Production-specific code
}
```

---

## 📖 Usage Guide

### 🔐 Authentication
- **Registration**: Create a new account with username, email, password, and 6-digit PIN
- **Login**: Use your username and PIN to access the system
- **Admin Approval**: New registrations require admin approval (first user is auto-approved)
- **PIN Management**: Update your security PIN from the user menu

### 💰 Managing Your Budget
1. **Set Up Monthly Budget**: Create a new monthly budget from the Monthly Budget page
2. **Add Income Sources**: Define your income sources and amounts with scheduling
3. **Plan Expenses**: Categorize and budget your expected expenses
4. **Track Actual Spending**: Record your actual expenses as they occur
5. **Monitor Progress**: Use the dashboard and analytics to track your financial health

### 📅 Bill Management
- **Add Bills**: Mark expenses as bills with due dates and categories
- **Track Due Dates**: View upcoming and overdue bills with status indicators
- **Update Payments**: Mark bills as paid and track payment methods
- **Bill Categories**: Organize bills by type (Utilities, Subscriptions, Credit Cards, etc.)

### 🏷️ Expense Categories
- **Create Categories**: Add custom expense categories with colors
- **Due Date Indicators**: Categories with bills show visual indicators
- **Manage Categories**: Edit or delete categories as needed
- **Category Organization**: Organize expenses by custom categories

### 💾 Savings Tracking
- **Create Savings Accounts**: Set up multiple savings accounts (Cash, Bank, Digital)
- **Track Progress**: Monitor your savings goals and progress
- **Automatic Calculations**: System calculates savings based on income vs. expenses
- **Savings Analytics**: View savings trends and projections

### ⚡ Quick Actions
- **Daily Expenses**: Use quick action buttons for common expenses
- **Custom Amounts**: Modify amounts for quick expense recording
- **Payment Methods**: Select payment method for each expense
- **Notes**: Add notes to track expense details

---

## 🗄️ Database Schema

### Core Tables
- `users` - User accounts and authentication
- `months` - Monthly budget periods
- `weeks` - Weekly tracking periods
- `income_sources` - Income categories and amounts
- `expenses` - Budgeted expenses and categories
- `actual_expenses` - Recorded actual spending

### Supporting Tables
- `security_pin` - User PIN authentication
- `payment_methods` - Payment method tracking
- `savings_accounts` - Savings account management
- `quick_actions` - Quick expense recording
- `notifications` - System notifications
- `expense_categories` - Expense categories with due date indicators

### Bill Management
- Bills are stored in the `expenses` table with `is_bill = TRUE`
- Payment status tracked via `actual_expenses` table
- Due dates and bill types for categorization

### Migration System
- **Migrations**: 12 migration files for database schema management
- **Seeders**: Default data population (categories, payment methods, savings accounts)
- **Setup Scripts**: Automated database setup and management
- **Safe Updates**: Database update scripts with existence checks

---

## 🎨 UI/UX Features

### 📱 Responsive Design
- Mobile-first approach with Bootstrap 5.3
- Collapsible navigation with dropdown menus
- Touch-friendly buttons and controls
- Optimized layouts for all screen sizes

### 🎯 User Experience
- Clean, intuitive interface with consistent design
- Visual indicators for important information
- Color-coded categories and status badges
- Helpful tooltips and guidance

### 📊 Data Visualization
- Interactive charts with Chart.js
- Color-coded financial data
- Progress indicators and status badges
- Trend analysis graphs

### 🔔 Smart Notifications
- Real-time notification system
- Admin approval workflow
- Bill due date reminders
- Payment status updates

---

## 🔒 Security Features

### 🔐 Authentication
- PIN-based authentication system (6-digit)
- Secure password hashing with bcrypt
- Session management with timeout
- Admin approval workflow

### 🛡️ Data Protection
- Input sanitization and validation
- SQL injection prevention with prepared statements
- XSS protection
- CSRF protection

### 👥 Multi-User Security
- User data isolation
- Role-based access control
- Secure admin functions
- Audit trail for approvals

---

## 📱 Mobile Testing

The application has been tested and optimized for:
- ✅ iPhone (iOS 14+)
- ✅ Android (Android 10+)
- ✅ iPad/Tablet devices
- ✅ Desktop browsers (Chrome, Firefox, Safari, Edge)

### Mobile-Specific Features
- Responsive navigation with hamburger menu
- Touch-optimized form controls
- Swipe-friendly tables
- Mobile-optimized modals and dialogs
- Dropdown menus for better mobile navigation

---

## ⚡ Performance

### Optimization Features
- Efficient database queries with proper indexing
- Optimized CSS and JavaScript
- Compressed assets
- Caching strategies

### Best Practices
- Clean URL routing with custom router
- Minimal HTTP requests
- Optimized images
- Progressive enhancement

---

## 🔄 Recent Updates

### v2.4.0 - Environment Configuration System
- ✅ **Environment Configuration**: Easy .env file configuration system
- ✅ **Interactive Setup**: Guided setup wizard with `php setup-env.php`
- ✅ **Centralized Config**: All settings in one place with helper functions
- ✅ **Configuration Testing**: Test script to verify all systems working
- ✅ **Environment Classes**: Professional configuration management
- ✅ **Easy Deployment**: Simple configuration for different environments

### v2.3.0 - Database Migration System & Cleanup
- ✅ **Migration System**: Laravel-style database migrations and seeders
- ✅ **Automated Setup**: One-command database setup with `php database/setup.php`
- ✅ **Database Cleanup**: Removed duplicate and unnecessary entries
- ✅ **Netflix Removal**: Completely removed Netflix subscription from system
- ✅ **Safe Updates**: Database update scripts with existence checks
- ✅ **Easy Deployment**: Migration system for consistent database setup

### v2.2.0 - PHP 7.4 Compatibility
- ✅ **PHP 7.4 Support**: Full compatibility with PHP 7.4.33+
- ✅ **Backward Compatibility**: Works with PHP 7.4 through PHP 8.2+
- ✅ **Valet Integration**: Laravel Valet support for local development
- ✅ **Comprehensive Testing**: PHP 7.4 compatibility verified
- ✅ **Branch Management**: Dedicated `php7.4-compatibility` branch

### v2.1.0 - Enhanced Bill Management & Categories
- ✅ **Bill Payment Tracking**: "Mark as Paid" functionality with status updates
- ✅ **Expense Categories Management**: Full CRUD operations for categories
- ✅ **Due Date Indicators**: Visual indicators for categories with bills
- ✅ **Payment Status**: Paid bills show payment date and amount
- ✅ **Dashboard Cleanup**: Removed redundant "Add Custom Expenses" button
- ✅ **Enhanced Navigation**: Improved mobile navigation with dropdowns

### v2.0.0 - Multi-User & Enhanced Features
- ✅ Multi-user system with admin approval
- ✅ Bill and subscription tracking with due dates
- ✅ Enhanced mobile responsiveness
- ✅ Clean URL routing with custom router
- ✅ Improved analytics and forecasting
- ✅ Payment method tracking
- ✅ Savings account management
- ✅ Notification system

### v1.5.0 - Mobile Optimization
- ✅ Mobile-first responsive design
- ✅ Touch-friendly interface
- ✅ Optimized navigation
- ✅ Mobile-specific styling

### v1.0.0 - Core Features
- ✅ Basic budget management
- ✅ Income and expense tracking
- ✅ Monthly analytics
- ✅ PIN-based authentication

---

## 🐛 Troubleshooting

### Common Issues
1. **Database Connection**: Ensure MySQL is running and credentials are correct
2. **URL Routing**: Use the custom router for PHP development server
3. **Permissions**: Ensure proper file permissions for uploads and logs
4. **Session Issues**: Clear browser cache and cookies if login problems occur
5. **PHP Version**: Ensure PHP 7.4+ is installed and configured

### Debug Tools
- `debug.php` - Session and authentication debugging
- `test-login.php` - Login functionality testing
- `test_compatibility.php` - PHP 7.4 compatibility testing
- `test-config.php` - Configuration system testing
- Error logs in PHP and MySQL for detailed debugging

### Configuration Issues
```bash
# Test your configuration
php test-config.php

# Interactive environment setup
php setup-env.php

# Check environment file
cat .env
```

### Database Management
```bash
# Check migration status
php database/setup.php status

# Reset database (WARNING: deletes all data)
php database/setup.php reset

# Run specific components
php database/setup.php migrate
php database/setup.php seed
```

### PHP Version Testing
```bash
# Test PHP 7.4 compatibility
php test_compatibility.php

# Check PHP version
php -v

# Test with Valet (macOS)
valet use php@7.4
valet park
```

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Branch Strategy
- `main` - Latest stable version (PHP 8.2+)
- `php7.4-compatibility` - PHP 7.4 compatible version with migrations and env config
- Feature branches for new development

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Nakerz99**
- GitHub: [@nakerz99](https://github.com/nakerz99)
- Project: [NR BUDGET Planner](https://github.com/nakerz99/budget-and-expenses-tracker)

---

## 🙏 Acknowledgments

- Bootstrap team for the amazing UI framework
- Chart.js for beautiful data visualization
- Font Awesome for the comprehensive icon library
- PHP community for the robust backend framework
- MySQL community for the reliable database system
- Laravel team for Valet development tool and migration inspiration

---

<div align="center">
  <p><strong>NR BUDGET Planner</strong> - Take control of your finances today! 💰</p>
  <p>Made with ❤️ by Nakerz99</p>
</div>
