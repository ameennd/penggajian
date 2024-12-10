<?php
session_start();

// Redirect ke halaman login jika pengguna belum login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Ambil role pengguna
$role = ucfirst($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= $role ?></title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <header>
        <h1>Dashboard <?= $role ?></h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="dashboard">
            <?php if ($_SESSION['role'] === 'payroll') { ?>
                <div class="card">
                    <h2>Rekap Absensi</h2>
                    <p>Lihat dan kelola data kehadiran karyawan.</p>
                    <a href="rekap_absensi.php" class="btn">Lihat Rekap</a>
                </div>
                <div class="card">
                    <h2>Generate Slip Gaji</h2>
                    <p>Kelola slip gaji untuk karyawan.</p>
                    <a href="generate_slip.php" class="btn">Generate Slip</a>
                </div>
            <?php } elseif ($_SESSION['role'] === 'finance') { ?>
                <div class="card">
                    <h2>Proses Pembayaran</h2>
                    <p>Kelola pembayaran gaji karyawan.</p>
                    <a href="proses_pembayaran.php" class="btn">Proses Gaji</a>
                </div>
            <?php } elseif ($_SESSION['role'] === 'direktur') { ?>
                <div class="card">
                    <h2>Laporan Rekap</h2>
                    <p>Monitor data rekap penggajian dan kinerja.</p>
                    <a href="laporan_rekap.php" class="btn">Lihat Rekap</a>
                </div>
                <div class="card">
                    <h2>Laporan Gaji</h2>
                    <p>Detail laporan gaji karyawan.</p>
                    <a href="laporan_gaji.php" class="btn">Lihat Laporan</a>
                </div>
            <?php } ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian</p>
    </footer>
</body>

</html>