# Hostinger Setup Guide

## Quick Fix for Database Access Issue

The error you encountered is because:
1. You can't create databases on Hostinger shared hosting
2. Your database name in .env doesn't match your actual database

## Step 1: Check Your Actual Database Name

In your Hostinger control panel:
1. Go to **Databases** section
2. Look for your database name (usually starts with `u885255124_`)
3. Note the exact database name, username, and password

## Step 2: Update Your .env File

Edit your `.env` file with the correct Hostinger credentials:

```bash
# Update these values in your .env file
DB_HOST=localhost
DB_NAME=u885255124_YOUR_ACTUAL_DB_NAME
DB_USER=u885255124_YOUR_ACTUAL_USERNAME
DB_PASS=YOUR_ACTUAL_PASSWORD
DB_CHARSET=utf8mb4
DB_PORT=3306
```

## Step 3: Import Tables (Not Database)

Use the new file: `database/hostinger-tables-only.sql`

**Option A: phpMyAdmin (Recommended)**
1. Go to Hostinger control panel → phpMyAdmin
2. Select your existing database
3. Click **Import**
4. Choose `database/hostinger-tables-only.sql`
5. Click **Go**

**Option B: Command Line**
```bash
mysql -u YOUR_USERNAME -p YOUR_DATABASE_NAME < database/hostinger-tables-only.sql
```

## Step 4: Test Connection

```bash
php database/quick-setup.php
```

## Step 5: Run Seeders (Optional)

```bash
php database/setup.php
```

## Common Database Names on Hostinger

Your database name is likely one of these:
- `u885255124_budget_planner`
- `u885255124_nr_budget`
- `u885255124_budget`

## Troubleshooting

**If you still get access denied:**
1. Check your database credentials in .env
2. Make sure you're using the database name from Hostinger control panel
3. Verify the database user has permissions to your database

**If tables already exist:**
- Drop all tables first, then import again
- Or use the migration system: `php database/setup.php`
