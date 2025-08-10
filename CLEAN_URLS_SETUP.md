# 🚀 Clean URLs Setup Guide for Hostinger

## 🎯 Goal
Get completely clean URLs like `yourdomain.com/income` instead of `yourdomain.com/pages/income.php`

## 🔧 Solution Options (Choose One)

### **Option 1: .htaccess (Recommended - Most Professional)**

#### Step 1: Use the Simple .htaccess
1. **Rename** `.htaccess-simple` to `.htaccess`
2. **Upload** to your `public_html` directory
3. **Test** by visiting `yourdomain.com/income`

#### Step 2: If .htaccess Doesn't Work
Contact Hostinger support with this message:
```
Subject: Enable mod_rewrite and .htaccess processing

Message:
Hello,

I need to enable clean URLs for my PHP application. Please:

1. Enable mod_rewrite module
2. Allow .htaccess processing
3. Set AllowOverride to All

Domain: nrbudegetplanner.online

Thank you!
```

### **Option 2: PHP Router (Works Everywhere)**

#### Step 1: Use the Router
1. **Upload** `clean-urls.php` to your `public_html` directory
2. **Rename** it to `index.php` (backup your current `index.php` first!)
3. **Test** by visiting `yourdomain.com/income`

#### Step 2: Update Your Current index.php
1. **Backup** your current `index.php` to `dashboard.php`
2. **Rename** `clean-urls.php` to `index.php`
3. **Update** the dashboard route in `clean-urls.php` to point to `dashboard.php`

### **Option 3: Hybrid Approach (Best of Both)**

#### Step 1: Try .htaccess First
1. Use `.htaccess-simple`
2. Test clean URLs
3. If it works, you're done!

#### Step 2: Fallback to PHP Router
1. If .htaccess fails, use `clean-urls.php`
2. This ensures clean URLs work regardless of server configuration

## 🧪 Testing Your Setup

### **Test These URLs:**
- ✅ `yourdomain.com/income` → Should show income page
- ✅ `yourdomain.com/expenses` → Should show expenses page  
- ✅ `yourdomain.com/dashboard` → Should show dashboard
- ✅ `yourdomain.com/login` → Should show login page

### **If URLs Don't Work:**
- ❌ Check if files are uploaded
- ❌ Verify file permissions (644 for .htaccess, 755 for directories)
- ❌ Contact Hostinger support about mod_rewrite

## 📁 File Structure After Setup

```
public_html/
├── .htaccess          ← Clean URL rules
├── index.php          ← Main router or dashboard
├── login.php          ← Login page
├── pages/
│   ├── income.php     ← Income page
│   ├── expenses.php   ← Expenses page
│   └── ...           ← Other pages
└── ...                ← Other files
```

## 🔒 Security Features

### **Protected Directories:**
- `config/` - Configuration files
- `includes/` - PHP includes
- `database/` - Database files

### **Clean URLs Supported:**
- `/income` → `pages/income.php`
- `/expenses` → `pages/expenses.php`
- `/dashboard` → `index.php`
- `/login` → `login.php`
- `/register` → `register.php`
- `/logout` → `logout.php`
- `/bills` → `pages/bills.php`
- `/savings` → `pages/savings.php`
- `/monthly-budget` → `pages/monthly-budget.php`
- `/analytics` → `pages/monthly-analytics.php`
- `/quick-actions` → `pages/quick-actions.php`
- `/actual-expenses` → `pages/actual-expenses.php`
- `/user-approvals` → `pages/user-approvals.php`
- `/pin-settings` → `pages/pin-settings.php`
- `/expense-categories` → `pages/expense-categories.php`
- `/reports` → `pages/reports.php`
- `/quick-expense` → `pages/quick-expense.php`

## 🚨 Troubleshooting

### **Problem: 500 Internal Server Error**
- **Cause**: .htaccess syntax error or mod_rewrite disabled
- **Solution**: Use `clean-urls.php` instead

### **Problem: 404 Not Found**
- **Cause**: Route not defined or file missing
- **Solution**: Check file exists and route is defined

### **Problem: Page Shows PHP Code**
- **Cause**: PHP not processing .htaccess
- **Solution**: Contact Hostinger support

### **Problem: Some URLs Work, Others Don't**
- **Cause**: Partial .htaccess processing
- **Solution**: Use `clean-urls.php` for consistent behavior

## 📞 Hostinger Support Request Template

```
Subject: Enable Clean URLs - mod_rewrite and .htaccess

Message:
Hello,

I need to enable clean URLs for my PHP application. Currently getting 404 errors.

Please enable:
1. mod_rewrite module
2. .htaccess processing  
3. Set AllowOverride to All

Domain: nrbudegetplanner.online
Current issue: Clean URLs like /income return 404

Thank you!
```

## 🎉 Success Indicators

### **✅ Clean URLs Working:**
- `yourdomain.com/income` loads income page
- `yourdomain.com/expenses` loads expenses page
- No `.php` extensions in browser address bar
- Professional-looking URLs

### **✅ Performance:**
- Pages load quickly
- No redirect loops
- Clean error handling
- Proper 404 pages

---

**💡 Pro Tip**: Start with Option 1 (.htaccess) as it's the most professional solution. If it doesn't work, fall back to Option 2 (PHP router) which will work on any server configuration.
