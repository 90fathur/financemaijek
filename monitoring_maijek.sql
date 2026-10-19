/*
 Navicat Premium Data Transfer

 Source Server         : koneksilaragon
 Source Server Type    : MySQL
 Source Server Version : 50733 (5.7.33)
 Source Host           : localhost:3306
 Source Schema         : monitoring_maijek

 Target Server Type    : MySQL
 Target Server Version : 50733 (5.7.33)
 File Encoding         : 65001

 Date: 19/10/2026 10:53:36
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('pemasukan','pengeluaran') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_kategori` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bi-tag',
  `warna` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#10b981',
  `deskripsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES (1, 'Bagi Hasil Driver (Ride & Delivery)', 'pemasukan', 'IN-RIDE', 'bi-bicycle', '#10b981', 'Potongan fee 20% dari setiap trip order ride & kurir titip barang', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (2, 'Komisi Penjualan Resto & Merchant (MaiFood)', 'pemasukan', 'IN-FOOD', 'bi-shop', '#06b6d4', 'Komisi bagi hasil dari pesanan makanan resto partner', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (3, 'Biaya Layanan Platform & Jasa Titip', 'pemasukan', 'IN-PLATFORM', 'bi-box-seam', '#3b82f6', 'Biaya aplikasi per transaksi dari konsumen', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (4, 'Topup Fee Saldo Dompet Driver & Pelanggan', 'pemasukan', 'IN-TOPUP', 'bi-wallet2', '#8b5cf6', 'Biaya admin topup saldo melalui payment gateway atau loket', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (5, 'Suntikan Modal Investasi Masuk', 'pemasukan', 'IN-INVEST', 'bi-cash-coin', '#059669', 'Penerimaan dana investasi baru dari investor', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (6, 'Iklan Merchant & Kerjasama Promo', 'pemasukan', 'IN-PROMO', 'bi-megaphone', '#f59e0b', 'Pemasangan banner promosi merchant di beranda aplikasi', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (7, 'Cloud Server & Database Hosting', 'pengeluaran', 'OUT-SERVER', 'bi-hdd-network', '#ef4444', 'Biaya server backend API, redis cache, dan hosting database', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (8, 'Google Maps Platform & Location API', 'pengeluaran', 'OUT-GMAPS', 'bi-geo-alt', '#f97316', 'Biaya rute, directions, dan geocoding live tracking driver', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (9, 'WhatsApp OTP Gateway & Notifikasi', 'pengeluaran', 'OUT-WA', 'bi-chat-dots', '#ec4899', 'Biaya pengiriman kode OTP login driver/pelanggan via WhatsApp', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (10, 'Gaji & Insentif Tim CS / Dispatcher', 'pengeluaran', 'OUT-GAJI', 'bi-people', '#6366f1', 'Gaji bulanan customer care, admin finance, dan verifikator mitra', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (11, 'Marketing, Jaket & Merchandise Mitra', 'pengeluaran', 'OUT-MKTG', 'bi-gift', '#84cc16', 'Pengadaan jaket/helm driver, stiker merchant, dan promosi offline', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (12, 'Pencairan Dividen / Bagi Hasil Investor', 'pengeluaran', 'OUT-DIVIDEN', 'bi-coin', '#eab308', 'Pembayaran dividen / return of investment kepada investor', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (13, 'Operasional Kantor & Biaya Umum', 'pengeluaran', 'OUT-OPS', 'bi-building', '#64748b', 'Listrik, internet fiber, sewa basecamp, ATK & konsumsi rapat', '2026-10-18 21:37:51', '2026-10-18 21:37:51');
INSERT INTO `categories` VALUES (14, 'Maintenance Aplikasi & Bug Fixing', 'pengeluaran', 'OUT-MAINT', 'bi-tools', '#a855f7', 'Biaya lisensi development tools, audit security, dan rilis Google Play', '2026-10-18 21:37:51', '2026-10-18 21:37:51');

-- ----------------------------
-- Table structure for investment_logs
-- ----------------------------
DROP TABLE IF EXISTS `investment_logs`;
CREATE TABLE `investment_logs`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `investor_id` int(11) UNSIGNED NOT NULL,
  `transaction_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `tipe` enum('suntikan_modal','dividen_cair','penarikan_modal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(15, 2) NOT NULL,
  `tanggal` date NOT NULL,
  `periode_dividen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `bukti` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `investor_id`(`investor_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of investment_logs
-- ----------------------------
INSERT INTO `investment_logs` VALUES (2, 2, 2, 'suntikan_modal', 5000000.00, '2026-10-15', NULL, 'bukti_20261019_100332_fb008aa3.jpeg', 'Modal awal pendaftaran', '2026-10-19 10:03:32', '2026-10-19 10:03:32');
INSERT INTO `investment_logs` VALUES (3, 3, 3, 'suntikan_modal', 5000000.00, '2026-10-14', NULL, 'bukti_20261019_100457_b6645774.jpeg', 'Modal awal pendaftaran', '2026-10-19 10:04:57', '2026-10-19 10:04:57');
INSERT INTO `investment_logs` VALUES (4, 4, 5, 'suntikan_modal', 5000000.00, '2026-10-14', NULL, 'bukti_20261019_100928_d644b76a.jpeg', 'Modal awal pendaftaran', '2026-10-19 10:09:28', '2026-10-19 10:09:28');

-- ----------------------------
-- Table structure for investors
-- ----------------------------
DROP TABLE IF EXISTS `investors`;
CREATE TABLE `investors`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_investor` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_investor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_telepon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nama_bank` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nomor_rekening` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pemilik_rekening` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `total_investasi` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `persentase_bagi_hasil` decimal(5, 2) NOT NULL DEFAULT 0.00,
  `tanggal_bergabung` date NOT NULL,
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `kode_investor`(`kode_investor`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of investors
-- ----------------------------
INSERT INTO `investors` VALUES (2, 'INV-002', 'Wahyuningsih', 'maijek@gmail.com', '6281242463700', 'Seabank', '', '', 5000000.00, 0.00, '2026-10-15', 'aktif', '', '2026-10-19 10:03:32', '2026-10-19 10:03:32');
INSERT INTO `investors` VALUES (3, 'INV-003', 'Akbar', 'maijek@gmail.com', '085255660006', 'Seabank', '', '', 5000000.00, 0.00, '2026-10-14', 'aktif', '', '2026-10-19 10:04:57', '2026-10-19 10:04:57');
INSERT INTO `investors` VALUES (4, 'INV-004', 'FADLI', 'maijek@gmail.com', '6282221057448', 'Seabank', '', '', 5000000.00, 0.00, '2026-10-14', 'aktif', '', '2026-10-19 10:09:28', '2026-10-19 10:09:28');

-- ----------------------------
-- Table structure for kas_accounts
-- ----------------------------
DROP TABLE IF EXISTS `kas_accounts`;
CREATE TABLE `kas_accounts`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_akun` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_akun` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `saldo_awal` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `saldo_saat_ini` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kas_accounts
-- ----------------------------
INSERT INTO `kas_accounts` VALUES (1, 'Bank BCA Bisnis Maijek', '8219082311', 0.00, 0.00, 'Rekening utama penampungan transaksi driver dan merchant resto', '2026-10-18 21:37:51', '2026-10-19 10:08:49');
INSERT INTO `kas_accounts` VALUES (2, 'Bank Mandiri Operasional', '152009812344', 0.00, 0.00, 'Rekening khusus pembayaran server, API Google, gaji dan vendor', '2026-10-18 21:37:51', '2026-10-19 09:51:50');
INSERT INTO `kas_accounts` VALUES (3, 'Rekening Penampungan Investasi (BNI)', '0987123456', 0.00, 0.00, 'Rekening modal investasi awal & dana cadangan strategis', '2026-10-18 21:37:51', '2026-10-19 09:51:50');
INSERT INTO `kas_accounts` VALUES (4, 'Kas Tunai / Petty Cash Kantor', 'CASH-POLEWALI', 0.00, 0.00, 'Kas kecil tunai untuk keperluan darurat operasional kantor', '2026-10-18 21:37:51', '2026-10-19 09:51:50');
INSERT INTO `kas_accounts` VALUES (5, 'Seabank', '901009570320', 0.00, 14800000.00, 'Rekening Sementara', '2026-10-19 10:01:24', '2026-10-19 10:11:39');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2026-10-18-133522', 'App\\Database\\Migrations\\CreateMonitoringMaijekTables', 'default', 'App', 1792330554, 1);

-- ----------------------------
-- Table structure for system_settings
-- ----------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `setting_key`(`setting_key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_settings
-- ----------------------------
INSERT INTO `system_settings` VALUES (1, 'app_name', 'Maijek Financial & Investment Monitoring System', NULL, NULL);
INSERT INTO `system_settings` VALUES (2, 'company_name', 'PT. Maijek Transportasi Nusantara', NULL, NULL);
INSERT INTO `system_settings` VALUES (3, 'currency_symbol', 'Rp', NULL, NULL);
INSERT INTO `system_settings` VALUES (4, 'target_roi_annual', '18.5', NULL, NULL);
INSERT INTO `system_settings` VALUES (5, 'contact_finance', 'finance@maijek.id', NULL, NULL);
INSERT INTO `system_settings` VALUES (6, 'app_version', 'v2.4.0-Enterprise', NULL, NULL);

-- ----------------------------
-- Table structure for transactions
-- ----------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_transaksi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `tipe` enum('pemasukan','pengeluaran') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `kas_account_id` int(11) UNSIGNED NOT NULL,
  `investor_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `jumlah` decimal(15, 2) NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `bukti_transaksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nama_penginput` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin Maijek',
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nomor_transaksi`(`nomor_transaksi`) USING BTREE,
  INDEX `category_id`(`category_id`) USING BTREE,
  INDEX `kas_account_id`(`kas_account_id`) USING BTREE,
  INDEX `investor_id`(`investor_id`) USING BTREE,
  INDEX `tanggal`(`tanggal`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transactions
-- ----------------------------
INSERT INTO `transactions` VALUES (2, 'TRX-IN-202610-0002', '2026-10-15', 'pemasukan', 5, 5, 2, 5000000.00, 'Setoran Modal Awal Investor Wahyuningsih', 'Penyertaan modal awal pendaftaran investor baru', 'bukti_20261019_100332_fb008aa3.jpeg', 'Super Administrator Maijek', '2026-10-19 10:03:32', '2026-10-19 10:03:32');
INSERT INTO `transactions` VALUES (3, 'TRX-IN-202610-0003', '2026-10-14', 'pemasukan', 5, 5, 3, 5000000.00, 'Setoran Modal Awal Investor Akbar', 'Penyertaan modal awal pendaftaran investor baru', 'bukti_20261019_100457_b6645774.jpeg', 'Super Administrator Maijek', '2026-10-19 10:04:57', '2026-10-19 10:04:57');
INSERT INTO `transactions` VALUES (4, 'TRX-OUT-202610-0001', '2026-10-14', 'pengeluaran', 11, 5, NULL, 125000.00, 'Pembuatan Banner 5 Lembar Maijek', 'Bukti Transfer Sudah Di Sertakan', 'bukti_20261019_100747_7e1cd0ee.jpeg', 'Super Administrator Maijek', '2026-10-19 10:07:47', '2026-10-19 10:08:09');
INSERT INTO `transactions` VALUES (5, 'TRX-IN-202610-0004', '2026-10-14', 'pemasukan', 5, 5, 4, 5000000.00, 'Setoran Modal Awal Investor FADLI', 'Penyertaan modal awal pendaftaran investor baru', 'bukti_20261019_100928_d644b76a.jpeg', 'Super Administrator Maijek', '2026-10-19 10:09:28', '2026-10-19 10:09:28');
INSERT INTO `transactions` VALUES (6, 'TRX-OUT-202610-0002', '2026-10-19', 'pengeluaran', 11, 5, NULL, 75000.00, 'Bayar Iklan di Bapenda Polman', 'Melalui Qris', 'bukti_20261019_101139_30f6b58e.jpeg', 'Super Administrator Maijek', '2026-10-19 10:11:39', '2026-10-19 10:11:39');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('superadmin','admin','investor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `investor_id` int(11) UNSIGNED NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE,
  UNIQUE INDEX `email`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Super Administrator Maijek', 'admin', 'admin@maijek.id', '$2y$12$dIH4uMEkHz.N3ajNlR5iOuLMURcnzsfNSbOStHXavf4qpoaxvzice', 'superadmin', NULL, NULL, '2026-10-18 21:37:50', '2026-10-19 09:51:50');
INSERT INTO `users` VALUES (2, 'Staff Finance & Accounting', 'finance', 'finance@maijek.id', '$2y$12$G8sXeIvM2YSWHvjrNpDNYOttbjuUvTA5mU6uogF62q5xWj/NV.pqO', 'admin', NULL, NULL, '2026-10-18 21:37:51', '2026-10-18 21:37:51');

SET FOREIGN_KEY_CHECKS = 1;
