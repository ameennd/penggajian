<?php
// Mulai sesi
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'sistem_penggajian');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari database
$query = "SELECT karyawan.nomor_karyawan, karyawan.nama, karyawan.jabatan, karyawan.gaji_pokok, absensi.tanggal, absensi.status_absensi 
          FROM karyawan 
          LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan
          ORDER BY absensi.tanggal DESC";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gaji</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navbar -->
    <header class="navbar">
        <h1>Laporan Gaji Karyawan</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="report-container">
        <h2>Daftar Laporan Gaji</h2>

        <!-- Filter Form -->
        <form action="" method="GET" class="filter-form">
            <label for="nama">Nama Karyawan:</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama karyawan">

            <label for="tanggal">Tanggal (YYYY-MM):</label>
            <input type="month" id="tanggal" name="tanggal">

            <button type="submit">Filter</button>
            <a href="laporan_gaji.php" class="btn-reset">Reset</a>
        </form>

        <!-- Tabel Laporan -->
        <table>
            <thead>
                <tr>
                    <th>Nomor Karyawan</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Gaji Pokok</th>
                    <th>Tanggal</th>
                    <th>Status Absensi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Filter data jika ada input
                if (isset($_GET['nama']) || isset($_GET['tanggal'])) {
                    $nama = $_GET['nama'] ?? '';
                    $tanggal = $_GET['tanggal'] ?? '';

                    $filterQuery = "SELECT karyawan.nomor_karyawan, karyawan.nama, karyawan.jabatan, karyawan.gaji_pokok, absensi.tanggal, absensi.status_absensi 
                                    FROM karyawan 
                                    LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan 
                                    WHERE karyawan.nama LIKE '%$nama%'";

                    if (!empty($tanggal)) {
                        $filterQuery .= " AND absensi.tanggal LIKE '$tanggal%'";
                    }

                    $filterQuery .= " ORDER BY absensi.tanggal DESC";
                    $result = $conn->query($filterQuery);
                }

                // Tampilkan data
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['nomor_karyawan'] . "</td>";
                        echo "<td>" . $row['nama'] . "</td>";
                        echo "<td>" . $row['jabatan'] . "</td>";
                        echo "<td>Rp " . number_format($row['gaji_pokok'], 0, ',', '.') . "</td>";
                        echo "<td>" . $row['tanggal'] . "</td>";
                        echo "<td>" . $row['status_absensi'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Sistem Penggajian. All rights reserved.</p>
    </footer>
</body>

</html>
<?php
// Tutup koneksi
$conn->close();
?>
