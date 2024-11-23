<?php
// Halaman untuk menampilkan laporan rekap untuk direktur

session_start();

// Cek apakah pengguna sudah login dan memiliki peran yang sesuai
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'direktur') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Query untuk mengambil data rekap keuangan dan kehadiran
$query = "SELECT karyawan.jabatan, 
                 COUNT(karyawan.id_karyawan) AS total_karyawan, 
                 SUM(karyawan.gaji_pokok) AS total_gaji_pokok, 
                 SUM(CASE WHEN absensi.id_karyawan IS NOT NULL THEN 1 ELSE 0 END) AS total_kehadiran,
                 SUM(karyawan.gaji_pokok + (CASE WHEN absensi.id_karyawan IS NOT NULL THEN 50000 ELSE 0 END)) AS total_pengeluaran
          FROM karyawan
          LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan
          GROUP BY karyawan.jabatan";

$result = $mysqli->query($query);

if (!$result) {
    die("Error saat mengambil data laporan rekap: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Laporan Rekap</h1>
    </header>

    <main>
        <table border="1">
            <thead>
                <tr>
                    <th>Jabatan</th>
                    <th>Total Karyawan</th>
                    <th>Total Gaji Pokok</th>
                    <th>Total Kehadiran</th>
                    <th>Total Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td><?= $row['total_karyawan'] ?></td>
                        <td><?= number_format($row['total_gaji_pokok'], 2) ?></td>
                        <td><?= $row['total_kehadiran'] ?></td>
                        <td><?= number_format($row['total_pengeluaran'], 2) ?></td>
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