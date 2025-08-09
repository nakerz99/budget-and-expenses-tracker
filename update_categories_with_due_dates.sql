-- Update Expense Categories with Due Date Information
-- Mark categories that have bills with due dates

USE budget_planner;

-- First, let's see which categories have bills
CREATE TEMPORARY TABLE categories_with_bills AS
SELECT DISTINCT ec.id 
FROM expense_categories ec 
JOIN expenses e ON ec.id = e.category_id 
WHERE e.is_bill = 1 AND e.user_id = 1;

-- Update categories that have bills with due dates (with existing description)
UPDATE expense_categories 
SET description = CONCAT(description, ' (Has due dates)')
WHERE id IN (SELECT id FROM categories_with_bills) 
AND description IS NOT NULL;

-- Update categories that have bills but no description
UPDATE expense_categories 
SET description = 'Has due dates'
WHERE id IN (SELECT id FROM categories_with_bills) 
AND description IS NULL;

-- Drop temporary table
DROP TEMPORARY TABLE categories_with_bills;

-- Show updated categories
SELECT 
    ec.id,
    ec.name,
    ec.description,
    ec.color,
    CASE 
        WHEN EXISTS (SELECT 1 FROM expenses e WHERE e.category_id = ec.id AND e.is_bill = 1 AND e.user_id = 1) 
        THEN 'Yes' 
        ELSE 'No' 
    END as has_due_dates,
    COUNT(e.id) as total_expenses,
    COUNT(CASE WHEN e.is_bill = 1 THEN 1 END) as bill_count
FROM expense_categories ec
LEFT JOIN expenses e ON ec.id = e.category_id AND e.user_id = 1
WHERE ec.user_id = 1
GROUP BY ec.id, ec.name, ec.description, ec.color
ORDER BY has_due_dates DESC, ec.name;
