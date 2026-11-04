-- =====================================================================
-- PATCH UPGRADE DATABASE E-BAROKAH V1 -> E-BAROKAH V2
-- Menambahkan tabel sistem Laravel, Spatie Roles, Master Bank,
-- Kepanitiaan, serta kolom catatan_khusus_umana & default value aman.
-- =====================================================================

DROP PROCEDURE IF EXISTS upgrade_ebarokah_v2_cols;
DELIMITER $$
CREATE PROCEDURE upgrade_ebarokah_v2_cols()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'kehadiran_pengajar' 
        AND COLUMN_NAME = 'catatan_khusus_umana'
    ) THEN
        ALTER TABLE `kehadiran_pengajar` ADD COLUMN `catatan_khusus_umana` TEXT NULL AFTER `tgl_input`;
    END IF;

    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'total_barokah_pengajar' 
        AND COLUMN_NAME = 'catatan_khusus_umana'
    ) THEN
        ALTER TABLE `total_barokah_pengajar` ADD COLUMN `catatan_khusus_umana` TEXT NULL AFTER `diterima`;
    END IF;

    -- Pastikan kolom legacy NOT NULL memiliki default value yang aman untuk V2
    ALTER TABLE `pengguna` MODIFY COLUMN `foto` VARCHAR(100) NULL DEFAULT '';
    ALTER TABLE `kehadiran_lembaga`
        MODIFY COLUMN `jumlah_total` INT(3) NOT NULL DEFAULT 0,
        MODIFY COLUMN `file` VARCHAR(225) NULL DEFAULT '';
    ALTER TABLE `satpam` MODIFY COLUMN `is_danru` ENUM('0','1') NOT NULL DEFAULT '0';
    ALTER TABLE `penempatan`
        MODIFY COLUMN `id_lembaga` VARCHAR(30) NOT NULL DEFAULT '',
        MODIFY COLUMN `tunj_kel` VARCHAR(10) NOT NULL DEFAULT 'Tidak',
        MODIFY COLUMN `tunj_anak` VARCHAR(10) NOT NULL DEFAULT 'Tidak',
        MODIFY COLUMN `tunj_mp` VARCHAR(10) NOT NULL DEFAULT 'Tidak',
        MODIFY COLUMN `kehormatan` VARCHAR(10) NOT NULL DEFAULT 'Tidak',
        MODIFY COLUMN `kategori_trans` VARCHAR(50) NOT NULL DEFAULT '',
        MODIFY COLUMN `jabatan_lembaga` VARCHAR(30) NOT NULL DEFAULT '',
        MODIFY COLUMN `file_sk` VARCHAR(255) NOT NULL DEFAULT '';
END$$
DELIMITER ;
CALL upgrade_ebarokah_v2_cols();
DROP PROCEDURE upgrade_ebarokah_v2_cols;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bank`
--

CREATE TABLE IF NOT EXISTS `bank` (
  `id_bank` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_bank` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_bank` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bifast_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BRINIDJA',
  `tipe_transfer` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BIF',
  `biaya_admin` int(11) NOT NULL DEFAULT '2500',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_bank`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `bank` VALUES
