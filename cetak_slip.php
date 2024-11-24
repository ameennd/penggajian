<?php
// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Ambil data dari form
$nrk = $_POST['cetak_nrk'];

// Query untuk mendapatkan slip gaji berdasarkan NRK yang sudah di-approve
$query = "SELECT karyawan.nama, slip_gaji.periode_bulan, slip_gaji.total_gaji, slip_gaji.tanggal_approve 
          FROM slip_gaji
          JOIN karyawan ON slip_gaji.id_karyawan = karyawan.id_karyawan
          WHERE karyawan.nomor_karyawan = ? AND slip_gaji.status_approve = 'Approved'";

$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $nrk);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $slip = $result->fetch_assoc();
    } else {
        echo "<script>alert('Slip gaji tidak ditemukan atau belum di-approve.'); window.location.href = 'index.php';</script>";
        exit();
    }
    $stmt->close();
} else {
    die("Query error: " . $mysqli->error);
}

// Tutup koneksi database
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <link rel="stylesheet" href="css/slip_gaji.css">
</head>

<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Sistem Penggajian</div>
    </header>

    <!-- Slip Gaji -->
    <main class="container">
        <section class="slip-gaji">
            <h2>Slip Gaji</h2>
            <p><strong>Nama Karyawan:</strong> <?= htmlspecialchars($slip['nama']) ?></p>
            <p><strong>Periode Bulan:</strong> <?= htmlspecialchars($slip['periode_bulan']) ?></p>
            <p><strong>Total Gaji:</strong> Rp <?= number_format($slip['total_gaji'], 2, ',', '.') ?></p>
            <p><strong>Tanggal Approve:</strong> <?= htmlspecialchars($slip['tanggal_approve']) ?></p>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Sistem Penggajian. All rights reserved.</p>
    </footer>
</body>

</html>