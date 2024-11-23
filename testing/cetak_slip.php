<?php
// Halaman untuk mencetak slip gaji karyawan

session_start();

// Cek apakah pengguna sudah login dan memiliki peran yang sesuai
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'payroll') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Ambil ID karyawan dari parameter URL
$id_karyawan = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id_karyawan) {
    die("ID karyawan tidak valid.");
}

// Query untuk mengambil data karyawan dan gaji
$query = "SELECT karyawan.nama, karyawan.jabatan, karyawan.gaji_pokok, 
                 SUM(CASE WHEN absensi.id_karyawan IS NOT NULL THEN 1 ELSE 0 END) AS total_hadir
          FROM karyawan
          LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan
          WHERE karyawan.id_karyawan = ?
          GROUP BY karyawan.id_karyawan";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_karyawan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data karyawan tidak ditemukan.");
}

$row = $result->fetch_assoc();

// Hitung gaji total
$gaji_total = $row['gaji_pokok'] + ($row['total_hadir'] * 50000); // Tambahkan bonus per kehadiran

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Slip Gaji Karyawan</h1>
    </header>

    <main>
        <section>
            <h2>Detail Gaji</h2>
            <p><strong>Nama:</strong> <?= htmlspecialchars($row['nama']) ?></p>
            <p><strong>Jabatan:</strong> <?= htmlspecialchars($row['jabatan']) ?></p>
            <p><strong>Gaji Pokok:</strong> <?= number_format($row['gaji_pokok'], 2) ?></p>
            <p><strong>Total Kehadiran:</strong> <?= $row['total_hadir'] ?></p>
            <p><strong>Gaji Total:</strong> <?= number_format($gaji_total, 2) ?></p>
        </section>

        <button onclick="window.print();">Cetak Slip Gaji</button>
        <a href="generate_slip.php">Kembali ke Generate Slip</a>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>
</body>
</html>