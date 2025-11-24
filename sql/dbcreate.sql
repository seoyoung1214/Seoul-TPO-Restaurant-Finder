-- Seoul TPO Restaurant Finder Database Schema
-- Team: team12

-- Drop existing tables if they exist (for clean installation)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS restaurant_cuisines;
DROP TABLE IF EXISTS restaurants;
DROP TABLE IF EXISTS cuisines;
DROP TABLE IF EXISTS districts;
DROP TABLE IF EXISTS occasions;
DROP TABLE IF EXISTS time_slots;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS team12 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE team12;

-- 1. users table
CREATE TABLE users (
    user_id        INT AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(50) NOT NULL UNIQUE,
    password       VARCHAR(255) NOT NULL,
    gender         ENUM('M','F','O') NULL,
    birth_year     INT NULL,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. districts table (Seoul districts)
CREATE TABLE districts (
    district_id    INT AUTO_INCREMENT PRIMARY KEY,
    district_name  VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. cuisines table (food types)
CREATE TABLE cuisines (
    cuisine_id     INT AUTO_INCREMENT PRIMARY KEY,
    cuisine_name   VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. occasions table (dining purposes)
CREATE TABLE occasions (
    occasion_id    INT AUTO_INCREMENT PRIMARY KEY,
    occasion_name  VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. time_slots table (time categories)
CREATE TABLE time_slots (
    time_slot_id   INT AUTO_INCREMENT PRIMARY KEY,
    time_of_day    ENUM('breakfast','lunch','afternoon','dinner','late_night') NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. restaurants table
CREATE TABLE restaurants (
    restaurant_id   INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    address         VARCHAR(200) NOT NULL,
    description     TEXT,
    price           INT UNSIGNED NOT NULL,
    opening_hours   VARCHAR(255),
    closed_day      VARCHAR(20),
    avg_rating      DECIMAL(3,2) DEFAULT 0.0,
    review_count    INT DEFAULT 0,
    district_id     INT NOT NULL,
    FOREIGN KEY (district_id) REFERENCES districts(district_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. restaurant_cuisines table (N:M mapping)
CREATE TABLE restaurant_cuisines (
    restaurant_id  INT NOT NULL,
    cuisine_id     INT NOT NULL,
    PRIMARY KEY (restaurant_id, cuisine_id),
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(restaurant_id) ON DELETE CASCADE,
    FOREIGN KEY (cuisine_id)    REFERENCES cuisines(cuisine_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. reviews table (Fact table for OLAP queries)
CREATE TABLE reviews (
    review_id        INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT NOT NULL,
    restaurant_id    INT NOT NULL,
    occasion_id      INT NOT NULL,
    time_slot_id     INT NOT NULL,
    rating_score     TINYINT NOT NULL CHECK (rating_score BETWEEN 1 AND 5),
    spend_amount     INT UNSIGNED NOT NULL,
    comment          TEXT,
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)       REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(restaurant_id) ON DELETE CASCADE,
    FOREIGN KEY (occasion_id)   REFERENCES occasions(occasion_id) ON DELETE CASCADE,
    FOREIGN KEY (time_slot_id)  REFERENCES time_slots(time_slot_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Success message
SELECT 'Database schema created successfully!' as message;
