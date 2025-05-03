-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               9.0.1 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.5.0.6677
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for threestycake
CREATE DATABASE IF NOT EXISTS `threestycake` /*!40100 DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `threestycake`;

-- Dumping structure for table threestycake.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.cache: ~12 rows (approximately)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('threestycake_cache_1b6453892473a467d07372d45eb05abc2031647a', 'i:1;', 1746230932),
	('threestycake_cache_1b6453892473a467d07372d45eb05abc2031647a:timer', 'i:1746230932;', 1746230932),
	('threestycake_cache_356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1745208029),
	('threestycake_cache_356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1745208029;', 1745208029),
	('threestycake_cache_77de68daecd823babbb58edb1c8e14d7106e83bb', 'i:1;', 1745211645),
	('threestycake_cache_77de68daecd823babbb58edb1c8e14d7106e83bb:timer', 'i:1745211645;', 1745211645),
	('threestycake_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:1;', 1745836627),
	('threestycake_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1745836627;', 1745836627),
	('threestycake_cache_livewire-rate-limiter:3f564426c74efa049b7054c9eaca7cf4aa68be8d', 'i:1;', 1745848798),
	('threestycake_cache_livewire-rate-limiter:3f564426c74efa049b7054c9eaca7cf4aa68be8d:timer', 'i:1745848798;', 1745848798),
	('threestycake_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1746239099),
	('threestycake_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1746239099;', 1746239099),
	('threestycake_cache_theme', 's:6:"sunset";', 2060846482),
	('threestycake_cache_theme_color', 's:4:"rose";', 2060846484);

-- Dumping structure for table threestycake.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.cache_locks: ~0 rows (approximately)
DELETE FROM `cache_locks`;

-- Dumping structure for table threestycake.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_user_id_foreign` (`user_id`),
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.customers: ~3 rows (approximately)
DELETE FROM `customers`;
INSERT INTO `customers` (`id`, `name`, `phone_number`, `address`, `created_at`, `updated_at`, `user_id`) VALUES
	(1, 'Lolita Collins', '484-919-9476', '5732 Heaney Flats', '2025-04-21 04:51:18', '2025-04-21 04:51:18', 2),
	(2, 'Customer 1', '081222333444', 'Denpasar', '2025-04-21 04:57:06', '2025-04-21 04:57:06', 3),
	(3, 'Ole Kris', '477-767-3613', '30865 Ebert Mount', '2025-04-28 13:58:58', '2025-04-28 13:58:58', 4);

-- Dumping structure for table threestycake.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table threestycake.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table threestycake.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table threestycake.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.migrations: ~22 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_04_18_210326_create_customers_table', 1),
	(5, '2025_04_19_001926_add_custom_fields_to_users_table', 1),
	(6, '2025_04_19_001927_add_avatar_url_to_users_table', 1),
	(7, '2025_04_19_014005_add_role_to_users', 1),
	(8, '2025_04_19_023517_create_suppliers_table', 1),
	(9, '2025_04_19_024727_create_products_table', 1),
	(10, '2025_04_19_024756_create_product_stock_table', 1),
	(11, '2025_04_19_031400_create_orders_table', 1),
	(12, '2025_04_19_031405_create_order_items_table', 1),
	(13, '2025_04_19_134837_create_purchases_table', 1),
	(14, '2025_04_19_134843_create_purchase_items_table', 1),
	(15, '2025_04_20_173501_create_payments_table', 1),
	(16, '2025_04_20_183612_create_product_customizations_table', 1),
	(17, '2025_04_20_183841_create_order_item_customizations_table', 1),
	(18, '2025_04_21_094524_change_product_customization_id_to_product', 2),
	(19, '2025_04_21_121236_add_user_id_to_customer', 2),
	(20, '2025_04_21_121653_remove_email_from_customers', 3),
	(21, '2025_04_21_215056_change_user_id_into_customer_id_to_orders_table', 4),
	(22, '2025_04_28_174802_create_settings_table', 5),
	(23, '2025_05_03_000154_change_product_stock_id_into_product_id_to_order_items', 5),
	(25, '2025_05_03_073035_create_order_item_product_stocks_table', 6),
	(26, '2025_05_03_101347_add_description_to_settings_table', 7);

-- Dumping structure for table threestycake.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_date` date NOT NULL,
  `status` enum('pending','paid','cancelled','delivered') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.orders: ~1 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`id`, `order_date`, `status`, `created_at`, `updated_at`, `customer_id`) VALUES
	(8, '2025-05-03', 'paid', '2025-05-02 16:08:11', '2025-05-02 23:38:36', 3),
	(9, '2025-05-03', 'paid', '2025-05-03 00:02:54', '2025-05-03 00:08:21', 3);

-- Dumping structure for table threestycake.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` int NOT NULL,
  `custom_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Catatan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `product_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.order_items: ~0 rows (approximately)
DELETE FROM `order_items`;
INSERT INTO `order_items` (`id`, `order_id`, `quantity`, `price`, `custom_note`, `created_at`, `updated_at`, `product_id`) VALUES
	(9, 8, 1, 175000, NULL, '2025-05-02 23:00:29', '2025-05-02 23:00:29', 2),
	(11, 8, 2, 175000, NULL, '2025-05-02 23:23:29', '2025-05-02 23:24:31', 2),
	(12, 8, 3, 150000, NULL, '2025-05-02 23:24:46', '2025-05-02 23:24:46', 1),
	(13, 9, 2, 165000, NULL, '2025-05-03 00:03:21', '2025-05-03 00:03:21', 3),
	(14, 9, 3, 150000, NULL, '2025-05-03 00:03:48', '2025-05-03 00:03:48', 1),
	(15, 9, 1, 165000, NULL, '2025-05-03 00:04:02', '2025-05-03 00:04:02', 3);

-- Dumping structure for table threestycake.order_item_customizations
CREATE TABLE IF NOT EXISTS `order_item_customizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint unsigned NOT NULL,
  `product_customization_id` bigint unsigned NOT NULL,
  `customization_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nilai Kustomisasi',
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Harga Kustomisasi',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status Kustomisasi',
  `custom_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Catatan Kustomisasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_item_customizations_order_item_id_foreign` (`order_item_id`),
  KEY `order_item_customizations_product_customization_id_foreign` (`product_customization_id`),
  CONSTRAINT `order_item_customizations_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_item_customizations_product_customization_id_foreign` FOREIGN KEY (`product_customization_id`) REFERENCES `product_customizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.order_item_customizations: ~0 rows (approximately)
DELETE FROM `order_item_customizations`;
INSERT INTO `order_item_customizations` (`id`, `order_item_id`, `product_customization_id`, `customization_value`, `price`, `status`, `custom_note`, `created_at`, `updated_at`) VALUES
	(12, 9, 3, '1', '10000', NULL, 'tidak ada', '2025-05-02 23:01:31', '2025-05-02 23:01:31'),
	(13, 11, 3, '1', '10000', NULL, NULL, '2025-05-02 23:24:23', '2025-05-02 23:24:23'),
	(14, 12, 1, '1', '20000', NULL, NULL, '2025-05-02 23:24:53', '2025-05-02 23:24:53'),
	(15, 12, 2, '1', '30000', NULL, NULL, '2025-05-02 23:25:01', '2025-05-02 23:25:01');

-- Dumping structure for table threestycake.order_item_product_stocks
CREATE TABLE IF NOT EXISTS `order_item_product_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint unsigned NOT NULL,
  `product_stock_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_item_product_stocks_order_item_id_foreign` (`order_item_id`),
  KEY `order_item_product_stocks_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `order_item_product_stocks_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_item_product_stocks_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `product_stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.order_item_product_stocks: ~0 rows (approximately)
DELETE FROM `order_item_product_stocks`;
INSERT INTO `order_item_product_stocks` (`id`, `order_item_id`, `product_stock_id`, `created_at`, `updated_at`) VALUES
	(7, 9, 8, '2025-05-02 23:59:17', '2025-05-02 23:59:17'),
	(8, 11, 9, '2025-05-02 23:59:17', '2025-05-02 23:59:17'),
	(9, 11, 7, '2025-05-02 23:59:17', '2025-05-02 23:59:17'),
	(10, 12, 1, '2025-05-02 23:59:17', '2025-05-02 23:59:17'),
	(11, 12, 2, '2025-05-02 23:59:17', '2025-05-02 23:59:17'),
	(12, 12, 3, '2025-05-02 23:59:17', '2025-05-02 23:59:17'),
	(13, 13, 15, '2025-05-03 00:16:52', '2025-05-03 00:16:52'),
	(14, 13, 14, '2025-05-03 00:16:52', '2025-05-03 00:16:52'),
	(15, 14, 10, '2025-05-03 00:16:52', '2025-05-03 00:16:52'),
	(16, 14, 11, '2025-05-03 00:16:52', '2025-05-03 00:16:52'),
	(17, 14, 12, '2025-05-03 00:16:52', '2025-05-03 00:16:52'),
	(18, 15, 13, '2025-05-03 00:16:52', '2025-05-03 00:16:52');

-- Dumping structure for table threestycake.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table threestycake.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL COMMENT 'Jumlah Pembayaran',
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Metode Pembayaran',
  `proof_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bukti Pembayaran',
  `status` enum('pending','confirmed','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Status Pembayaran',
  `payment_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tanggal Pembayaran',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Catatan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_foreign` (`order_id`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.payments: ~0 rows (approximately)
DELETE FROM `payments`;
INSERT INTO `payments` (`id`, `order_id`, `amount`, `method`, `proof_image`, `status`, `payment_date`, `notes`, `created_at`, `updated_at`) VALUES
	(5, 8, 1155000.00, 'BCA', 'payment-proofs/01JT9NPKSJZPC7S5600V7M8JJV.jpeg', 'confirmed', '2025-05-03', NULL, '2025-05-02 23:35:56', '2025-05-02 23:43:45'),
	(7, 9, 945000.00, 'Mandiri', 'payment-proofs/01JT9QH475QK06ZWBEWVKG3N50.jpg', 'confirmed', '2025-05-03', NULL, '2025-05-03 00:07:54', '2025-05-03 00:07:54');

-- Dumping structure for table threestycake.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Produk',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Deskripsi',
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Harga',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Gambar',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Status',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.products: ~3 rows (approximately)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Tiramisu Cake', 'Tiramisù (Tiramesù) adalah kue khas Italia dengan taburan bubuk kakao di atasnya. Kue ini merupakan hidangan penutup yang dimakan dengan sendok, sehingga digolongkan ke dalam hidangan "al cucchiaio" ("dengan sendok").', '150000', '01JS9N75VWFXCBKZ52PREBRV8Y.jpeg', 'active', '2025-04-20 13:11:49', '2025-04-20 13:11:49'),
	(2, 'Black Forest Cake', 'Kue black forest adalah jenis kue khas Jerman yang paling dikenal di dunia, terbuat dari bolu coklat yang dilapisi krim segar, serutan coklat dan ceri.', '175000', '01JS9NQJNRJNR5P768Y7SVA7CR.jpg', 'active', '2025-04-20 13:20:46', '2025-04-20 13:20:56'),
	(3, 'Red Velvet Cake', 'Red velvet cake atau dalam bahasa Indonesia dikenal dengan bolu beludru merah/kue beludru merah adalah bolu lapis coklat berwarna merah, cokelat kemerahan, atau kemerahan, yang dilapisi dengan keju krim putih.', '165000', '01JS9PXCZMW9J8NVB3QR3QDBWV.jpg', 'active', '2025-04-20 13:41:26', '2025-04-21 15:44:26');

-- Dumping structure for table threestycake.product_customizations
CREATE TABLE IF NOT EXISTS `product_customizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `customization_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipe Kustomisasi',
  `customization_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nilai Kustomisasi',
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Harga Kustomisasi',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status Kustomisasi',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Deskripsi Kustomisasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_customizations_product_id_foreign` (`product_id`),
  CONSTRAINT `product_customizations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.product_customizations: ~5 rows (approximately)
DELETE FROM `product_customizations`;
INSERT INTO `product_customizations` (`id`, `product_id`, `customization_type`, `customization_value`, `price`, `status`, `description`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Upsize (Large)', '32x32', '20000', 'active', 'Aspernatur minus totam delectus sit fuga maiores aperiam qui dolor. Illo vitae facilis placeat illum modi. Ipsa dolor sint nam saepe voluptatem blanditiis veniam.', '2025-04-21 01:29:37', '2025-04-21 01:29:37'),
	(2, 1, 'Upsize (Extra Large)', '40x40', '30000', 'inactive', 'Ipsam quaerat consequuntur dolore blanditiis reiciendis. Quod quae voluptatem labore necessitatibus tempore autem eaque. Quas expedita modi blanditiis.', '2025-04-21 01:38:01', '2025-04-21 01:42:05'),
	(3, 2, 'Extra Chocolate', '1', '10000', 'active', 'Cum consequuntur soluta architecto ipsa. Quas voluptatum cum dignissimos corporis temporibus illo minus perspiciatis placeat. Quidem sequi labore omnis distinctio ea velit repellat exercitationem omnis.', '2025-04-21 01:39:42', '2025-04-21 01:39:42'),
	(4, 3, 'Extra Cheese Cream', '-', '25000', 'active', 'Velit pariatur sed doloribus minus quas aut provident consequatur laborum. Itaque explicabo quaerat illum. Assumenda possimus qui laboriosam aperiam.', '2025-04-21 01:41:50', '2025-04-21 01:41:50'),
	(5, 3, 'Extra Whipping Cream', '-', '10000', 'inactive', 'Voluptatibus perspiciatis quaerat. Id inventore natus voluptatibus voluptatum excepturi magnam consectetur. Modi nesciunt atque dolore ad modi.', '2025-04-21 01:41:50', '2025-04-21 01:41:50');

-- Dumping structure for table threestycake.product_stocks
CREATE TABLE IF NOT EXISTS `product_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `expiry_date` date NOT NULL COMMENT 'Tanggal Kadaluarsa',
  `stock_in_date` date NOT NULL COMMENT 'Tanggal Masuk Stok',
  `stock_out_date` date DEFAULT NULL COMMENT 'Tanggal Keluar Stok',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_stocks_product_id_foreign` (`product_id`),
  CONSTRAINT `product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.product_stocks: ~12 rows (approximately)
DELETE FROM `product_stocks`;
INSERT INTO `product_stocks` (`id`, `product_id`, `expiry_date`, `stock_in_date`, `stock_out_date`, `created_at`, `updated_at`) VALUES
	(1, 1, '2025-04-28', '2025-04-21', '2025-05-03', '2025-04-21 02:42:16', '2025-05-02 23:59:17'),
	(2, 1, '2025-04-28', '2025-04-21', '2025-05-03', '2025-04-21 03:18:36', '2025-05-02 23:59:17'),
	(3, 1, '2025-04-28', '2025-04-21', '2025-05-03', '2025-04-21 03:18:36', '2025-05-02 23:59:17'),
	(4, 1, '2025-04-28', '2025-04-21', '2025-04-28', '2025-04-21 03:18:36', '2025-04-28 10:54:29'),
	(5, 1, '2025-04-28', '2025-04-21', '2025-04-28', '2025-04-21 03:18:36', '2025-04-28 10:54:29'),
	(6, 3, '2025-04-28', '2025-04-21', '2025-04-22', '2025-04-21 15:38:56', '2025-04-22 13:36:26'),
	(7, 2, '2025-04-28', '2025-04-22', '2025-05-03', '2025-04-21 15:39:38', '2025-05-02 23:59:17'),
	(8, 2, '2025-04-28', '2025-04-20', '2025-05-03', '2025-04-21 15:39:38', '2025-05-02 23:59:17'),
	(9, 2, '2025-04-28', '2025-04-21', '2025-05-03', '2025-04-21 15:39:38', '2025-05-02 23:59:17'),
	(10, 1, '2025-05-05', '2025-04-27', '2025-05-03', '2025-04-28 14:05:18', '2025-05-03 00:16:52'),
	(11, 1, '2025-05-05', '2025-04-28', '2025-05-03', '2025-04-28 14:05:18', '2025-05-03 00:16:52'),
	(12, 1, '2025-05-05', '2025-04-29', '2025-05-03', '2025-04-28 14:05:18', '2025-05-03 00:16:52'),
	(13, 3, '2025-05-10', '2025-05-03', '2025-05-03', '2025-05-03 00:02:32', '2025-05-03 00:16:52'),
	(14, 3, '2025-05-10', '2025-05-02', '2025-05-03', '2025-05-03 00:02:32', '2025-05-03 00:16:52'),
	(15, 3, '2025-05-10', '2025-05-01', '2025-05-03', '2025-05-03 00:02:32', '2025-05-03 00:16:52'),
	(16, 1, '2025-05-10', '2025-05-04', NULL, '2025-05-03 01:24:40', '2025-05-03 01:51:23'),
	(17, 1, '2025-05-10', '2025-05-04', NULL, '2025-05-03 01:24:40', '2025-05-03 01:51:23'),
	(18, 1, '2025-05-10', '2025-05-04', NULL, '2025-05-03 01:24:40', '2025-05-03 01:51:23');

-- Dumping structure for table threestycake.purchases
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `purchase_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.purchases: ~1 rows (approximately)
DELETE FROM `purchases`;
INSERT INTO `purchases` (`id`, `supplier_id`, `purchase_date`, `created_at`, `updated_at`) VALUES
	(1, 1, '2025-04-21', '2025-04-21 13:31:37', '2025-04-21 13:31:37');

-- Dumping structure for table threestycake.purchase_items
CREATE TABLE IF NOT EXISTS `purchase_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.purchase_items: ~1 rows (approximately)
DELETE FROM `purchase_items`;
INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 3, 150000, '2025-04-21 13:32:47', '2025-04-21 13:33:09');

-- Dumping structure for table threestycake.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.sessions: ~1 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('ytltmimg9X0n8AnlQWwGOn5WkrTDOi0k7tY5e3UM', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoicDhrRmNtN1p1ZXhkQ25DZllGbXh5M2FmZzU0clJjSzFyMk44WG1GZSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly90aHJlZXN0eWNha2UudGVzdC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkVzVFSEJnUG9KN3pqRVlIeGZSVGZMLjVWVk84Z0dUSzJTZkoyWXE1VWVRbFd5OTd5cWt3c0ciO3M6NDA6IjQ4MDQwZWY3ZjI1NDJiMzliOWJhOWE3Mjk4M2IwZDg4X2ZpbHRlcnMiO047fQ==', 1746239073);

-- Dumping structure for table threestycake.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipe Pengaturan',
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nilai Pengaturan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.settings: ~3 rows (approximately)
DELETE FROM `settings`;
INSERT INTO `settings` (`id`, `type`, `value`, `created_at`, `updated_at`, `description`) VALUES
	(1, 'account_name', 'Itzel Kiehn', '2025-04-28 09:59:10', '2025-04-28 10:02:59', 'Nama lengkap pemilik rekening'),
	(2, 'account_number', '111222333', '2025-04-28 09:59:10', '2025-04-28 10:03:32', 'Nomor rekening tujuan pembayaran'),
	(3, 'bank_name', 'BCA', '2025-04-28 09:59:10', '2025-04-28 10:03:39', 'Nama bank'),
	(4, 'address', 'Jl. Pantai Pererenan No.80, Pererenan, Kec. Mengwi, Kabupaten Badung, Bali 80351', NULL, NULL, 'Alamat toko'),
	(5, 'phone', '628980592309', NULL, NULL, 'Nomor telepon (diawali dengan kode negara, tanpa tanda + dan simbol lainnya)'),
	(6, 'instagram', 'threestycake', NULL, NULL, 'Username instagram (tanpa simbol @)');

-- Dumping structure for table threestycake.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama Perusahaan',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Supplier',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor Telepon',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Alamat',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.suppliers: ~1 rows (approximately)
DELETE FROM `suppliers`;
INSERT INTO `suppliers` (`id`, `company_name`, `name`, `phone_number`, `email`, `address`, `created_at`, `updated_at`) VALUES
	(1, 'Wunsch - Schultz', 'Nigel Buckridge', '261-618-5567', 'your.email+fakedata22296@gmail.com', '734 Baumbach Rest', '2025-04-21 13:31:27', '2025-04-21 13:31:27');

-- Dumping structure for table threestycake.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custom_fields` json DEFAULT NULL,
  `avatar_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table threestycake.users: ~4 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `custom_fields`, `avatar_url`, `role`) VALUES
	(1, 'admin', 'admin@threestycake.test', NULL, '$2y$12$u9gVNyeMTAzZu/rcpA/PLuFfus13O36NcU91ZcBtNUZfU.IdjTM/K', 'HVGbQwuizqOv1EC8OM0wlyNX8wO68BgdbfKxjoCPUASrD0xl8AiIZqeRo2DC', '2025-04-20 12:55:30', '2025-04-21 03:59:31', NULL, 'avatars/01JSB80M4JZ2ZBKE0NQAH1PPGA.jpg', 'admin'),
	(2, 'Lolita Collins', 'your.email+fakedata94820@gmail.com', NULL, '$2y$12$W5EHBgPoJ7zjEYHxfRTfL.5VVO8gGTK2SfJ2Yq5UeQlWy97yqkwsG', '3w0niTZpLjs872UsdYw0Zlk36k4C4SsNsnuvb3VxCpfJ78s5IUc8nQ8fWsys', '2025-04-21 04:51:18', '2025-04-21 04:51:18', NULL, NULL, 'customer'),
	(3, 'customer', 'customer1@threestycake.test', NULL, '$2y$12$e29oVGCAX1gbQj.0OXT7Z.4oT0vEFS/9nzzSj/ZkaMEg0T3ZSYoFy', 'QYxs3AKY8w3Z4Pv1rfaiBdOLuLjl3gfFfLvuDsPJJhCgqecbbq7cQZ3E01Hi', '2025-04-21 04:57:06', '2025-04-28 14:44:47', NULL, 'avatars/01JSBBF4KZV2M0P799RKDJ1WNT.jpg', 'customer'),
	(4, 'Ole Kris', 'your.email+fakedata21560@gmail.com', NULL, '$2y$12$W5EHBgPoJ7zjEYHxfRTfL.5VVO8gGTK2SfJ2Yq5UeQlWy97yqkwsG', NULL, '2025-04-28 13:58:58', '2025-04-28 13:58:58', NULL, NULL, 'customer');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
