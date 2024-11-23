<?php
// Struktur Awal Sistem Penggajian
// Halaman Utama untuk Absensi Karyawan menggunakan Sidik Jari
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penggajian - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Sistem Informasi Penggajian</h1>
    </header>

    <main>
        <section>
            <h2>Absensi Karyawan</h2>
            <form action="solution_x105_integration.php" method="POST">
                <label for="fingerprint">Letakkan Sidik Jari Anda:</label>
                <p>Pastikan perangkat sidik jari terhubung.</p>
                <button type="submit">Mulai Absen</button>
            </form>
        </section>

        <section>
            <h2>Login Staff</h2>
            <a href="login.php">Login untuk Staff Payroll, Finance, dan Direktur</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>
</body>
</html>