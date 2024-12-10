<?php
session_start();

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Ambil data NRK
$nrk = $_POST['cetak_nrk'] ?? null;

if (!$nrk) {
    echo "Nomor Karyawan (NRK) wajib diisi.";
    exit();
}

// Periksa apakah NRK valid dan sudah di-approve
$query = "
    SELECT slip_gaji.*, karyawan.nama, karyawan.jabatan 
    FROM slip_gaji 
    JOIN karyawan ON slip_gaji.id_karyawan = karyawan.id_karyawan 
    WHERE karyawan.no_karyawan = ? AND slip_gaji.status_approval = 'approved'
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $nrk);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Slip gaji belum di-approve oleh direktur atau NRK tidak valid.";
    exit();
}

$slip = $result->fetch_assoc();

// Tampilkan Slip Gaji
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <link rel="stylesheet" href="css/cetak_slip.css">
</head>

<body>
    <header>
        <h1>Slip Gaji</h1>
    </header>

    <main>
        <div class="slip-container">
            <p><strong>Nama Karyawan:</strong> <?= htmlspecialchars($slip['nama']) ?></p>
            <p><strong>Jabatan:</strong> <?= htmlspecialchars($slip['jabatan']) ?></p>
            <p><strong>Bulan:</strong> <?= htmlspecialchars($slip['bulan']) ?></p>
            <p><strong>Total Kehadiran:</strong> <?= htmlspecialchars($slip['total_kehadiran']) ?></p>
            <p><strong>Gaji Final:</strong> Rp <?= number_format($slip['gaji_final'], 2, ',', '.') ?></p>
        </div>

        <button onclick="window.print()">Cetak Slip</button>
        <a href="index.php" class="btn-back">Kembali</a>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>
</body>

</html>