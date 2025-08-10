# Deployment Guide for Hostinger Shared Hosting

## Overview
This guide explains how to deploy the Budget Planner application to Hostinger shared hosting with a **Laravel-inspired secure file structure**.

## 🚀 **Laravel-Inspired Security Architecture**

The application now follows Laravel's security principle: **only one PHP file is publicly accessible** - everything else is secured outside the web root.

## File Structure
```
public_html/          # Web-accessible files (Hostinger public directory)
├── .htaccess        # Apache configuration and security
└── index.php        # SINGLE entry point (Laravel-style)

app/                  # Application files (NOT web-accessible - SECURED)
├── config/          # Configuration files
│   ├── database.php
│   └── database.example.php
├── includes/        # PHP functions and components
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── pages/           # Application pages
│   ├── dashboard.php
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── income.php
│   ├── expenses.php
│   ├── bills.php
│   └── ... (other pages)
├── ajax/            # AJAX handlers
│   ├── mark-notification-read.php
│   └── mark-notifications-read.php
├── assets/          # Static assets
│   ├── logo.html
│   └── logo.svg
└── router.php       # Application router
```

## 🔒 **Security Features**

- **Single Entry Point**: Only `index.php` is accessible from the web
- **Complete App Isolation**: All application files are outside web root
- **Advanced .htaccess Protection**: Blocks direct access to app/ directory
- **Security Headers**: XSS protection, content type sniffing prevention, frame options
- **File Access Control**: Prevents access to sensitive file types
- **Backup File Protection**: Blocks access to temporary and backup files

## 🚀 **Deployment Steps**

### 1. Upload Files to Hostinger
1. Connect to your Hostinger hosting via FTP/SFTP
2. Navigate to the `public_html` directory (or your domain's public directory)
3. Upload **ONLY** the contents of the `public_html/` folder to the root of `public_html`
4. Upload the `app/` folder to the **parent directory** of `public_html` (same level as public_html)

**⚠️ CRITICAL**: The `app/` folder must be uploaded OUTSIDE of `public_html` for security!

### 2. Database Setup
1. Create a MySQL database in Hostinger control panel
2. Import the `database.sql` file
3. Update `app/config/database.php` with your Hostinger database credentials:
   ```php
   define('DB_HOST', 'your_hostinger_host');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_database_username');
   define('DB_PASS', 'your_database_password');
   ```

### 3. File Permissions
Set the following permissions:
- `public_html/`: 755
- `app/`: 755
- `app/config/`: 755
- `app/config/database.php`: 644

### 4. Test the Application
1. Visit your domain to test the application
2. Test login functionality
3. Verify all pages load correctly
4. **Verify security**: Try to access `yoursite.com/app/` - it should be blocked

## 🌐 **URL Structure**
The application supports clean URLs through the single entry point:
- `/` → Dashboard
- `/login` → Login page
- `/income` → Income management
- `/expenses` → Expense management
- `/bills` → Bill management
- `/savings` → Savings tracking
- `/analytics` → Monthly analytics
- `/reports` → Financial reports

## 🔧 **Troubleshooting**

### Common Issues
1. **500 Internal Server Error**: Check .htaccess syntax and mod_rewrite support
2. **Database Connection Error**: Verify database credentials and host settings
3. **File Not Found**: Ensure `app/` folder is uploaded to the correct location
4. **Permission Denied**: Check file and directory permissions

### Debug Mode
To enable debug mode, add this to the top of `public_html/index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## ⚡ **Performance Optimization**
- Static assets are cached for 1 year
- Gzip compression is enabled
- Clean URLs improve SEO and user experience
- Single entry point reduces server overhead

## 🛡️ **Security Notes**
- **NEVER** expose the `app/` directory to the web
- Keep `database.php` outside of web root
- The `app/` folder is completely protected by .htaccess
- Regularly update the application
- Monitor access logs for suspicious activity

## 📁 **File Upload Example**
```
Your Hostinger Account/
├── public_html/          ← Upload contents of public_html/ here
│   ├── .htaccess
│   └── index.php
└── app/                  ← Upload app/ folder here (same level as public_html)
    ├── config/
    ├── includes/
    ├── pages/
    ├── ajax/
    ├── assets/
    └── router.php
```

## 🆘 **Support**
For issues specific to Hostinger hosting, contact Hostinger support.
For application issues, check the error logs in your Hostinger control panel.

## ✅ **Security Verification**
After deployment, verify security by trying to access:
- `yoursite.com/app/` → Should return 403 Forbidden
- `yoursite.com/app/config/` → Should return 403 Forbidden
- `yoursite.com/app/includes/` → Should return 403 Forbidden
