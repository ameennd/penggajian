<?php
// Halaman untuk Generate Slip Gaji

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

// Query untuk mengambil data karyawan dan gaji
$query = "SELECT karyawan.id_karyawan, karyawan.nama, karyawan.jabatan, karyawan.gaji_pokok, 
                 SUM(CASE WHEN absensi.id_karyawan IS NOT NULL THEN 1 ELSE 0 END) AS total_hadir
          FROM karyawan
          LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan
          GROUP BY karyawan.id_karyawan";

$result = $mysqli->query($query);

if (!$result) {
    die("Error saat mengambil data gaji: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Slip Gaji</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Generate Slip Gaji</h1>
    </header>

    <main>
        <table border="1">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Gaji Pokok</th>
                    <th>Total Kehadiran</th>
                    <th>Gaji Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { 
                    $gaji_total = $row['gaji_pokok'] + ($row['total_hadir'] * 50000); // Tambahkan bonus per kehadiran
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td><?= number_format($row['gaji_pokok'], 2) ?></td>
                        <td><?= $row['total_hadir'] ?></td>
                        <td><?= number_format($gaji_total, 2) ?></td>
                        <td><a href="cetak_slip.php?id=<?= $row['id_karyawan'] ?>">Cetak Slip</a></td>
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