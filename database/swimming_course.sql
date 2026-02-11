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
