<<<<<<< HEAD
-- ============================================================
-- swimming_course.sql
-- Skema lengkap database, disinkronkan dengan kondisi aktual
-- ============================================================

CREATE DATABASE IF NOT EXISTS swimming_course
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE swimming_course;

-- ──────────────────────────────────────────────────────────
-- 1. Tabel: user
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `user` (
    `id_user`    INT            NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)   NOT NULL,
    `email`      VARCHAR(100)   NOT NULL,
    `password`   VARCHAR(255)   NOT NULL,
    `role`       ENUM('admin','user') NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user`),
    UNIQUE KEY `email` (`email`),
    KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data default admin
INSERT IGNORE INTO `user` (`name`, `email`, `password`, `role`)
VALUES ('Admin', 'admin@swimming.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ──────────────────────────────────────────────────────────
-- 2. Tabel: jadwal
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `jadwal` (
    `id_jadwal`     INT            NOT NULL AUTO_INCREMENT,
    `hari`          VARCHAR(20)    NOT NULL,
    `waktu_mulai`   TIME           NOT NULL,
    `waktu_selesai` TIME           NOT NULL,
    `keterangan`    VARCHAR(100)   DEFAULT NULL,
    `is_active`     TINYINT(1)     DEFAULT 1,
    `created_at`    TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_jadwal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
-- 3. Tabel: program
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `program` (
    `id_program`       INT          NOT NULL AUTO_INCREMENT,
    `nama_program`     VARCHAR(100) NOT NULL,
    `deskripsi`        TEXT         DEFAULT NULL,
    `harga`            INT          NOT NULL DEFAULT 0,
    `jumlah_pertemuan` INT          NOT NULL DEFAULT 8,
    `is_active`        TINYINT(1)   DEFAULT 1,
    `created_at`       TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_program`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
-- 4. Tabel: peserta
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `peserta` (
    `id_peserta`  INT            NOT NULL AUTO_INCREMENT,
    `full_name`   VARCHAR(100)   NOT NULL,
    `age`         INT            NOT NULL,
    `gender`      ENUM('Laki-laki','Perempuan') NOT NULL,
    `whatsapp`    VARCHAR(15)    NOT NULL,
    `address`     TEXT           NOT NULL,
    `id_program`  INT            DEFAULT NULL COMMENT 'FK ke program.id_program',
    `id_jadwal`   INT            DEFAULT NULL COMMENT 'FK ke jadwal.id_jadwal',
    `program`     VARCHAR(50)    NOT NULL   COMMENT 'Nama program (snapshot text)',
    `schedule`    VARCHAR(100)   NOT NULL   COMMENT 'Keterangan jadwal (snapshot text)',
    `status`      ENUM('Pending','Approved','Rejected','Completed') DEFAULT 'Pending',
    `notes`       TEXT           DEFAULT NULL,
    `created_at`  TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_peserta`),
    KEY `idx_status`     (`status`),
    KEY `idx_program`    (`program`),
    KEY `idx_created_at` (`created_at`),
    KEY `fk_id_program`  (`id_program`),
    KEY `fk_id_jadwal`   (`id_jadwal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
-- 5. Tabel: arsip_peserta
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `arsip_peserta` (
    `id_arsip`        INT            NOT NULL AUTO_INCREMENT,
    `id_peserta_asli` INT            NOT NULL COMMENT 'ID peserta asli sebelum dihapus',
    `full_name`       VARCHAR(150)   NOT NULL,
    `age`             INT            DEFAULT NULL,
    `gender`          VARCHAR(20)    DEFAULT NULL,
    `whatsapp`        VARCHAR(30)    DEFAULT NULL,
    `address`         TEXT           DEFAULT NULL,
    `program`         VARCHAR(100)   DEFAULT NULL,
    `schedule`        VARCHAR(150)   DEFAULT NULL,
    `harga`           DECIMAL(12,0)  DEFAULT 0    COMMENT 'Harga saat pendaftaran (snapshot)',
    `status`          VARCHAR(30)    DEFAULT 'Pending',
    `notes`           TEXT           DEFAULT NULL,
    `created_at`      DATETIME       DEFAULT NULL COMMENT 'Tanggal daftar asli',
    `archived_at`     DATETIME       DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal data diarsipkan',
    PRIMARY KEY (`id_arsip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
-- 6. Tabel: feedback
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `feedback` (
    `id_feedback` INT         NOT NULL AUTO_INCREMENT,
    `id_user`     INT         NOT NULL COMMENT 'FK ke user.id_user',
    `rating`      TINYINT     NOT NULL DEFAULT 5,
    `komentar`    TEXT        NOT NULL,
    `is_approved` TINYINT(1)  DEFAULT 1,
    `created_at`  TIMESTAMP   NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_feedback`),
    KEY `fk_feedback_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
=======
-- ===================================================
-- Database for AquaLearn Swimming Course Registration
-- ===================================================

-- Create database
CREATE DATABASE IF NOT EXISTS swimming_course CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE swimming_course;

-- Table for registrations
CREATE TABLE IF NOT EXISTS peserta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Laki-laki', 'Perempuan') NOT NULL,
    whatsapp VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    program VARCHAR(50) NOT NULL,
    schedule VARCHAR(100) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_program (program),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for users (both admin and regular users)
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (email: admin@swimming.com, password: admin123)
-- Password is hashed using bcrypt
INSERT INTO user (name, email, password, role) VALUES 
('Administrator', 'admin@swimming.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5eoWb.hlO7T8u', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Sample data untuk testing (optional)
INSERT INTO registrations (full_name, age, gender, whatsapp, address, program, schedule, status) VALUES
('Budi Santoso', 8, 'Laki-laki', '081234567890', 'Jl. Merdeka No. 123, Jakarta', 'Kelas Anak-anak', 'Senin, Rabu, Jumat (15:00 - 16:00)', 'Approved'),
('Siti Nurhaliza', 15, 'Perempuan', '081234567891', 'Jl. Sudirman No. 456, Jakarta', 'Kelas Remaja', 'Selasa, Kamis, Sabtu (16:00 - 17:30)', 'Pending'),
('Ahmad Yani', 25, 'Laki-laki', '081234567892', 'Jl. Gatot Subroto No. 789, Jakarta', 'Kelas Dewasa', 'Pagi (06:00 - 07:00)', 'Approved');
>>>>>>> 5602e542b274e0e95a5208303e442b6d4456b195
