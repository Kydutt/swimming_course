-- ===================================================
-- Database for AquaLearn Swimming Course Registration
-- ===================================================

-- Create database
CREATE DATABASE IF NOT EXISTS swimming_course CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE swimming_course;

-- Table for registrations
CREATE TABLE IF NOT EXISTS registrations (
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

-- Table for admin users (if needed)
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Note: In production, use proper password hashing
INSERT INTO admin (email, password, name) VALUES 
('admin@aqualear.id', MD5('admin123'), 'Administrator')
ON DUPLICATE KEY UPDATE email=email;

-- Sample data untuk testing (optional)
INSERT INTO registrations (full_name, age, gender, whatsapp, address, program, schedule, status) VALUES
('Budi Santoso', 8, 'Laki-laki', '081234567890', 'Jl. Merdeka No. 123, Jakarta', 'Kelas Anak-anak', 'Senin, Rabu, Jumat (15:00 - 16:00)', 'Approved'),
('Siti Nurhaliza', 15, 'Perempuan', '081234567891', 'Jl. Sudirman No. 456, Jakarta', 'Kelas Remaja', 'Selasa, Kamis, Sabtu (16:00 - 17:30)', 'Pending'),
('Ahmad Yani', 25, 'Laki-laki', '081234567892', 'Jl. Gatot Subroto No. 789, Jakarta', 'Kelas Dewasa', 'Pagi (06:00 - 07:00)', 'Approved');
