-- ============================================================
-- GlowCare – Personal Skincare Assistant
-- Database Schema Script
-- RDBMS: MySQL 8.0+
-- ============================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS glowcare
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE glowcare;

-- ============================================================
-- 1. USERS TABLE
-- ============================================================
CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    phone         VARCHAR(20)   DEFAULT NULL,
    date_of_birth DATE          DEFAULT NULL,
    gender        ENUM('Male','Female','Other','Prefer not to say') DEFAULT NULL,
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    profile_image VARCHAR(255)  DEFAULT NULL,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- ============================================================
-- 2. SKIN ASSESSMENTS TABLE
-- ============================================================
CREATE TABLE skin_assessments (
    assessment_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT           NOT NULL,
    skin_type       ENUM('oily','dry','combination','normal','sensitive') NOT NULL,
    oily_feeling    ENUM('yes','no','sometimes') NOT NULL,
    dry_tight       ENUM('yes','no','sometimes') NOT NULL,
    easily_irritated ENUM('yes','no','sometimes') NOT NULL,
    concerns        VARCHAR(500)  DEFAULT NULL,  -- comma-separated values
    assessment_date DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_assessment_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_assessment_user (user_id)
) ENGINE=InnoDB;

-- ============================================================
-- 3. CATEGORIES TABLE
-- ============================================================
CREATE TABLE categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description   VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 4. PRODUCTS TABLE
-- ============================================================
CREATE TABLE products (
    product_id      INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(200)  NOT NULL,
    brand           VARCHAR(100)  NOT NULL DEFAULT 'GlowCare Clinical',
    category_id     INT           NOT NULL,
    price           DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    description     TEXT          DEFAULT NULL,
    ingredients     TEXT          DEFAULT NULL,
    usage_instructions TEXT       DEFAULT NULL,
    precautions     TEXT          DEFAULT NULL,
    skin_type       ENUM('oily','dry','combination','normal','sensitive','all') NOT NULL DEFAULT 'all',
    image_path      VARCHAR(255)  DEFAULT NULL,
    is_active       TINYINT(1)    NOT NULL DEFAULT 1,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_category
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_product_category (category_id),
    INDEX idx_product_skin_type (skin_type),
    INDEX idx_product_price (price)
) ENGINE=InnoDB;

-- ============================================================
-- 5. ROUTINES TABLE
-- ============================================================
CREATE TABLE routines (
    routine_id    INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT          NOT NULL,
    routine_name  VARCHAR(100) NOT NULL,
    routine_type  ENUM('morning','evening') NOT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_routine_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_routine_user (user_id),
    INDEX idx_routine_type (routine_type)
) ENGINE=InnoDB;

-- ============================================================
-- 6. ROUTINE STEPS TABLE
-- ============================================================
CREATE TABLE routine_steps (
    step_id       INT AUTO_INCREMENT PRIMARY KEY,
    routine_id    INT          NOT NULL,
    product_id    INT          DEFAULT NULL,
    step_name     VARCHAR(150) NOT NULL,
    step_order    INT          NOT NULL DEFAULT 1,
    is_completed  TINYINT(1)   NOT NULL DEFAULT 0,
    completed_date DATE        DEFAULT NULL,
    CONSTRAINT fk_step_routine
        FOREIGN KEY (routine_id) REFERENCES routines(routine_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_step_product
        FOREIGN KEY (product_id) REFERENCES products(product_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_step_routine (routine_id),
    INDEX idx_step_order (step_order)
) ENGINE=InnoDB;

-- ============================================================
-- 7. PROGRESS LOGS TABLE
-- ============================================================
CREATE TABLE progress_logs (
    log_id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT          NOT NULL,
    routine_step_id   INT          DEFAULT NULL,
    completion_status ENUM('completed','skipped','partial') NOT NULL DEFAULT 'completed',
    log_date          DATE         NOT NULL,
    notes             TEXT         DEFAULT NULL,
    skin_feeling      ENUM('great','good','okay','bad','terrible') DEFAULT NULL,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_progress_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_progress_step
        FOREIGN KEY (routine_step_id) REFERENCES routine_steps(step_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_progress_user (user_id),
    INDEX idx_progress_date (log_date)
) ENGINE=InnoDB;

-- ============================================================
-- 8. WISHLISTS TABLE
-- ============================================================
CREATE TABLE wishlists (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT      NOT NULL,
    product_id  INT      NOT NULL,
    date_added  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wishlist_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_wishlist_product
        FOREIGN KEY (product_id) REFERENCES products(product_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uk_wishlist_user_product (user_id, product_id),
    INDEX idx_wishlist_user (user_id)
) ENGINE=InnoDB;

-- ============================================================
-- 9. ORDERS TABLE
-- ============================================================
CREATE TABLE orders (
    order_id         VARCHAR(50)   PRIMARY KEY,
    user_id          INT           NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    discount_amount  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    shipping_fee     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method   VARCHAR(50)   NOT NULL,
    payment_status   ENUM('paid','pending','failed') NOT NULL DEFAULT 'paid',
    order_status     VARCHAR(50)   NOT NULL DEFAULT 'Confirmed - Preparing Dispatch',
    shipping_address TEXT          NOT NULL,
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_orders_user (user_id)
) ENGINE=InnoDB;

-- ============================================================
-- 10. ORDER ITEMS TABLE
-- ============================================================
CREATE TABLE order_items (
    item_id    INT AUTO_INCREMENT PRIMARY KEY,
    order_id   VARCHAR(50)   NOT NULL,
    product_id INT           NOT NULL,
    quantity   INT           NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_product
        FOREIGN KEY (product_id) REFERENCES products(product_id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_order_items_order (order_id)
) ENGINE=InnoDB;

-- ============================================================
-- VIEWS (for demonstration of SQL concepts)
-- ============================================================

-- View: User profile with latest skin assessment
CREATE OR REPLACE VIEW vw_user_profile AS
SELECT
    u.user_id,
    u.full_name,
    u.email,
    u.gender,
    u.created_at AS member_since,
    sa.skin_type,
    sa.concerns,
    sa.assessment_date
FROM users u
LEFT JOIN skin_assessments sa ON u.user_id = sa.user_id
    AND sa.assessment_date = (
        SELECT MAX(sa2.assessment_date)
        FROM skin_assessments sa2
        WHERE sa2.user_id = u.user_id
    );

-- View: Product catalogue with category names
CREATE OR REPLACE VIEW vw_product_catalogue AS
SELECT
    p.product_id,
    p.name AS product_name,
    c.category_name,
    p.price,
    p.description,
    p.skin_type,
    p.image_path,
    p.is_active
FROM products p
JOIN categories c ON p.category_id = c.category_id
WHERE p.is_active = 1;

-- View: Weekly progress summary per user
CREATE OR REPLACE VIEW vw_weekly_progress AS
SELECT
    pl.user_id,
    YEARWEEK(pl.log_date, 1) AS year_week,
    COUNT(*) AS total_logs,
    SUM(CASE WHEN pl.completion_status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
    SUM(CASE WHEN pl.completion_status = 'skipped' THEN 1 ELSE 0 END) AS skipped_count,
    MIN(pl.log_date) AS week_start,
    MAX(pl.log_date) AS week_end
FROM progress_logs pl
GROUP BY pl.user_id, YEARWEEK(pl.log_date, 1);
