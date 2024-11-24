<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index - Sistem Penggajian</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">
            <img src="img/logo.png" alt="Logo" class="logo-img">
            PayZen
        </div>
        <nav>
            <a href="login.php" class="btn">Login</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container">
        <section class="cards">
            <!-- Card Absensi NRK -->
            <div class="card">
                <h2>Absensi NRK</h2>
                <form id="form-absensi" method="POST">
                    <label for="no_karyawan">Nomor Karyawan (NRK):</label>
                    <input type="text" id="no_karyawan" name="no_karyawan" placeholder="Masukkan NRK" required>

                    <label for="status_absensi">Status Absensi:</label>
                    <select id="status_absensi" name="status_absensi" required>
                        <option value="Hadir">Hadir</option>
                        <option value="Telat">Telat</option>
                        <option value="Izin">Izin</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Sakit">Sakit</option>
                    </select>

                    <button type="submit" class="btn">Kirim Absensi</button>
                </form>
            </div>

            <!-- Card Cetak Slip Gaji -->
            <div class="card">
                <h2>Cetak Slip Gaji</h2>
                <form id="form-cetak" method="POST">
                    <label for="cetak_nrk">Nomor Karyawan (NRK):</label>
                    <input type="text" id="cetak_nrk" name="cetak_nrk" placeholder="Masukkan NRK" required>
                    <button type="submit" class="btn">Cetak Slip</button>
                </form>
            </div>
        </section>
    </main>

    <!-- Pop-up Notification -->
    <div id="popup" class="popup hidden">
        <div class="popup-content">
            <h3 id="popup-message">Notifikasi</h3>
            <button onclick="closePopup()">OK</button>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Sistem Penggajian. All rights reserved.</p>
    </footer>

    <script src="js/index.js"></script>
</body>

</html>