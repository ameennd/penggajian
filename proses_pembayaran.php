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

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_karyawan'], $_POST['jumlah_pembayaran'])) {
    $id_karyawan = $_POST['id_karyawan'];
    $jumlah_pembayaran = $_POST['jumlah_pembayaran'];

    // Simpan ke tabel pembayaran
    $query = "INSERT INTO pembayaran (id_karyawan, jumlah_pembayaran, status_pembayaran) 
              VALUES (?, ?, 'completed')";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("id", $id_karyawan, $jumlah_pembayaran);

    if ($stmt->execute()) {
        $message = "Pembayaran untuk karyawan ID $id_karyawan berhasil diproses.";
    } else {
        $message = "Gagal memproses pembayaran: " . $stmt->error;
    }

    $stmt->close();
}

// Ambil data slip gaji yang sudah di-approve direktur
$query = "
    SELECT karyawan.id_karyawan, karyawan.nama, karyawan.jabatan, slip_gaji.gaji_final
    FROM slip_gaji
    JOIN karyawan ON slip_gaji.id_karyawan = karyawan.id_karyawan
    WHERE slip_gaji.status_approval = 'approved' AND slip_gaji.id_karyawan NOT IN (
        SELECT id_karyawan FROM pembayaran WHERE status_pembayaran = 'completed'
    )";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pembayaran</title>
    <link rel="stylesheet" href="css/pembayaran.css">
</head>

<body>
    <header>
        <h1>Proses Pembayaran</h1>
    </header>

    <main>
        <?php if (isset($message)) { ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php } ?>

        <table>
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Gaji Final</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td>Rp <?= number_format($row['gaji_final'], 2, ',', '.') ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="id_karyawan" value="<?= $row['id_karyawan'] ?>">
                                <input type="hidden" name="jumlah_pembayaran" value="<?= $row['gaji_final'] ?>">
                                <button type="submit">Proses Pembayaran</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="back-btn">Kembali ke Dashboard</a>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>
</body>

</html>

<?php
$mysqli->close();
?>