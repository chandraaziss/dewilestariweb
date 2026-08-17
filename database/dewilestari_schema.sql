-- ============================================================
-- DATABASE IMPLEMENTATION SCRIPT
-- Application : Sistem Informasi Penjualan Toko Dewi Lestari 2
-- DBMS        : MySQL / MariaDB (InnoDB Engine)
-- Database    : dewilestari
-- Charset     : utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS `dewilestari` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `dewilestari`;

-- Disable foreign key checks for clean table creation
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. Table: users
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `plain_password` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('admin', 'cust') NOT NULL DEFAULT 'cust',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. Table: user_addresses
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `label` VARCHAR(50) DEFAULT 'Alamat Utama',
  `receiver_name` VARCHAR(255) NOT NULL,
  `receiver_phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_addresses_user_id_foreign` (`user_id`),
  CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. Table: suppliers
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `items` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. Table: supplier_stocks
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `supplier_stocks`;
CREATE TABLE `supplier_stocks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `entry_date` DATE DEFAULT NULL,
  `variants` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variants`)),
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `supplier_stocks_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `supplier_stocks_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. Table: orders
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_number` VARCHAR(50) NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `delivery_option` VARCHAR(50) NOT NULL DEFAULT 'delivery',
  `delivery_address` TEXT DEFAULT NULL,
  `delivery_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'midtrans',
  `payment_status` VARCHAR(50) NOT NULL DEFAULT 'unpaid',
  `midtrans_order_id` VARCHAR(100) DEFAULT NULL,
  `snap_token` VARCHAR(255) DEFAULT NULL,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `tracking_ticket_id` VARCHAR(50) DEFAULT NULL,
  `tracking_status` VARCHAR(50) DEFAULT 'diproses',
  `shipped_at` TIMESTAMP NULL DEFAULT NULL,
  `almost_arrived_at` TIMESTAMP NULL DEFAULT NULL,
  `delivered_at` TIMESTAMP NULL DEFAULT NULL,
  `delivery_proof` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. Table: order_items
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED DEFAULT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `weight` VARCHAR(50) DEFAULT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `buy_price` DECIMAL(12,2) DEFAULT 0.00,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `supplier_stocks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. Table: supplier_orders
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `supplier_orders`;
CREATE TABLE `supplier_orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `items` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `notes` TEXT DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `supplier_orders_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `supplier_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. Table: supplier_returns
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `supplier_returns`;
CREATE TABLE `supplier_returns` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_number` VARCHAR(50) NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `supplier_order_id` BIGINT UNSIGNED DEFAULT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `weight` VARCHAR(50) DEFAULT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `reason` TEXT NOT NULL,
  `proof_image` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `admin_notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `supplier_returns_supplier_id_foreign` (`supplier_id`),
  KEY `supplier_returns_supplier_order_id_foreign` (`supplier_order_id`),
  CONSTRAINT `supplier_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_returns_supplier_order_id_foreign` FOREIGN KEY (`supplier_order_id`) REFERENCES `supplier_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. Table: stock_logs
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `stock_logs`;
CREATE TABLE `stock_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_stock_id` BIGINT UNSIGNED NOT NULL,
  `weight` VARCHAR(50) DEFAULT NULL,
  `type` ENUM('in', 'out', 'adjust', 'return') NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `stock_logs_supplier_stock_id_foreign` (`supplier_stock_id`),
  CONSTRAINT `stock_logs_supplier_stock_id_foreign` FOREIGN KEY (`supplier_stock_id`) REFERENCES `supplier_stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. Table: ratings
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `order_item_id` BIGINT UNSIGNED DEFAULT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `review` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ratings_order_id_foreign` (`order_id`),
  KEY `ratings_product_id_foreign` (`product_id`),
  KEY `ratings_user_id_foreign` (`user_id`),
  CONSTRAINT `ratings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `supplier_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. Table: messages
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(100) NOT NULL,
  `sender` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA UNTUK PENGUJIAN DAN DEMO
-- ============================================================

-- 1. Seed Users
INSERT INTO `users` (`id`, `name`, `email`, `password`, `plain_password`, `phone`, `address`, `role`) VALUES
(1, 'Administrator', 'admin@dewilestari.com', '$2y$12$K8yXpX3xXp.qN/5H.4y8yO7Z4x2z4y5z.z4y5z.z4y5z.z4y5z', 'admin123', '081234567890', 'Jl. Merdeka No. 10, Jakarta', 'admin'),
(2, 'Siti Aminah', 'siti@gmail.com', '$2y$12$K8yXpX3xXp.qN/5H.4y8yO7Z4x2z4y5z.z4y5z.z4y5z.z4y5z', 'pelanggan123', '081987654321', 'Jl. Kenanga No. 45, Bandung', 'cust');

-- 2. Seed User Addresses
INSERT INTO `user_addresses` (`id`, `user_id`, `label`, `receiver_name`, `receiver_phone`, `address`, `city`, `is_primary`) VALUES
(1, 2, 'Rumah Utama', 'Siti Aminah', '081987654321', 'Jl. Kenanga No. 45, RT 02/RW 05', 'Bandung', 1);

-- 3. Seed Suppliers
INSERT INTO `suppliers` (`id`, `name`, `slug`, `phone`, `email`, `address`, `items`) VALUES
(1, 'PT Jaya Rasa Nusantara', 'jaya-rasa', '6281234567893', 'sales@jayarasa.co.id', 'Jl. Raya Industri No. 88, Cikarang', '[{"name":"Kripik Tempe Premium","unit":"pcs"},{"name":"Kripik Pisang Cokelat","unit":"pcs"}]');

-- 4. Seed Supplier Stocks (Catalog Products)
INSERT INTO `supplier_stocks` (`id`, `supplier_id`, `item_name`, `description`, `image_path`, `is_active`, `entry_date`, `variants`) VALUES
(1, 1, 'Kripik Tempe Premium', 'Kripik tempe renyah gurih asli racikan khas Toko Dewi Lestari 2.', 'storage/products/kripik_tempe.jpg', 1, '2026-08-01', '[{"weight":"250g","initial_quantity":50,"available_quantity":45,"expiry_date":"2026-12-31","price":15000,"hpp":10000},{"weight":"500g","initial_quantity":30,"available_quantity":28,"expiry_date":"2026-12-31","price":28000,"hpp":19000}]'),
(2, 1, 'Kripik Pisang Cokelat', 'Kripik pisang manis salut cokelat lumer berkualitas tinggi.', 'storage/products/kripik_pisang.jpg', 1, '2026-08-01', '[{"weight":"200g","initial_quantity":40,"available_quantity":35,"expiry_date":"2026-11-15","price":18000,"hpp":12000}]');

-- 5. Seed Orders
INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_phone`, `delivery_option`, `delivery_address`, `delivery_cost`, `total_amount`, `status`, `payment_method`, `payment_status`, `tracking_ticket_id`, `tracking_status`) VALUES
(1, 2, 'ORD-20260809-0001', 'Siti Aminah', '081987654321', 'delivery', 'Jl. Kenanga No. 45, Bandung', 12000.00, 42000.00, 'paid', 'midtrans', 'paid', 'TKT-2608-00001', 'dikirim');

