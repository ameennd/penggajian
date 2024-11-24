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

// Proses approval absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_date'])) {
    $approve_date = $_POST['approve_date'];
    $approve_query = "UPDATE absensi SET status_approval = 'approved' WHERE tanggal = ?";
    $stmt = $mysqli->prepare($approve_query);
    $stmt->bind_param("s", $approve_date);
    $stmt->execute();
    $stmt->close();
    $message = "Absensi untuk tanggal $approve_date telah disetujui.";
}

// Query untuk mengambil daftar tanggal absensi
$date_query = "SELECT DISTINCT tanggal FROM absensi ORDER BY tanggal DESC";
$date_result = $mysqli->query($date_query);

// Query untuk mengambil data absensi (default: semua data)
$selected_date = $_GET['tanggal'] ?? null;
if ($selected_date) {
    $query = "SELECT karyawan.nama, absensi.tanggal, absensi.jam_masuk, absensi.status_approval
              FROM absensi
              JOIN karyawan ON absensi.id_karyawan = karyawan.id_karyawan
              WHERE absensi.tanggal = ?
              ORDER BY absensi.jam_masuk ASC";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT karyawan.nama, absensi.tanggal, absensi.jam_masuk, absensi.status_approval
              FROM absensi
              JOIN karyawan ON absensi.id_karyawan = karyawan.id_karyawan
              ORDER BY absensi.tanggal DESC, absensi.jam_masuk ASC";
    $result = $mysqli->query($query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_date'])) {
    $approve_date = $_POST['approve_date'];
    
    // Approve absensi
    $approve_query = "UPDATE absensi SET status_approval = 'approved' WHERE tanggal = ?";
    $stmt = $mysqli->prepare($approve_query);
    $stmt->bind_param("s", $approve_date);
    $stmt->execute();
    $stmt->close();

    // Ambil data absensi yang di-approve untuk tanggal tersebut
    $fetch_query = "SELECT absensi.id_karyawan, COUNT(absensi.id_karyawan) AS total_kehadiran, karyawan.gaji_pokok 
                    FROM absensi 
                    JOIN karyawan ON absensi.id_karyawan = karyawan.id_karyawan 
                    WHERE absensi.tanggal = ? AND absensi.status_approval = 'approved' AND absensi.is_processed = FALSE
                    GROUP BY absensi.id_karyawan";
    $stmt = $mysqli->prepare($fetch_query);
    $stmt->bind_param("s", $approve_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Hitung total gaji dan simpan ke tabel laporan keuangan
    while ($row = $result->fetch_assoc()) {
        $id_karyawan = $row['id_karyawan'];
        $total_kehadiran = $row['total_kehadiran'];
        $gaji_pokok = $row['gaji_pokok'];
        $hari_kerja = 20; // Jumlah hari kerja dalam 1 bulan
        $gaji_final = ($gaji_pokok / $hari_kerja) * $total_kehadiran;

        // Simpan ke tabel laporan keuangan
        $insert_finance_query = "INSERT INTO laporan_keuangan (id_karyawan, total_kehadiran, gaji_final, tanggal) 
                                 VALUES (?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_finance_query);
        $insert_stmt->bind_param("iiis", $id_karyawan, $total_kehadiran, $gaji_final, $approve_date);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    // Tandai data absensi sebagai diproses
    $update_processed_query = "UPDATE absensi SET is_processed = TRUE WHERE tanggal = ?";
    $stmt = $mysqli->prepare($update_processed_query);
    $stmt->bind_param("s", $approve_date);
    $stmt->execute();
    $stmt->close();

    $message = "Absensi untuk tanggal $approve_date telah disetujui dan gaji telah dihitung.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi</title>
    <link rel="stylesheet" href="css/rekap_absensi.css">
</head>

<body>
    <header>
        <h1>Rekap Absensi</h1>
    </header>

    <main>
        <?php if (isset($message)) { ?>
        <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php } ?>

        <!-- Filter Tanggal -->
        <form action="rekap_absensi.php" method="GET">
            <label for="tanggal">Pilih Tanggal:</label>
            <select name="tanggal" id="tanggal" onchange="this.form.submit()">
                <option value="">Semua Tanggal</option>
                <?php while ($date_row = $date_result->fetch_assoc()) { ?>
                <option value="<?= $date_row['tanggal'] ?>"
                    <?= $selected_date == $date_row['tanggal'] ? 'selected' : '' ?>>
                    <?= $date_row['tanggal'] ?>
                </option>
                <?php } ?>
            </select>
        </form>

        <!-- Tabel Rekap Absensi -->
        <table border="1">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['jam_masuk']) ?></td>
                    <td><?= $row['status_approval'] === 'approved' ? 'Disetujui' : 'Pending' ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tombol Approve -->
        <?php if ($selected_date) { ?>
        <form action="rekap_absensi.php" method="POST">
            <input type="hidden" name="approve_date" value="<?= $selected_date ?>">
            <button type="submit">Approve Absensi</button>
        </form>
        <?php } ?>

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