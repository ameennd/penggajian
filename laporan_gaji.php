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

// Proses approve slip gaji per bulan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_month'])) {
    $selected_month = $_POST['bulan'];

    $approve_query = "UPDATE slip_gaji SET status_approval = 'approved' WHERE bulan = ?";
    $stmt = $mysqli->prepare($approve_query);
    $stmt->bind_param("s", $selected_month);

    if ($stmt->execute()) {
        $message = "Slip gaji untuk bulan $selected_month berhasil di-approve oleh direktur.";
    } else {
        $message = "Gagal meng-approve slip gaji: " . $stmt->error;
    }

    $stmt->close();
}

// Query untuk mengambil slip gaji berdasarkan bulan tertentu
$selected_month = $_GET['bulan'] ?? date('Y-m'); // Default bulan saat ini
$query = "
    SELECT 
        karyawan.nama AS nama_karyawan,
        karyawan.jabatan,
        slip_gaji.total_kehadiran,
        slip_gaji.gaji_final,
        slip_gaji.status_approval
    FROM slip_gaji
    JOIN karyawan ON slip_gaji.id_karyawan = karyawan.id_karyawan
    WHERE slip_gaji.bulan = ?
    ORDER BY karyawan.nama ASC
";


$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $selected_month);
$stmt->execute();
$result = $stmt->get_result();

// Ambil daftar bulan unik dari tabel slip_gaji
$months_query = "SELECT DISTINCT bulan FROM slip_gaji ORDER BY bulan DESC";
$months_result = $mysqli->query($months_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gaji</title>
    <link rel="stylesheet" href="css/laporan_gaji.css">
</head>

<body>
    <header>
        <h1>Laporan Gaji</h1>
    </header>

    <main>
        <?php if (isset($message)) { ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php } ?>

        <!-- Filter Bulan -->
        <form method="GET" action="laporan_gaji.php">
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
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Total Kehadiran</th>
                    <th>Gaji Final</th>
                    <th>Status Approval</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td><?= htmlspecialchars($row['total_kehadiran']) ?></td>
                        <td>Rp <?= number_format($row['gaji_final'], 2, ',', '.') ?></td>
                        <td><?= $row['status_approval'] === 'approved' ? 'Disetujui' : 'Pending' ?></td>
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