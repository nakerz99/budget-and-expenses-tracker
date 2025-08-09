-- Budget Planner Database Schema
-- Created for PHP Budget and Expenses Tracker

-- Create database
CREATE DATABASE IF NOT EXISTS budget_planner;
USE budget_planner;

-- PIN table for security
CREATE TABLE security_pin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pin_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Users table (for future multi-user support)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    approved_at TIMESTAMP NULL,
    approved_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Months table for tracking monthly data
CREATE TABLE months (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    year INT NOT NULL,
    month INT NOT NULL,
    name VARCHAR(20) NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_month (user_id, year, month),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Weeks table for tracking weekly data
CREATE TABLE weeks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    year INT NOT NULL,
    month INT NOT NULL,
    week_number INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    name VARCHAR(30) NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_week (user_id, year, month, week_number),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Income sources table
CREATE TABLE income_sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    schedule_type ENUM('monthly', 'weekly', 'specific_date') NOT NULL,
    schedule_day INT NULL, -- Day of month for specific_date schedule
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Expense categories table
CREATE TABLE expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#007bff', -- Hex color for UI
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Expenses table (budgeted amounts)
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    budgeted_amount DECIMAL(10,2) NOT NULL,
    schedule_type ENUM('monthly', 'weekly', 'specific_date') NOT NULL,
    schedule_day INT NULL, -- Day of month for specific_date schedule
    description TEXT,
    due_date DATE NULL,
    is_bill BOOLEAN DEFAULT FALSE,
    bill_type VARCHAR(50) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE CASCADE
);

-- Actual expenses table (real spending)
CREATE TABLE actual_expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    expense_id INT NOT NULL,
    month_id INT NOT NULL,
    week_id INT NULL, -- For weekly tracking
    actual_amount DECIMAL(10,2) NOT NULL,
    date_paid DATE NOT NULL,
    notes TEXT,
    payment_method_id INT NULL,
    savings_account_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE CASCADE,
    FOREIGN KEY (month_id) REFERENCES months(id) ON DELETE CASCADE,
    FOREIGN KEY (week_id) REFERENCES weeks(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL,
    FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE SET NULL
);

-- Quick actions table for frequently used expenses
CREATE TABLE quick_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-receipt',
    color VARCHAR(7) DEFAULT '#007bff',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE CASCADE
);

-- Payment methods table
CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    type ENUM('cash', 'credit_card', 'online', 'savings') NOT NULL,
    bank_name VARCHAR(100),
    icon VARCHAR(50) DEFAULT 'fas fa-credit-card',
    color VARCHAR(7) DEFAULT '#007bff',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Savings accounts table
CREATE TABLE savings_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    type ENUM('cash', 'bank', 'digital') NOT NULL,
    bank_name VARCHAR(100),
    account_number VARCHAR(50),
    current_balance DECIMAL(10,2) DEFAULT 0.00,
    target_balance DECIMAL(10,2) DEFAULT 0.00,
    icon VARCHAR(50) DEFAULT 'fas fa-piggy-bank',
    color VARCHAR(7) DEFAULT '#28a745',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default PIN (000111)
