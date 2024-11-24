<?php
session_start();

// Cek apakah pengguna adalah staff finance
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'finance') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Proses approve untuk semua data dalam 1 bulan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_month'])) {
    $selected_month = $_POST['month']; // Format: YYYY-MM

    // Update semua data dalam bulan yang dipilih
    $query = "UPDATE laporan_keuangan 
              SET status_approval_finance = 'approved' 
              WHERE DATE_FORMAT(created_at, '%Y-%m') = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $selected_month);
    $stmt->execute();
    $stmt->close();

    $message = "Semua laporan untuk bulan $selected_month telah disetujui.";
}

$selected_month = $_POST['month'] ?? $_GET['month'] ?? null;

// Ambil data laporan keuangan dengan filter bulan jika ada
if ($selected_month) {
    $query = "
        SELECT 
            laporan_keuangan.id_laporan, 
            karyawan.nama AS nama_karyawan, 
            laporan_keuangan.total_kehadiran, 
            laporan_keuangan.gaji_final, 
            laporan_keuangan.status_approval_finance, 
            DATE_FORMAT(laporan_keuangan.created_at, '%Y-%m') AS month 
        FROM laporan_keuangan
        JOIN karyawan ON laporan_keuangan.id_karyawan = karyawan.id_karyawan
        WHERE DATE_FORMAT(laporan_keuangan.created_at, '%Y-%m') = ?
        ORDER BY laporan_keuangan.created_at DESC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $selected_month);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "
        SELECT 
            laporan_keuangan.id_laporan, 
            karyawan.nama AS nama_karyawan, 
            laporan_keuangan.total_kehadiran, 
            laporan_keuangan.gaji_final, 
            laporan_keuangan.status_approval_finance, 
            DATE_FORMAT(laporan_keuangan.created_at, '%Y-%m') AS month 
        FROM laporan_keuangan
        JOIN karyawan ON laporan_keuangan.id_karyawan = karyawan.id_karyawan
        ORDER BY laporan_keuangan.created_at DESC
    ";
    $result = $mysqli->query($query);
}

// Ambil daftar bulan dari laporan keuangan
$months_query = "SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') AS month FROM laporan_keuangan";
$months_result = $mysqli->query($months_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" href="css/laporan_keuangan.css">
</head>

<body>
    <header>
        <h1>Laporan Keuangan</h1>
    </header>
    <main>
        <?php if (isset($message)) { ?>
        <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php } ?>

        <!-- Filter Berdasarkan Bulan -->
        <form method="POST" action="laporan_keuangan.php">
            <label for="month">Pilih Bulan:</label>
            <select name="month" id="month" required>
                <option value="">-- Pilih Bulan --</option>
                <?php while ($row = $months_result->fetch_assoc()) { ?>
                <option value="<?= $row['month'] ?>"><?= $row['month'] ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="approve_month">Approve Semua</button>
        </form>

        <!-- Tabel Data Laporan -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Karyawan</th>
                    <th>Total Kehadiran</th>
                    <th>Gaji Final</th>
                    <th>Status Approval Finance</th>
                    <th>Bulan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_laporan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                    <td><?= htmlspecialchars($row['total_kehadiran']) ?></td>
                    <td>Rp <?= number_format($row['gaji_final'], 2, ',', '.') ?></td>
                    <td><?= $row['status_approval_finance'] === 'approved' ? 'Disetujui' : 'Pending' ?></td>
                    <td><?= htmlspecialchars($row['month']) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="back-btn">Kembali ke Dashboard</a>

    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian</p>
    </footer>
</body>

</html>

<?php
$mysqli->close();
?>