# 🚀 Local Development Setup for NR BUDGET Planner

## 🎯 Goal
Set up a local development environment with clean URLs working perfectly, then deploy the working solution to Hostinger.

## 🔧 Quick Start (Choose Your OS)

### **macOS/Linux:**
```bash
# Make script executable (first time only)
chmod +x start-local-dev.sh

# Start local development server
./start-local-dev.sh
```

### **Windows:**
```cmd
# Double-click the batch file or run in Command Prompt
start-local-dev.bat
```

### **Manual Start (Any OS):**
```bash
php -S localhost:8000 local-dev.php
```

## 🌐 Test Your Clean URLs

Once the server is running, visit these URLs in your browser:

### **✅ Should Work:**
- `http://localhost:8000/` → Dashboard
- `http://localhost:8000/dashboard` → Dashboard
- `http://localhost:8000/login` → Login page
- `http://localhost:8000/income` → Income page
- `http://localhost:8000/expenses` → Expenses page
- `http://localhost:8000/bills` → Bills page
- `http://localhost:8000/savings` → Savings page
- `http://localhost:8000/analytics` → Analytics page

### **🔍 Test 404 Handling:**
- `http://localhost:8000/nonexistent` → Should show custom 404 page

## 📁 File Structure for Local Development

```
BudgetPlanner/
├── local-dev.php              ← Local development router
├── deploy-clean-urls.php      ← Deployment-ready router
├── start-local-dev.sh         ← macOS/Linux startup script
├── start-local-dev.bat        ← Windows startup script
├── index.php                  ← Your current dashboard
├── login.php                  ← Login page
├── pages/
│   ├── income.php             ← Income page
│   ├── expenses.php           ← Expenses page
│   ├── bills.php              ← Bills page
│   └── ...                    ← Other pages
└── ...                        ← Other files
```

## 🧪 Testing Checklist

### **Before Deployment:**
- [ ] All clean URLs work locally
- [ ] 404 pages show properly
- [ ] Navigation between pages works
- [ ] No PHP errors in browser console
- [ ] All pages load with correct styling

### **Test These Scenarios:**
1. **Direct URL Access:**
   - Visit `http://localhost:8000/income` directly
   - Should load income page without redirects

2. **Navigation Links:**
   - Click links between pages
   - URLs should stay clean (no .php extensions)

3. **404 Handling:**
   - Visit non-existent URLs
   - Should show custom 404 page with helpful links

4. **Performance:**
   - Pages should load quickly
   - No infinite redirects

## 🚀 Deployment to Hostinger

### **Step 1: Test Locally First**
Make sure everything works perfectly on `localhost:8000` before deploying.

### **Step 2: Prepare for Deployment**
1. **Backup your current index.php:**
   ```bash
   # On Hostinger server
   cp index.php dashboard.php
   ```

2. **Upload the deployment router:**
   ```bash
   # Upload deploy-clean-urls.php to Hostinger
   # Then rename it to index.php
   mv deploy-clean-urls.php index.php
   ```

### **Step 3: Test on Hostinger**
After deployment, test these URLs:
- `https://nrbudegetplanner.online/income` → Should work!
- `https://nrbudegetplanner.online/expenses` → Should work!
- `https://nrbudegetplanner.online/dashboard` → Should work!

## 🔧 Troubleshooting Local Development

### **Problem: "Address already in use"**
```bash
# Kill any existing PHP processes
pkill -f "php -S"

# Or use a different port
php -S localhost:8001 local-dev.php
```

### **Problem: Pages show PHP code**
- Check if PHP is installed: `php -v`
- Make sure you're using the router: `php -S localhost:8000 local-dev.php`

### **Problem: 404 for all routes**
- Check if `local-dev.php` exists in current directory
- Verify file permissions: `ls -la local-dev.php`

### **Problem: Database connection errors**
- Check if `config/database.php` exists
- Verify your local database credentials
- Use local `.env` file for database settings

## 📋 Development Workflow

### **1. Local Development:**
```bash
# Start local server
./start-local-dev.sh

# Make changes to your code
# Test clean URLs locally
# Fix any issues
```

### **2. Testing:**
```bash
# Test all routes work
# Verify 404 handling
# Check navigation
# Ensure no PHP errors
```

### **3. Deployment:**
```bash
# Commit your changes
git add .
git commit -m "Clean URLs working locally"
git push origin php7.4-compatibility

# Deploy to Hostinger
# Test clean URLs on live site
```

## 🎉 Success Indicators

### **✅ Local Development Working:**
- All clean URLs load correctly
- No `.php` extensions in browser address bar
- Custom 404 pages for invalid routes
- Fast page loading
- No redirect loops

### **✅ Ready for Deployment:**
- `deploy-clean-urls.php` tested locally
- All routes working perfectly
- No PHP errors or warnings
- Clean, professional URLs

## 💡 Pro Tips

1. **Always test locally first** - Fix issues before deploying
2. **Use the startup scripts** - They check for common problems
3. **Test 404 handling** - Important for user experience
4. **Check browser console** - Look for JavaScript errors
5. **Test navigation flow** - Ensure users can move between pages

## 🆘 Need Help?

### **Common Issues:**
- **Port conflicts**: Use different port (8001, 8002, etc.)
- **File permissions**: Make scripts executable (`chmod +x`)
- **PHP not found**: Install PHP or add to PATH
- **Database errors**: Check local database configuration

### **Next Steps:**
1. Start local development server
2. Test all clean URLs
3. Fix any issues locally
4. Deploy working solution to Hostinger

---

**🎯 Goal**: Get clean URLs working perfectly locally, then deploy the proven solution to Hostinger!
