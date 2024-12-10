<?php
session_start();

// Cek apakah pengguna adalah direktur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'direktur') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Proses approve laporan rekap per bulan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_month'])) {
    $selected_month = $_POST['bulan'];

    $approve_query = "UPDATE laporan_rekap SET status_approval_direktur = 'approved' WHERE bulan = ?";
    $stmt = $mysqli->prepare($approve_query);
    $stmt->bind_param("s", $selected_month);

    if ($stmt->execute()) {
        $message = "Laporan rekap untuk bulan $selected_month berhasil di-approve oleh direktur.";
    } else {
        $message = "Gagal meng-approve laporan rekap: " . $stmt->error;
    }

    $stmt->close();
}

// Query untuk mengambil laporan berdasarkan data absensi yang sudah di-approve oleh payroll
$selected_month = $_GET['bulan'] ?? date('Y-m'); // Default bulan saat ini
$query = "
    SELECT 
        karyawan.jabatan,
        COUNT(karyawan.id_karyawan) AS total_karyawan,
        SUM(CASE WHEN absensi.id_karyawan IS NOT NULL THEN 1 ELSE 0 END) AS total_kehadiran
    FROM absensi
    JOIN karyawan ON absensi.id_karyawan = karyawan.id_karyawan
    WHERE DATE_FORMAT(absensi.tanggal, '%Y-%m') = ? AND absensi.status_approval = 'approved'
    GROUP BY karyawan.jabatan
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $selected_month);
$stmt->execute();
$result = $stmt->get_result();

// Ambil daftar bulan unik dari tabel absensi
$months_query = "SELECT DISTINCT DATE_FORMAT(tanggal, '%Y-%m') AS bulan FROM absensi WHERE status_approval = 'approved' ORDER BY bulan DESC";
$months_result = $mysqli->query($months_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap</title>
    <link rel="stylesheet" href="css/laporan_rekap.css">
</head>

<body>
    <header>
        <h1>Laporan Rekap</h1>
    </header>

    <main>
        <?php if (isset($message)) { ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php } ?>

        <!-- Filter Bulan -->
        <form method="GET" action="laporan_rekap.php">
            <label for="bulan">Pilih Bulan:</label>
            <select name="bulan" id="bulan" onchange="this.form.submit()">
                <?php while ($row = $months_result->fetch_assoc()) { ?>
                    <option value="<?= $row['bulan'] ?>" <?= $row['bulan'] === $selected_month ? 'selected' : '' ?>>
                        <?= $row['bulan'] ?>
                    </option>
                <?php } ?>
            </select>
        </form>

        <!-- Tabel Laporan -->
        <table>
            <thead>
                <tr>
                    <th>Jabatan</th>
                    <th>Total Karyawan</th>
                    <th>Total Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td><?= htmlspecialchars($row['total_karyawan']) ?></td>
                        <td><?= htmlspecialchars($row['total_kehadiran']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tombol Approve -->
        <?php if ($result->num_rows > 0) { ?>
            <form method="POST" action="">
                <input type="hidden" name="bulan" value="<?= $selected_month ?>">
                <button type="submit" name="approve_month" class="btn-approve">Approve Bulan Ini</button>
            </form>
        <?php } ?>

        <a href="dashboard.php" class="back-btn">Kembali ke Dashboard</a>
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