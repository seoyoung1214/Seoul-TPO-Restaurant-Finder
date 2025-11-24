-- Drop All Tables Script
-- Team: team12

USE team12;

-- Disable foreign key checks to allow dropping in any order
SET FOREIGN_KEY_CHECKS = 0;

-- Drop views first
DROP VIEW IF EXISTS v_restaurant_details;

-- Drop all tables
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS restaurant_cuisines;
DROP TABLE IF EXISTS restaurants;
DROP TABLE IF EXISTS cuisines;
DROP TABLE IF EXISTS districts;
DROP TABLE IF EXISTS occasions;
DROP TABLE IF EXISTS time_slots;
DROP TABLE IF EXISTS users;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Display confirmation
SELECT 'All tables have been dropped successfully!' as message;
