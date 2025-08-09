# Database Migration & Seeder System

This directory contains the Laravel-style migration and seeder system for the NR BUDGET Planner application.

## 📁 Directory Structure

```
database/
├── migrations/          # Database migration files
│   ├── 001_create_users_table.php
│   ├── 002_create_security_pin_table.php
│   ├── 003_create_months_table.php
│   ├── 004_create_weeks_table.php
│   ├── 005_create_expense_categories_table.php
│   ├── 006_create_income_sources_table.php
│   ├── 007_create_expenses_table.php
│   ├── 008_create_payment_methods_table.php
│   ├── 009_create_savings_accounts_table.php
│   ├── 010_create_actual_expenses_table.php
│   ├── 011_create_quick_actions_table.php
│   └── 012_create_notifications_table.php
├── seeders/            # Database seeder files
│   ├── DatabaseSeeder.php
│   ├── DefaultExpenseCategoriesSeeder.php
│   ├── DefaultPaymentMethodsSeeder.php
│   ├── DefaultSavingsAccountsSeeder.php
│   └── AdminUserSeeder.php
├── migrate.php         # Migration runner
├── setup.php          # Complete setup script
└── README.md          # This file
```

## 🚀 Quick Start

### 1. Complete Setup (Recommended)
```bash
php database/setup.php
```

This will:
- Run all migrations in order
- Seed the database with default data
- Create an admin user

### 2. Individual Commands

#### Run Migrations Only
```bash
php database/migrate.php
```

#### Run Seeders Only
```bash
php database/seeders/DatabaseSeeder.php
```

#### Check Migration Status
```bash
php database/migrate.php status
```

#### Rollback Migrations
```bash
php database/migrate.php rollback [number_of_steps]
```

#### Reset Database
```bash
php database/setup.php reset
```

## 📋 Migration Files

Each migration file follows the Laravel convention:

```php
class CreateTableName {
    public function up($pdo) {
        // Create table logic
    }
    
    public function down($pdo) {
        // Drop table logic
    }
}
```

### Migration Order
1. **users** - User accounts and authentication
2. **security_pin** - 6-digit PIN security
3. **months** - Monthly budget periods
4. **weeks** - Weekly tracking periods
5. **expense_categories** - Expense categorization
6. **income_sources** - Income tracking
7. **expenses** - Budgeted expenses
8. **payment_methods** - Payment method tracking
9. **savings_accounts** - Savings account management
10. **actual_expenses** - Actual expense tracking
11. **quick_actions** - Quick expense templates
12. **notifications** - User notifications

## 🌱 Seeder Files

### DatabaseSeeder
Main seeder that runs all other seeders in order:
- DefaultExpenseCategoriesSeeder
- DefaultPaymentMethodsSeeder
- DefaultSavingsAccountsSeeder
- AdminUserSeeder

### Default Data
- **15 Expense Categories** (Food & Dining, Transportation, etc.)
- **8 Payment Methods** (Cash, Credit Card, etc.)
- **5 Savings Accounts** (Emergency Fund, Vacation Fund, etc.)
- **1 Admin User** (admin/admin123/123456)

## 🔧 Deployment

### Production Deployment
1. Copy the application files
2. Configure database connection in `config/database.php`
3. Run setup:
   ```bash
   php database/setup.php
   ```
4. Change default admin credentials

### Development Environment
1. Clone the repository
2. Configure local database
3. Run setup:
   ```bash
   php database/setup.php
   ```

### Database Updates
When adding new features:
1. Create new migration file: `database/migrations/013_create_new_table.php`
2. Run migrations: `php database/migrate.php`
3. Create seeder if needed: `database/seeders/NewDataSeeder.php`
4. Update DatabaseSeeder to include new seeder

## 🛡️ Security Features

- **Foreign Key Constraints** - Data integrity
- **Indexes** - Query performance
- **User Isolation** - Multi-user data separation
- **Password Hashing** - Secure authentication
- **PIN Security** - Additional access control

## 📊 Database Schema

### Key Features
- **Multi-user Support** - All tables include `user_id`
- **Audit Trail** - `created_at` and `updated_at` timestamps
- **Soft Deletes** - `is_active` flags for data retention
- **Flexible Enums** - Type-safe category fields
- **Decimal Precision** - Accurate financial calculations

### Relationships
- Users → All tables (one-to-many)
- Months → Weeks (one-to-many)
- Categories → Expenses (one-to-many)
- Expenses → Actual Expenses (one-to-many)

## 🔍 Troubleshooting

### Common Issues

1. **Migration Already Run**
   - Check status: `php database/migrate.php status`
   - Reset if needed: `php database/setup.php reset`

2. **Foreign Key Errors**
   - Ensure migrations run in correct order
   - Check table dependencies

3. **Seeder Errors**
   - Verify admin user exists before running other seeders
   - Check for duplicate data with `INSERT IGNORE`

### Debug Commands
```bash
# Check migration status
php database/migrate.php status

# View database structure
php database/setup.php status

# Reset and recreate
php database/setup.php reset
php database/setup.php
```

## 📝 Adding New Migrations

1. Create new file: `database/migrations/013_create_new_table.php`
2. Follow naming convention: `CreateTableName`
3. Implement `up()` and `down()` methods
4. Add proper indexes and foreign keys
5. Test with: `php database/migrate.php`

## 🌟 Benefits

- **Version Control** - Track database changes
- **Easy Deployment** - One-command setup
- **Data Integrity** - Proper relationships and constraints
- **Multi-Environment** - Same setup for dev/staging/prod
- **Rollback Support** - Undo changes if needed
- **Default Data** - Consistent starting point

---

**Note**: This system is compatible with PHP 7.4+ and follows Laravel conventions for familiarity and maintainability.
