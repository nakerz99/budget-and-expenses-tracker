-- Database Update Script for NR BUDGET Planner
-- Run this script to add missing tables and columns

USE budget_planner;

-- Add missing columns to users table
ALTER TABLE users 
ADD COLUMN is_approved BOOLEAN DEFAULT FALSE,
ADD COLUMN approved_at TIMESTAMP NULL,
ADD COLUMN approved_by INT NULL,
ADD FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

-- Update existing admin user to be approved
UPDATE users SET is_approved = TRUE WHERE username = 'admin';

-- Add missing columns to security_pin table
ALTER TABLE security_pin 
ADD COLUMN user_id INT NOT NULL DEFAULT 1,
ADD COLUMN is_active BOOLEAN DEFAULT TRUE,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Update existing PIN to be associated with admin user
UPDATE security_pin SET user_id = 1 WHERE id = 1;

-- Add missing columns to months table
ALTER TABLE months 
ADD COLUMN user_id INT DEFAULT 1,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Update existing months to be associated with admin user
UPDATE months SET user_id = 1;

-- Add missing columns to weeks table
ALTER TABLE weeks 
ADD COLUMN user_id INT DEFAULT 1,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Update existing weeks to be associated with admin user
UPDATE weeks SET user_id = 1;

-- Add missing columns to income_sources table
ALTER TABLE income_sources 
ADD COLUMN user_id INT DEFAULT 1,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Update existing income sources to be associated with admin user
UPDATE income_sources SET user_id = 1;

-- Add missing columns to expenses table
ALTER TABLE expenses 
ADD COLUMN user_id INT DEFAULT 1,
ADD COLUMN due_date DATE NULL,
ADD COLUMN is_bill BOOLEAN DEFAULT FALSE,
ADD COLUMN bill_type VARCHAR(50) NULL,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Update existing expenses to be associated with admin user
UPDATE expenses SET user_id = 1;

-- Add missing columns to actual_expenses table
ALTER TABLE actual_expenses 
ADD COLUMN payment_method_id INT NULL,
ADD COLUMN savings_account_id INT NULL,
ADD FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL,
ADD FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE SET NULL;

-- Add missing columns to quick_actions table
ALTER TABLE quick_actions 
ADD COLUMN user_id INT DEFAULT 1,
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Update existing quick actions to be associated with admin user
UPDATE quick_actions SET user_id = 1;

-- Create payment_methods table if it doesn't exist
CREATE TABLE IF NOT EXISTS payment_methods (
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

-- Create savings_accounts table if it doesn't exist
CREATE TABLE IF NOT EXISTS savings_accounts (
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

-- Create notifications table if it doesn't exist
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default payment methods for admin user
INSERT INTO payment_methods (user_id, name, type, bank_name, icon, color) VALUES
(1, 'Cash', 'cash', NULL, 'fas fa-money-bill-wave', '#28a745'),
(1, 'Credit Card', 'credit_card', NULL, 'fas fa-credit-card', '#007bff'),
(1, 'Online Payment', 'online', NULL, 'fas fa-globe', '#17a2b8'),
(1, 'Bank Transfer', 'online', NULL, 'fas fa-university', '#6f42c1');

-- Insert default savings accounts for admin user
INSERT INTO savings_accounts (user_id, name, type, bank_name, current_balance, target_balance, icon, color) VALUES
(1, 'Cash Savings', 'cash', NULL, 0, 0, 'fas fa-money-bill-wave', '#28a745'),
(1, 'BPI Savings', 'bank', 'BPI', 0, 0, 'fas fa-university', '#007bff'),
(1, 'Unionbank Savings', 'bank', 'Unionbank', 0, 0, 'fas fa-university', '#6f42c1'),
(1, 'GCash Wallet', 'digital', 'GCash', 0, 0, 'fas fa-mobile-alt', '#20c997');

-- Update unique constraints for months and weeks
ALTER TABLE months DROP INDEX unique_month;
ALTER TABLE months ADD UNIQUE KEY unique_month (user_id, year, month);

ALTER TABLE weeks DROP INDEX unique_week;
ALTER TABLE weeks ADD UNIQUE KEY unique_week (user_id, year, month, week_number);
