-- =====================================================
-- Tabel arsip_peserta: menyimpan data historis peserta
-- yang dihapus agar laporan tetap akurat
-- =====================================================
CREATE TABLE IF NOT EXISTS `arsip_peserta` (
    `id_arsip`      INT AUTO_INCREMENT PRIMARY KEY,
    `id_peserta_asli` INT NOT NULL COMMENT 'ID peserta asli sebelum dihapus',
    `full_name`     VARCHAR(150)   NOT NULL,
    `age`           INT            DEFAULT NULL,
    `gender`        VARCHAR(20)    DEFAULT NULL,
    `whatsapp`      VARCHAR(30)    DEFAULT NULL,
    `address`       TEXT           DEFAULT NULL,
    `program`       VARCHAR(100)   DEFAULT NULL,
    `schedule`      VARCHAR(150)   DEFAULT NULL,
    `harga`         DECIMAL(12,0)  DEFAULT 0     COMMENT 'Harga saat pendaftaran — snapshot',
    `status`        VARCHAR(30)    DEFAULT 'Pending',
    `notes`         TEXT           DEFAULT NULL,
    `created_at`    DATETIME       DEFAULT NULL  COMMENT 'Tanggal daftar asli',
    `archived_at`   DATETIME       DEFAULT NOW() COMMENT 'Tanggal data diarsipkan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
