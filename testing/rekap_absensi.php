<?php
// Halaman Rekap Absensi untuk Staff Payroll

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

// Query untuk mengambil data absensi
$query = "SELECT karyawan.nama, absensi.tanggal, absensi.jam_masuk
          FROM absensi
          JOIN karyawan ON absensi.id_karyawan = karyawan.id_karyawan
          ORDER BY absensi.tanggal DESC";

$result = $mysqli->query($query);

if (!$result) {
    die("Error saat mengambil data absensi: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Rekap Absensi</h1>
    </header>

    <main>
        <table border="1">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['jam_masuk']) ?></td>
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