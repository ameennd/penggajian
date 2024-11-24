
-- File: database/sistem_penggajian.sql
CREATE DATABASE sistem_penggajian;

USE sistem_penggajian;

CREATE TABLE karyawan (
    id_karyawan INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    gaji_pokok DECIMAL(10, 2) NOT NULL
);

CREATE TABLE absensi (
    id_absensi INT AUTO_INCREMENT PRIMARY KEY,
    id_karyawan INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME NOT NULL,
    FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan)
);

CREATE TABLE pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('payroll', 'finance', 'direktur') NOT NULL
);
