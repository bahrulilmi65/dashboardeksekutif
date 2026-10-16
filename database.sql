-- ============================================================
-- DATABASE: dashboard_eksekutif
-- Aplikasi Dashboard Eksekutif Pengelolaan Dana Organisasi Kepemudaan
-- ============================================================

CREATE DATABASE IF NOT EXISTS `dashboard_eksekutif` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `dashboard_eksekutif`;

-- ------------------------------------------------------------
-- Tabel: users
-- ------------------------------------------------------------
CREATE TABLE `users` (
    `id`         INT(11) NOT NULL AUTO_INCREMENT,
    `nama`       VARCHAR(100) NOT NULL,
    `username`   VARCHAR(50) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `role`       ENUM('admin','pimpinan') NOT NULL DEFAULT 'pimpinan',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel: organisasi
-- ------------------------------------------------------------
CREATE TABLE `organisasi` (
    `id`              INT(11) NOT NULL AUTO_INCREMENT,
    `nama_organisasi` VARCHAR(150) NOT NULL,
    `ketua`           VARCHAR(100) NOT NULL,
    `alamat`          TEXT NOT NULL,
    `no_hp`           VARCHAR(20) NOT NULL,
    `jumlah_anggota`  INT(11) NOT NULL DEFAULT 0,
    `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel: pengajuan_dana
-- ------------------------------------------------------------
CREATE TABLE `pengajuan_dana` (
    `id`             INT(11) NOT NULL AUTO_INCREMENT,
    `organisasi_id`  INT(11) NOT NULL,
    `nomor_proposal` VARCHAR(50) NOT NULL UNIQUE,
    `tanggal`        DATE NOT NULL,
    `nama_kegiatan`  VARCHAR(200) NOT NULL,
    `dana_diajukan`  DECIMAL(15,2) NOT NULL DEFAULT 0,
    `status`         ENUM('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
    `catatan`        TEXT NULL,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`organisasi_id`) REFERENCES `organisasi`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel: pencairan_dana
-- ------------------------------------------------------------
CREATE TABLE `pencairan_dana` (
    `id`               INT(11) NOT NULL AUTO_INCREMENT,
    `pengajuan_id`     INT(11) NOT NULL,
    `nomor_pencairan`  VARCHAR(50) NOT NULL UNIQUE,
    `tanggal`          DATE NOT NULL,
    `nominal`          DECIMAL(15,2) NOT NULL DEFAULT 0,
    `keterangan`       TEXT NULL,
    `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_dana`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA AWAL (SEED DATA)
-- ============================================================

-- Users: password = "admin123" dan "pimpinan123" (bcrypt hash)
INSERT INTO `users` (`nama`, `username`, `password`, `role`) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Dr. Hendra Kusuma', 'pimpinan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pimpinan');

-- Organisasi
INSERT INTO `organisasi` (`nama_organisasi`, `ketua`, `alamat`, `no_hp`, `jumlah_anggota`) VALUES
('Himpunan Mahasiswa Informatika', 'Rizky Pratama', 'Gedung A Lt. 2, Universitas Nusantara', '081234567890', 85),
('Badan Eksekutif Mahasiswa', 'Siti Rahayu', 'Gedung Rektorat Lt. 1, Universitas Nusantara', '082345678901', 120),
('Unit Kegiatan Mahasiswa Seni', 'Ahmad Fauzi', 'Gedung Seni, Jl. Pendidikan No. 5', '083456789012', 60),
('Komunitas Pemuda Peduli Lingkungan', 'Dewi Anggraini', 'Jl. Hijau Raya No. 12, Kota Maju', '084567890123', 45),
('Karang Taruna Kelurahan Sejahtera', 'Budi Santoso', 'Balai Kelurahan Sejahtera, Jl. Mawar No. 7', '085678901234', 95),
('Forum Pemuda Kreatif', 'Nurul Hidayah', 'Jl. Kreasi No. 3, Gedung Pemuda', '086789012345', 50),
('Ikatan Pelajar Muhammadiyah', 'Yusuf Abdullah', 'Jl. Ahmad Dahlan No. 15', '087890123456', 110),
('Gerakan Mahasiswa Nasional', 'Putri Wulandari', 'Sekretariat GMNI, Jl. Perjuangan No. 8', '088901234567', 75);

-- Pengajuan Dana
INSERT INTO `pengajuan_dana` (`organisasi_id`, `nomor_proposal`, `tanggal`, `nama_kegiatan`, `dana_diajukan`, `status`, `catatan`) VALUES
(1, 'PROP/2026/001', '2026-01-10', 'Workshop Pemrograman Web', 5000000, 'Disetujui', 'Proposal lengkap dan layak dibiayai'),
(2, 'PROP/2026/002', '2026-01-15', 'Seminar Kepemimpinan Mahasiswa', 8500000, 'Disetujui', 'Sesuai program kerja tahunan'),
(3, 'PROP/2026/003', '2026-02-05', 'Pentas Seni Budaya Nusantara', 12000000, 'Menunggu', NULL),
(4, 'PROP/2026/004', '2026-02-12', 'Penanaman Pohon dan Bersih Lingkungan', 3500000, 'Disetujui', 'Kegiatan positif dan bermanfaat'),
(5, 'PROP/2026/005', '2026-02-20', 'Turnamen Olahraga Antar Pemuda', 7000000, 'Ditolak', 'Anggaran melebihi batas kuota'),
(1, 'PROP/2026/006', '2026-03-01', 'Hackathon Mahasiswa 2026', 10000000, 'Menunggu', NULL),
(6, 'PROP/2026/007', '2026-03-10', 'Pelatihan Kewirausahaan Pemuda', 6000000, 'Disetujui', 'Mendukung program unggulan daerah'),
(2, 'PROP/2026/008', '2026-03-18', 'Musyawarah Besar BEM', 4500000, 'Menunggu', NULL),
(7, 'PROP/2026/009', '2026-04-05', 'Kajian Islam dan Kebangsaan', 2500000, 'Disetujui', 'Kegiatan rutin tahunan'),
(8, 'PROP/2026/010', '2026-04-15', 'Seminar Nasional Pemuda 2026', 15000000, 'Menunggu', NULL),
(3, 'PROP/2026/011', '2026-05-02', 'Festival Musik Kampus', 9000000, 'Disetujui', 'Mendukung pengembangan minat bakat'),
(5, 'PROP/2026/012', '2026-05-20', 'Pelatihan Kepemimpinan Pemuda', 5500000, 'Menunggu', NULL),
(4, 'PROP/2026/013', '2026-06-08', 'Aksi Bersih Sungai dan Edukasi', 4000000, 'Disetujui', 'Program prioritas lingkungan'),
(6, 'PROP/2026/014', '2026-06-25', 'Bazaar UMKM Pemuda', 3000000, 'Ditolak', 'Proposal tidak memenuhi syarat administrasi'),
(1, 'PROP/2026/015', '2026-07-10', 'Konferensi Teknologi Mahasiswa', 11000000, 'Disetujui', 'Sangat mendukung kemajuan akademik'),
(2, 'PROP/2026/016', '2026-07-22', 'Peringatan Hari Kemerdekaan RI', 6500000, 'Menunggu', NULL);

-- Pencairan Dana
INSERT INTO `pencairan_dana` (`pengajuan_id`, `nomor_pencairan`, `tanggal`, `nominal`, `keterangan`) VALUES
(1, 'CAIR/2026/001', '2026-01-20', 5000000, 'Pencairan penuh sesuai proposal'),
(2, 'CAIR/2026/002', '2026-01-25', 8500000, 'Pencairan penuh untuk kegiatan seminar'),
(4, 'CAIR/2026/003', '2026-02-18', 3500000, 'Pencairan sesuai persetujuan'),
(7, 'CAIR/2026/004', '2026-03-15', 6000000, 'Pencairan untuk pelatihan wirausaha'),
(9, 'CAIR/2026/005', '2026-04-10', 2500000, 'Pencairan kajian islam dan kebangsaan'),
(11, 'CAIR/2026/006', '2026-05-08', 9000000, 'Pencairan festival musik kampus'),
(13, 'CAIR/2026/007', '2026-06-12', 4000000, 'Pencairan aksi bersih sungai'),
(15, 'CAIR/2026/008', '2026-07-15', 11000000, 'Pencairan konferensi teknologi mahasiswa');
