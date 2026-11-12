-- =========================================================
-- DATABASE: db_elearning
-- Fokus entitas: users (admin & siswa), materis, soals
-- Import file ini lewat phpMyAdmin / mysql -u root -p < db_elearning.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_elearning
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_elearning;

-- ---------------------------------------------------------
-- TABEL: users  (menampung admin/guru dan pengguna/siswa)
-- ---------------------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa',
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABEL: materis  (modul materi + file upload)
-- ---------------------------------------------------------
CREATE TABLE materis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    file_name VARCHAR(255) NULL,       -- nama asli file
    file_path VARCHAR(255) NULL,       -- path/lokasi file tersimpan (storage)
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_materi_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABEL: soals  (bank soal pretest & posttest)
-- setiap baris = 1 soal dengan 4 pilihan jawaban (a,b,c,d)
-- ---------------------------------------------------------
CREATE TABLE soals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    materi_id BIGINT UNSIGNED NOT NULL,
    tipe ENUM('pretest', 'posttest') NOT NULL,
    pertanyaan TEXT NOT NULL,
    pilihan_a VARCHAR(500) NOT NULL,
    pilihan_b VARCHAR(500) NOT NULL,
    pilihan_c VARCHAR(500) NOT NULL,
    pilihan_d VARCHAR(500) NOT NULL,
    jawaban_benar ENUM('a', 'b', 'c', 'd') NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_soal_materi FOREIGN KEY (materi_id) REFERENCES materis(id) ON DELETE CASCADE,
    CONSTRAINT fk_soal_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- DATA CONTOH (untuk testing tampilan)
-- password untuk kedua akun contoh: "password"
-- hash di bawah adalah hasil bcrypt Laravel dari kata "password"
-- ---------------------------------------------------------
INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES
('Admin Guru', 'admin@sekolah.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
('Budi Siswa', 'siswa@sekolah.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'siswa', NOW(), NOW());

INSERT INTO materis (judul, deskripsi, file_name, file_path, created_by, created_at, updated_at) VALUES
('Pengenalan Aljabar', 'Materi dasar tentang aljabar untuk kelas 7', NULL, NULL, 1, NOW(), NOW());

INSERT INTO soals (materi_id, tipe, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, created_by, created_at, updated_at) VALUES
(1, 'pretest', 'Berapakah hasil dari 2x + 3 jika x = 4?', '11', '10', '9', '12', 'a', 1, NOW(), NOW()),
(1, 'pretest', 'Bentuk sederhana dari 3x + 2x adalah?', '5x', '6x', '5x^2', '3x2', 'a', 1, NOW(), NOW()),
(1, 'posttest', 'Jika 5x = 25, maka nilai x adalah?', '4', '5', '6', '7', 'b', 1, NOW(), NOW());
