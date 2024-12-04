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

// Validasi input
if (!$no_karyawan || !$status_absensi) {
    echo json_encode([
        "success" => false,
        "message" => "Nomor Karyawan dan Status Absensi wajib diisi.",
    ]);
    exit();
}

// Periksa apakah nomor karyawan valid
$query = $conn->prepare("SELECT id_karyawan, nama FROM karyawan WHERE no_karyawan = ?");
$query->bind_param('s', $no_karyawan);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_karyawan = $row['id_karyawan'];
    $nama_karyawan = $row['nama'];
    $tanggal = date('Y-m-d');
    $jam_masuk = date('H:i:s');

    // Set nilai default untuk status_approval dan is_processed
    $status_approval = 'pending';  // default status approval
    $is_processed = 0;  // status tidak diproses, 0 berarti belum diproses

    // Simpan data absensi ke dalam tabel
    $insert_query = $conn->prepare("INSERT INTO absensi (id_karyawan, tanggal, jam_masuk, status_absensi, status_approval, is_processed) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param('isssss', $id_karyawan, $tanggal, $jam_masuk, $status_absensi, $status_approval, $is_processed);

    if ($insert_query->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Absensi berhasil dicatat.",
            "data" => [
                "nama" => $nama_karyawan,
                "no_karyawan" => $no_karyawan,
            ],
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
        "message" => "Nomor karyawan tidak ditemukan.",
    ]);
}

$query->close();
$conn->close();
?>