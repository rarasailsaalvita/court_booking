-- Script Database SM Sport Center
-- Database: sm_sport_center
-- Compatible: MySQL / MariaDB (XAMPP)

CREATE DATABASE IF NOT EXISTS `sm_sport_center` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sm_sport_center`;

-- Table Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Users
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `no_hp`) VALUES
(1, 'Administrator SM Sport', 'admin@smsport.com', '$2y$10$e8p2U.Oa2.jI813j3hJ1gO64tK.JqC8bNfG1qV3xL3G3z1Z1Z1Z1Z', 'admin', '081234567890'),
(2, 'Budi Santoso', 'budi@gmail.com', '$2y$10$e8p2U.Oa2.jI813j3hJ1gO64tK.JqC8bNfG1qV3xL3G3z1Z1Z1Z1Z', 'pelanggan', '089876543210');

-- Table Lapangan
CREATE TABLE IF NOT EXISTS `lapangan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `jenis` ENUM('futsal','badminton') NOT NULL,
  `harga_per_jam` DECIMAL(10,2) NOT NULL,
  `deskripsi` TEXT,
  `status` ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Lapangan
INSERT INTO `lapangan` (`id`, `nama`, `jenis`, `harga_per_jam`, `deskripsi`, `status`) VALUES
(1, 'Lapangan Futsal A (Rumput Sintetis)', 'futsal', 120000.00, 'Rumput sintetis standar FIFA, pencahayaan LED terang, ventilasi optimal.', 'aktif'),
(2, 'Lapangan Futsal B (Interlock Polypropylene)', 'futsal', 110000.00, 'Lantai interlock antiselip, peredam kejut tinggi untuk performa bermain maksimal.', 'aktif'),
(3, 'Lapangan Badminton 1 (Lantai Karpet Vinyl)', 'badminton', 50000.00, 'Karpet vinyl Li-Ning resmi BWF, standar turnamen nasional.', 'aktif'),
(4, 'Lapangan Badminton 2 (Lantai Karpet Vinyl)', 'badminton', 50000.00, 'Karpet vinyl tebal antilicin dengan garis batas standar internasional.', 'aktif'),
(5, 'Lapangan Badminton 3 (Lantai Kayu Parquet)', 'badminton', 45000.00, 'Lantai kayu parquet premium dengan kelenturan optimal.', 'aktif');

-- Table Reservasi
CREATE TABLE IF NOT EXISTS `reservasi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `lapangan_id` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `durasi` INT NOT NULL,
  `total_bayar` DECIMAL(10,2) NOT NULL,
  `status` ENUM('menunggu','lunas','dibatalkan') NOT NULL DEFAULT 'menunggu',
  `bukti_bayar` VARCHAR(255) DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Reservasi
INSERT INTO `reservasi` (`id`, `user_id`, `lapangan_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `durasi`, `total_bayar`, `status`, `bukti_bayar`, `catatan`) VALUES
(1, 2, 1, CURDATE(), '19:00:00', '21:00:00', 2, 240000.00, 'lunas', 'sample_qris_bukti.jpg', 'Sparring futsal tim RW 05'),
(2, 2, 3, CURDATE(), '16:00:00', '17:00:00', 1, 50000.00, 'menunggu', NULL, 'Latihan rutin badminton');
