<?php
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

// Jumlah hari kerja dalam 1 bulan
$hari_kerja = 20;

// Query untuk mengambil data karyawan dan menghitung total gaji berdasarkan kehadiran yang valid
$query = "
    SELECT 
        karyawan.id_karyawan, 
        karyawan.nama, 
        karyawan.jabatan, 
        karyawan.gaji_pokok, 
        COUNT(absensi.id_karyawan) AS total_hadir,
        (karyawan.gaji_pokok / $hari_kerja) * COUNT(absensi.id_karyawan) AS gaji_total
    FROM karyawan
    LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan 
        AND absensi.status_approval = 'approved'
    GROUP BY karyawan.id_karyawan
";

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
    <link rel="stylesheet" href="css/generate_slip.css">
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
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td>Rp <?= number_format($row['gaji_pokok'], 2, ',', '.') ?></td>
                        <td><?= $row['total_hadir'] ?></td>
                        <td>Rp <?= number_format($row['gaji_total'], 2, ',', '.') ?></td>
                        <td>
                            <!-- Link untuk menyimpan slip gaji sementara -->
                            <a href="save_slip.php?id=<?= $row['id_karyawan'] ?>">Simpan Slip</a>
                        </td>
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