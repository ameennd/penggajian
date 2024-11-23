<?php
// Halaman Dashboard Role-Based

session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Dashboard <?= ucfirst($role) ?></h1>
    </header>

    <main>
        <?php if ($role === 'payroll') { ?>
            <h2>Payroll Dashboard</h2>
            <ul>
                <li><a href="rekap_absensi.php">Rekap Absensi</a></li>
                <li><a href="generate_slip.php">Generate Slip Gaji</a></li>
            </ul>
        <?php } elseif ($role === 'finance') { ?>
            <h2>Finance Dashboard</h2>
            <ul>
                <li><a href="laporan_keuangan.php">Laporan Keuangan</a></li>
                <li><a href="proses_pembayaran.php">Proses Pembayaran Gaji</a></li>
            </ul>
        <?php } elseif ($role === 'direktur') { ?>
            <h2>Direktur Dashboard</h2>
            <ul>
                <li><a href="laporan_rekap.php">Laporan Rekap</a></li>
                <li><a href="laporan_gaji.php">Laporan Gaji</a></li>
            </ul>
        <?php } else { ?>
            <p>Role tidak dikenali.</p>
        <?php } ?>

        <a href="logout.php">Logout</a>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>
</body>
</html>