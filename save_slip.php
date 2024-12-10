<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'payroll') {
    header("Content-Type: application/json");
    echo json_encode(["status" => "error", "message" => "Akses ditolak."]);
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    header("Content-Type: application/json");
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal."]);
    exit();
}

$id_karyawan = $_GET['id'];

$query = "INSERT INTO slip_gaji (id_karyawan, status_approval, created_at) VALUES (?, 'pending', NOW())";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_karyawan);

if ($stmt->execute()) {
    header("Content-Type: application/json");
    echo json_encode(["status" => "success", "message" => "Slip gaji berhasil disimpan."]);
} else {
    header("Content-Type: application/json");
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan slip gaji."]);
}

$stmt->close();
$mysqli->close();