-- 6. Seed Order Items
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `item_name`, `weight`, `quantity`, `price`, `buy_price`, `subtotal`) VALUES
(1, 1, 1, 'Kripik Tempe Premium', '250g', 2, 15000.00, 10000.00, 30000.00);

-- 7. Seed Supplier Orders (PO)
INSERT INTO `supplier_orders` (`id`, `invoice_number`, `supplier_id`, `items`, `notes`, `status`) VALUES
(1, 'PO/SUP-001/202608/0001', 1, '[{"item_name":"Kripik Tempe Premium","weight":"250g","quantity":100,"buy_price":10000}]', 'Pengadaan stok tambahan menyambut libur nasional.', 'pending');

-- 8. Seed Stock Logs
INSERT INTO `stock_logs` (`id`, `supplier_stock_id`, `weight`, `type`, `quantity`, `description`) VALUES
(1, 1, '250g', 'in', 50, 'Stok Awal Masuk Supplier'),
(2, 1, '250g', 'out', 2, 'Penjualan order ORD-20260809-0001');

-- 9. Seed Ratings
INSERT INTO `ratings` (`id`, `order_id`, `order_item_id`, `product_id`, `user_id`, `rating`, `review`) VALUES
(1, 1, 1, 1, 2, 5, 'Kripik tempenya sangat renyah dan gurih, pengiriman sangat cepat!');

-- 10. Seed Messages
INSERT INTO `messages` (`id`, `session_id`, `sender`, `message`, `is_read`) VALUES
(1, 'sess_siti_001', 'user', 'Halo admin, apakah Kripik Tempe stoknya ready?', 1),
(2, 'sess_siti_001', 'admin', 'Halo Kak Siti, ready banyak ya kak. Silahkan diorder!', 1);
