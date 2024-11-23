<?php
// Halaman untuk menampilkan laporan keuangan

session_start();

// Cek apakah pengguna sudah login dan memiliki peran yang sesuai
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'finance') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Query untuk mengambil laporan keuangan
$query = "SELECT karyawan.nama, karyawan.jabatan, SUM(absensi.id_karyawan IS NOT NULL) AS total_hadir, 
                 karyawan.gaji_pokok + (SUM(absensi.id_karyawan IS NOT NULL) * 50000) AS total_gaji
          FROM karyawan
          LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan
          GROUP BY karyawan.id_karyawan";

$result = $mysqli->query($query);

if (!$result) {
    die("Error saat mengambil data laporan keuangan: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Laporan Keuangan</h1>
    </header>

    <main>
        <table border="1">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Total Kehadiran</th>
                    <th>Total Gaji</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td><?= $row['total_hadir'] ?></td>
                        <td><?= number_format($row['total_gaji'], 2) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dashboard.php">Kembali ke Dashboard</a>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>
</body>
</html>

<?php
// Tutup koneksi database
$mysqli->close();
?>