(1,'Bank Rakyat Indonesia','002','BRINIDJA','IFT',1000,1,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(2,'Bank Mandiri','008','BMRIIDJA','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(3,'Bank BCA','014','CENAIDJA','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(4,'Bank BNI','009','BNINIDJA','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(5,'Bank Jatim','114','PDJTIDJ1','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(6,'Bank Syari\'ah Indonesia','451','BSMDIDJA','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(7,'Bank Muamalat','147','MUABIDJA','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(8,'Bank BTN','200','BTANIDJA','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18'),
(9,'Bank CIMB','022','BNIAIDJA','BIF',2500,0,'2026-10-13 01:16:25','2026-10-13 01:29:18');

--
-- Table structure for Kepanitiaan V2 tables
--

CREATE TABLE IF NOT EXISTS `master_jenis_kegiatan` (
  `id_jenis_kegiatan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kegiatan` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jenis_kegiatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `barokah_kepanitiaan` (
  `id_nominal_kepanitiaan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_jenis_kegiatan` bigint(20) unsigned NOT NULL,
  `jabatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_nominal_kepanitiaan`),
  KEY `barokah_kepanitiaan_id_jenis_kegiatan_foreign` (`id_jenis_kegiatan`),
  CONSTRAINT `barokah_kepanitiaan_id_jenis_kegiatan_foreign` FOREIGN KEY (`id_jenis_kegiatan`) REFERENCES `master_jenis_kegiatan` (`id_jenis_kegiatan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kepanitiaan_transaksi` (
  `id_kepanitiaan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_jenis_kegiatan` bigint(20) unsigned NOT NULL,
  `nama_spesifik_kegiatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bulan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Draft','Tervalidasi','Dicairkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kepanitiaan`),
  KEY `kepanitiaan_transaksi_id_jenis_kegiatan_foreign` (`id_jenis_kegiatan`),
  CONSTRAINT `kepanitiaan_transaksi_id_jenis_kegiatan_foreign` FOREIGN KEY (`id_jenis_kegiatan`) REFERENCES `master_jenis_kegiatan` (`id_jenis_kegiatan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kepanitiaan_detail` (
  `id_detail_kepanitiaan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_kepanitiaan` bigint(20) unsigned NOT NULL,
  `nik_umana` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_nominal_kepanitiaan` bigint(20) unsigned NOT NULL,
  `jml_kehadiran` int(11) NOT NULL DEFAULT 1,
  `nominal_satuan` bigint(20) NOT NULL DEFAULT 0,
  `total_barokah` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_kepanitiaan`),
  KEY `kepanitiaan_detail_id_kepanitiaan_foreign` (`id_kepanitiaan`),
  KEY `kepanitiaan_detail_id_nominal_kepanitiaan_foreign` (`id_nominal_kepanitiaan`),
  KEY `kepanitiaan_detail_nik_umana_index` (`nik_umana`),
  CONSTRAINT `kepanitiaan_detail_id_kepanitiaan_foreign` FOREIGN KEY (`id_kepanitiaan`) REFERENCES `kepanitiaan_transaksi` (`id_kepanitiaan`) ON DELETE CASCADE,
  CONSTRAINT `kepanitiaan_detail_id_nominal_kepanitiaan_foreign` FOREIGN KEY (`id_nominal_kepanitiaan`) REFERENCES `barokah_kepanitiaan` (`id_nominal_kepanitiaan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `roles` VALUES
(1,'SuperAdmin','web','2026-10-01 07:33:41','2026-10-01 07:33:41'),
(2,'SDM','web','2026-10-01 07:33:42','2026-10-01 07:33:42'),
(3,'AdminLembaga','web','2026-10-01 07:33:42','2026-10-01 07:33:42'),
(4,'Evaluasi','web','2026-10-01 07:33:42','2026-10-01 07:33:42'),
(5,'Kasir','web','2026-10-01 07:33:42','2026-10-01 07:33:42');

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `model_has_roles` VALUES (1,'App\\Models\\Pengguna',1),(2,'App\\Models\\Pengguna',3),(3,'App\\Models\\Pengguna',4),(3,'App\\Models\\Pengguna',5),(3,'App\\Models\\Pengguna',6),(4,'App\\Models\\Pengguna',7),(5,'App\\Models\\Pengguna',8),(3,'App\\Models\\Pengguna',9),(3,'App\\Models\\Pengguna',10),(3,'App\\Models\\Pengguna',11),(3,'App\\Models\\Pengguna',12),(3,'App\\Models\\Pengguna',13),(3,'App\\Models\\Pengguna',14),(3,'App\\Models\\Pengguna',15),(3,'App\\Models\\Pengguna',16),(3,'App\\Models\\Pengguna',17),(3,'App\\Models\\Pengguna',18),(3,'App\\Models\\Pengguna',19),(3,'App\\Models\\Pengguna',20),(3,'App\\Models\\Pengguna',21),(3,'App\\Models\\Pengguna',22),(3,'App\\Models\\Pengguna',23),(3,'App\\Models\\Pengguna',24),(3,'App\\Models\\Pengguna',25),(3,'App\\Models\\Pengguna',26),(3,'App\\Models\\Pengguna',27),(3,'App\\Models\\Pengguna',28),(3,'App\\Models\\Pengguna',29),(3,'App\\Models\\Pengguna',30),(3,'App\\Models\\Pengguna',31),(3,'App\\Models\\Pengguna',32),(3,'App\\Models\\Pengguna',33),(3,'App\\Models\\Pengguna',34),(3,'App\\Models\\Pengguna',35),(3,'App\\Models\\Pengguna',36),(3,'App\\Models\\Pengguna',37),(3,'App\\Models\\Pengguna',38),(3,'App\\Models\\Pengguna',39),(3,'App\\Models\\Pengguna',40),(3,'App\\Models\\Pengguna',41),(3,'App\\Models\\Pengguna',42),(3,'App\\Models\\Pengguna',43),(3,'App\\Models\\Pengguna',44),(3,'App\\Models\\Pengguna',45),(3,'App\\Models\\Pengguna',46),(3,'App\\Models\\Pengguna',47),(3,'App\\Models\\Pengguna',48),(3,'App\\Models\\Pengguna',49),(3,'App\\Models\\Pengguna',50),(3,'App\\Models\\Pengguna',51),(2,'App\\Models\\Pengguna',52),(3,'App\\Models\\Pengguna',53),(3,'App\\Models\\Pengguna',54),(3,'App\\Models\\Pengguna',55),(3,'App\\Models\\Pengguna',56),(3,'App\\Models\\Pengguna',57),(3,'App\\Models\\Pengguna',58),(3,'App\\Models\\Pengguna',59),(3,'App\\Models\\Pengguna',60),(3,'App\\Models\\Pengguna',61),(3,'App\\Models\\Pengguna',62),(3,'App\\Models\\Pengguna',63),(3,'App\\Models\\Pengguna',64),(3,'App\\Models\\Pengguna',65),(3,'App\\Models\\Pengguna',66),(1,'App\\Models\\Pengguna',67);

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `cache_locks`
--

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `job_batches`
--

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_10_01_073222_create_permission_tables',1),
(5,'2026_10_13_000000_create_bank_table',2),
(6,'2026_10_29_000001_create_kepanitiaan_tables',3),
(7,'2026_11_04_000001_upgrade_legacy_v1_schema_for_v2',3);

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
