<?php
// Set header untuk mengirimkan respon JSON
header('Content-Type: application/json');

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'sistem_penggajian');

// Periksa koneksi
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Koneksi database gagal: " . $conn->connect_error
    ]);
    exit();
}

// Ambil data dari form
$no_karyawan = $_POST['no_karyawan'] ?? null;
$status_absensi = $_POST['status_absensi'] ?? null;
$keterangan = $_POST['keterangan'] ?? null;

// Validasi input
if (!$no_karyawan || !$status_absensi) {
    echo json_encode([
        "success" => false,
        "message" => "Nomor Karyawan dan Status Absensi wajib diisi."
    ]);
    exit();
}

// Periksa apakah nomor karyawan valid
$query = $conn->prepare("SELECT id_karyawan, nama FROM karyawan WHERE nomor_karyawan = ?");
$query->bind_param('s', $no_karyawan);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_karyawan = $row['id_karyawan'];
    $nama_karyawan = $row['nama'];
    $tanggal = date('Y-m-d');
    $jam_masuk = date('H:i:s');
    $metode_absensi = 'Nomor Karyawan';

    // Simpan data absensi
    $insert_query = $conn->prepare("INSERT INTO absensi (id_karyawan, tanggal, jam_masuk, metode_absensi, status_absensi, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param('isssss', $id_karyawan, $tanggal, $jam_masuk, $metode_absensi, $status_absensi, $keterangan);

    if ($insert_query->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Absensi berhasil dicatat.",
            "data" => [
                "nama" => $nama_karyawan,
                "no_karyawan" => $no_karyawan
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Gagal mencatat absensi: " . $conn->error
        ]);
    }

    $insert_query->close();
} else {
    echo json_encode([
        "success" => false,
        "message" => "Nomor karyawan tidak ditemukan."
    ]);
}

$query->close();
$conn->close();
?>