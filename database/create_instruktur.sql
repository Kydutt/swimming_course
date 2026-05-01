-- ============================================================
-- create_instruktur.sql
-- Tabel instruktur / pelatih kursus renang
-- Jalankan sekali untuk membuat tabel dan data awal
-- ============================================================

USE swimming_course;

CREATE TABLE IF NOT EXISTS `instruktur` (
    `id_instruktur`      INT            NOT NULL AUTO_INCREMENT,
    `nama`               VARCHAR(100)   NOT NULL,
    `umur`               INT            DEFAULT NULL,
    `jenis_kelamin`      ENUM('Laki-laki','Perempuan') DEFAULT 'Laki-laki',
    `telepon`            VARCHAR(20)    DEFAULT NULL,
    `foto`               VARCHAR(255)   DEFAULT NULL COMMENT 'Path file foto relatif dari root project',
    `spesialisasi`       VARCHAR(200)   DEFAULT NULL COMMENT 'Misal: Gaya Bebas, Gaya Dada, dll',
    `sertifikasi`        VARCHAR(200)   DEFAULT NULL COMMENT 'Lisensi Provinsi / Kabupaten / Nasional',
    `pengalaman_wasit`   TEXT           DEFAULT NULL COMMENT 'Daftar pengalaman sebagai wasit, pisah baris baru',
    `pengalaman_melatih` TEXT           DEFAULT NULL COMMENT 'Daftar pengalaman melatih, pisah baris baru',
    `bio`                TEXT           DEFAULT NULL COMMENT 'Bio / deskripsi singkat pelatih',
    `is_active`          TINYINT(1)     NOT NULL DEFAULT 1,
    `created_at`         TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         TIMESTAMP      NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_instruktur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data awal berdasarkan biodata yang sudah ada di landing page
INSERT IGNORE INTO `instruktur`
    (`id_instruktur`, `nama`, `umur`, `jenis_kelamin`, `spesialisasi`, `sertifikasi`,
     `pengalaman_wasit`, `pengalaman_melatih`, `bio`, `is_active`)
VALUES (
    1,
    'Muhammad Fajri Yusuf',
    28,
    'Laki-laki',
    'Renang Gaya Bebas & Gaya Dada',
    'Lisensi Provinsi PRSI',
    'PON JABAR 2016\nPOMNAS 2018\nPORDA JABAR 2018\nPORPROV JABAR 2022',
    'Tirta Wiralodra Swimming Club (Indramayu)\nMetamorfosa Swimming Club (Indramayu)',
    'Pelatih renang profesional berpengalaman dengan lisensi wasit tingkat provinsi. Berdedikasi membantu setiap peserta mencapai potensi terbaiknya dalam olahraga renang.',
    1
);
