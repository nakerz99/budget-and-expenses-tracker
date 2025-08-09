-- Database Cleanup Script
-- Removes duplicate entries from expenses table
-- Run this script if duplicates are found

-- Remove all Netflix entries (completely removed)
DELETE FROM expenses WHERE name LIKE '%netflix%' OR name LIKE '%Netflix%';

-- Remove duplicate Water Bill entries (keep the one with higher amount)
DELETE FROM expenses WHERE id = 5; -- Water Bill (350.00)
-- Kept: Water Bill (800.00) with id = 28

-- Verify no duplicates remain
-- SELECT name, COUNT(*) as count FROM expenses GROUP BY name HAVING COUNT(*) > 1;
