-- MySQL 5.2.2 Compatible Database Schema
-- Budget Planner Application
-- Compatible with older MySQL versions

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_approved` tinyint(1) DEFAULT '0',
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `security_pin`
CREATE TABLE `security_pin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pin_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `months`
CREATE TABLE `months` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `weeks`
CREATE TABLE `weeks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `week_number` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `month_id` (`month_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `expense_categories`
CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `color` varchar(7) DEFAULT '#007bff',
  `icon` varchar(50) DEFAULT 'fas fa-tag',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `income_sources`
CREATE TABLE `income_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `month_id` int(11) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `month_id` (`month_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `expenses`
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `month_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text,
  `due_date` date DEFAULT NULL,
  `is_bill` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `month_id` (`month_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `payment_methods`
CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `savings_accounts`
CREATE TABLE `savings_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `target_amount` decimal(10,2) DEFAULT NULL,
  `current_amount` decimal(10,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `actual_expenses`
CREATE TABLE `actual_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '1',
  `expense_id` int(11) NOT NULL,
  `month_id` int(11) NOT NULL,
  `week_id` int(11) DEFAULT NULL,
  `actual_amount` decimal(10,2) NOT NULL,
  `date_paid` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method_id` int(11) DEFAULT NULL,
  `savings_account_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `expense_id` (`expense_id`),
  KEY `month_id` (`month_id`),
  KEY `week_id` (`week_id`),
  KEY `payment_method_id` (`payment_method_id`),
  KEY `savings_account_id` (`savings_account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `quick_actions`
CREATE TABLE `quick_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `notifications`
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table structure for table `migrations`
CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `month_id` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Insert default data
INSERT INTO `users` (`username`, `email`, `password_hash`, `is_approved`, `is_admin`) VALUES
('admin', 'admin@budgetplanner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

INSERT INTO `security_pin` (`user_id`, `pin_hash`, `is_active`) VALUES
(1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Sample months for 2025
INSERT INTO `months` (`user_id`, `start_date`, `end_date`, `is_active`) VALUES
(1, '2025-01-01', '2025-01-31', 1),
(1, '2025-02-01', '2025-02-28', 1),
(1, '2025-03-01', '2025-03-31', 1),
(1, '2025-04-01', '2025-04-30', 1),
(1, '2025-05-01', '2025-05-31', 1),
(1, '2025-06-01', '2025-06-30', 1),
(1, '2025-07-01', '2025-07-31', 1),
(1, '2025-08-01', '2025-08-31', 1),
(1, '2025-09-01', '2025-09-30', 1),
(1, '2025-10-01', '2025-10-31', 1),
(1, '2025-11-01', '2025-11-30', 1),
(1, '2025-12-01', '2025-12-31', 1);

-- Sample weeks for January 2025
INSERT INTO `weeks` (`month_id`, `start_date`, `end_date`, `week_number`) VALUES
(1, '2025-01-01', '2025-01-05', 1),
(1, '2025-01-06', '2025-01-12', 2),
(1, '2025-01-13', '2025-01-19', 3),
(1, '2025-01-20', '2025-01-26', 4),
(1, '2025-01-27', '2025-01-31', 5);

-- Default expense categories
INSERT INTO `expense_categories` (`user_id`, `name`, `description`, `color`, `icon`) VALUES
(1, 'Food & Dining', 'Groceries, restaurants, and food delivery', '#28a745', 'fas fa-utensils'),
(1, 'Transportation', 'Gas, public transport, and vehicle maintenance', '#007bff', 'fas fa-car'),
(1, 'Housing', 'Rent, mortgage, and home maintenance', '#6f42c1', 'fas fa-home'),
(1, 'Utilities', 'Electricity, water, internet, and phone bills', '#fd7e14', 'fas fa-bolt'),
(1, 'Healthcare', 'Medical expenses and insurance', '#e83e8c', 'fas fa-heartbeat'),
(1, 'Entertainment', 'Movies, games, and leisure activities', '#20c997', 'fas fa-gamepad'),
(1, 'Shopping', 'Clothing, electronics, and personal items', '#ffc107', 'fas fa-shopping-bag'),
(1, 'Education', 'Books, courses, and training materials', '#17a2b8', 'fas fa-graduation-cap'),
(1, 'Insurance', 'Life, health, and property insurance', '#6c757d', 'fas fa-shield-alt'),
(1, 'Savings', 'Emergency fund and long-term savings', '#28a745', 'fas fa-piggy-bank'),
(1, 'Debt Payment', 'Credit cards, loans, and debt reduction', '#dc3545', 'fas fa-credit-card'),
(1, 'Gifts & Donations', 'Charitable giving and gifts', '#fd7e14', 'fas fa-gift'),
(1, 'Travel', 'Vacations and business trips', '#20c997', 'fas fa-plane'),
(1, 'Pets', 'Pet food, vet bills, and pet supplies', '#6f42c1', 'fas fa-paw'),
(1, 'Miscellaneous', 'Other expenses not categorized', '#6c757d', 'fas fa-ellipsis-h');

-- Default payment methods
INSERT INTO `payment_methods` (`user_id`, `name`, `description`) VALUES
(1, 'Cash', 'Physical cash payments'),
(1, 'Credit Card', 'Credit card payments'),
(1, 'Debit Card', 'Debit card payments'),
(1, 'Online Banking', 'Online bank transfers'),
(1, 'Savings Account', 'Direct from savings'),
(1, 'Mobile Payment', 'Mobile payment apps'),
(1, 'Check', 'Check payments'),
(1, 'Other', 'Other payment methods');

-- Default savings accounts
INSERT INTO `savings_accounts` (`user_id`, `name`, `description`, `target_amount`, `current_amount`) VALUES
(1, 'Emergency Fund', 'Emergency savings fund', 10000.00, 0.00),
(1, 'Vacation Fund', 'Savings for vacations and travel', 5000.00, 0.00),
(1, 'Investment Account', 'Long-term investment savings', NULL, 0.00),
(1, 'Cash Savings', 'Physical cash savings', 1000.00, 0.00),
(1, 'Digital Wallet', 'Digital payment wallet', 500.00, 0.00);

-- Sample income sources for January 2025
INSERT INTO `income_sources` (`user_id`, `name`, `amount`, `month_id`) VALUES
(1, 'Salary', 3000.00, 1),
(1, 'Freelance Work', 500.00, 1),
(1, 'Investment Returns', 200.00, 1);

-- Sample expenses for January 2025
INSERT INTO `expenses` (`user_id`, `category_id`, `month_id`, `amount`, `description`, `due_date`, `is_bill`) VALUES
(1, 3, 1, 500.00, 'Monthly rent payment', '2025-01-01', 1),
(1, 4, 1, 150.00, 'Electricity bill', '2025-01-15', 1),
(1, 4, 1, 80.00, 'Internet service', '2025-01-20', 1),
(1, 1, 1, 200.00, 'Grocery shopping', NULL, 0),
(1, 2, 1, 50.00, 'Gas for car', NULL, 0);

COMMIT;