INSERT INTO security_pin (user_id, pin_hash) VALUES 
(1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert default user
INSERT INTO users (username, email, password_hash, is_approved) VALUES 
('admin', 'admin@budgetplanner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Insert current month
INSERT INTO months (user_id, year, month, name, is_active) VALUES 
(1, 2024, 12, 'December 2024', TRUE);

-- Insert current week
INSERT INTO weeks (user_id, year, month, week_number, start_date, end_date, name, is_active) VALUES 
(1, 2024, 12, 1, '2024-12-01', '2024-12-07', 'Week 1 - Dec 2024', TRUE),
(1, 2024, 12, 2, '2024-12-08', '2024-12-14', 'Week 2 - Dec 2024', FALSE),
(1, 2024, 12, 3, '2024-12-15', '2024-12-21', 'Week 3 - Dec 2024', FALSE),
(1, 2024, 12, 4, '2024-12-22', '2024-12-28', 'Week 4 - Dec 2024', FALSE),
(1, 2024, 12, 5, '2024-12-29', '2024-12-31', 'Week 5 - Dec 2024', FALSE);

-- Insert expense categories
INSERT INTO expense_categories (name, description, color) VALUES
('NSJBI', 'NSJBI Payment', '#dc3545'),
('Pag-ibig Loan', 'Pag-ibig Loan Payment', '#fd7e14'),
('Food', 'Monthly Food Expenses', '#28a745'),
('Meralco Bill', 'Electricity Bill', '#ffc107'),
('Water Bill', 'Water Utility Bill', '#17a2b8'),
('Baby Necessities', 'Baby Care Items', '#e83e8c'),
('Pets', 'Pet Care Expenses', '#6f42c1'),
('Laundry', 'Laundry Services', '#20c997'),
('Helper', 'Household Helper', '#6c757d'),
('Insurance', 'Insurance Payments', '#343a40'),
('Internet', 'Internet Service', '#007bff'),
('Credit Cards', 'Credit Card Payments', '#dc3545'),
('Dette', 'Debt Payments', '#fd7e14'),
('Leisure', 'Entertainment & Leisure', '#28a745'),
('BPI Credit to Cash', 'BPI Credit to Cash', '#ffc107'),
('Subscriptions', 'Monthly Subscriptions', '#17a2b8');

-- Insert income sources (based on provided data)
INSERT INTO income_sources (user_id, name, amount, schedule_type, schedule_day, description) VALUES
(1, 'Hammerulo', 98000.00, 'specific_date', 5, 'Hammerulo Income - 5th and 20th'),
(1, 'Hammerulo', 98000.00, 'specific_date', 20, 'Hammerulo Income - 5th and 20th'),
(1, 'Mile Marker', 98000.00, 'specific_date', 30, 'Mile Marker Income - 30th of the month'),
(1, 'Remotify', 20000.00, 'specific_date', 30, 'Remotify Income - 30th of the month'),
(1, 'PisoNet', 11000.00, 'specific_date', 30, 'PisoNet Income - 30th of the month');

-- Insert budgeted expenses (based on provided data)
INSERT INTO expenses (user_id, category_id, name, budgeted_amount, schedule_type, schedule_day, description) VALUES
(1, 1, 'NSJBI', 20866.57, 'specific_date', 20, 'NSJBI Payment'),
(1, 2, 'Pag-ibig Loan', 31048.00, 'specific_date', 30, 'Pag-ibig Loan Payment'),
(1, 3, 'Food', 21700.00, 'monthly', NULL, 'Monthly Food Expenses'),
(1, 4, 'Meralco Bill', 9000.00, 'specific_date', 20, 'Electricity Bill'),
(1, 5, 'Water Bill', 350.00, 'specific_date', 20, 'Water Utility Bill'),
(1, 6, 'Baby Necessities', 6600.00, 'monthly', NULL, 'Baby Care Items'),
(1, 7, 'Pets', 4000.00, 'monthly', NULL, 'Pet Care Expenses'),
(1, 8, 'Laundry', 2500.00, 'monthly', NULL, 'Laundry Services'),
(1, 9, 'Helper', 8000.00, 'monthly', NULL, 'Household Helper'),
(1, 10, 'Insurance', 2000.00, 'specific_date', 30, 'Insurance Payments'),
(1, 11, 'Internet', 2000.00, 'specific_date', 20, 'Internet Service'),
(1, 12, 'Unionbank CC', 2629.63, 'specific_date', 10, 'Unionbank Credit Card'),
(1, 12, 'Security Bank CC', 150.00, 'specific_date', 20, 'Security Bank Credit Card'),
(1, 13, 'Dette', 10000.00, 'monthly', NULL, 'Debt Payments'),
(1, 14, 'Leisure', 10000.00, 'monthly', NULL, 'Entertainment & Leisure'),
(1, 15, 'BPI Credit to Cash', 2090.00, 'monthly', NULL, 'BPI Credit to Cash'),

(1, 16, 'i-cloud', 180.00, 'monthly', NULL, 'iCloud Subscription'),
(1, 16, 'Youtube', 250.00, 'monthly', NULL, 'YouTube Subscription'),
(1, 16, 'Cursor', 1000.00, 'monthly', NULL, 'Cursor Subscription'),
(1, 16, 'Smart Postpaid', 2400.00, 'monthly', NULL, 'Smart Postpaid Plan');

-- Insert quick actions for common expenses
INSERT INTO quick_actions (user_id, name, category_id, amount, icon, color) VALUES
(1, 'Food', 3, 500.00, 'fas fa-utensils', '#28a745'),
(1, 'Gas', 3, 300.00, 'fas fa-gas-pump', '#ffc107'),
(1, 'Coffee', 3, 150.00, 'fas fa-coffee', '#6f42c1'),
(1, 'Transport', 3, 200.00, 'fas fa-bus', '#17a2b8'),
(1, 'Shopping', 14, 1000.00, 'fas fa-shopping-bag', '#e83e8c'),
(1, 'Medicine', 6, 300.00, 'fas fa-pills', '#dc3545'),
(1, 'Snacks', 3, 100.00, 'fas fa-cookie-bite', '#fd7e14'),
(1, 'Parking', 3, 50.00, 'fas fa-parking', '#6c757d');
