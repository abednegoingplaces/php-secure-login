-- Question 21: database for user login and authentication
-- Import this file in phpMyAdmin (Import tab) or run it in the MySQL console.

CREATE DATABASE IF NOT EXISTS secure_login
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE secure_login;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    full_name     VARCHAR(100)  NOT NULL,
    username      VARCHAR(50)   NOT NULL,
    email         VARCHAR(120)  NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,   -- stores password_hash() output, never plain text
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login    DATETIME      NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- No users are inserted here on purpose.
-- Passwords must be hashed by PHP's password_hash(), so create accounts
-- with register.php, or open seed.php once to add the demo accounts.
