-- Update Bills Script for NR BUDGET Planner
-- Mark existing expenses as bills with due dates

USE budget_planner;

-- Update Meralco Bill (Electricity)
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'utility', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-20')
WHERE name = 'Meralco Bill' AND user_id = 1;

-- Update Water Bill
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'utility', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-20')
WHERE name = 'Water Bill' AND user_id = 1;

-- Update Internet
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'utility', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-20')
WHERE name = 'Internet' AND user_id = 1;

-- Update Unionbank CC
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'credit_card', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-10')
WHERE name = 'Unionbank CC' AND user_id = 1;

-- Update Security Bank CC
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'credit_card', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-20')
WHERE name = 'Security Bank CC' AND user_id = 1;

-- Update NSJBI
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'loan', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-20')
WHERE name = 'NSJBI' AND user_id = 1;

-- Update Pag-ibig Loan
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'loan', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-30')
WHERE name = 'Pag-ibig Loan' AND user_id = 1;

-- Update Insurance
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'insurance', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-30')
WHERE name = 'Insurance' AND user_id = 1;


-- Update i-cloud
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'subscription', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
WHERE name = 'i-cloud' AND user_id = 1;

-- Update Youtube
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'subscription', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
WHERE name = 'Youtube' AND user_id = 1;

-- Update Cursor
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'subscription', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
WHERE name = 'Cursor' AND user_id = 1;

-- Update Smart Postpaid
UPDATE expenses 
SET is_bill = TRUE, 
    bill_type = 'subscription', 
    due_date = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
WHERE name = 'Smart Postpaid' AND user_id = 1;

-- Show updated bills
SELECT 
    name, 
    bill_type, 
    due_date, 
    is_bill,
    budgeted_amount
FROM expenses 
WHERE is_bill = TRUE AND user_id = 1 
ORDER BY due_date;
