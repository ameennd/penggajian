<?php
session_start();

// Cek apakah pengguna adalah staff payroll
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'payroll') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Ambil ID karyawan
$id_karyawan = $_GET['id'];

// Simpan slip gaji sementara
$query = "INSERT INTO slip_gaji (id_karyawan, status_approval, created_at) VALUES (?, 'pending', NOW())";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_karyawan);

if ($stmt->execute()) {
    echo "Slip gaji berhasil disimpan dan menunggu approval direktur.";
} else {
    echo "Gagal menyimpan slip gaji: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>