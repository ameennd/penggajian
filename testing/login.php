<?php
// Halaman Login untuk Staff Payroll, Finance, dan Direktur

session_start(); // Inisialisasi sesi

// Cek jika pengguna sudah login
if (isset($_SESSION['role'])) {
    header("Location: dashboard.php"); // Arahkan ke dashboard jika sudah login
    exit();
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Koneksi database
    $mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

    if ($mysqli->connect_error) {
        die("Koneksi database gagal: " . $mysqli->connect_error);
    }

    // Cek kredensial pengguna
    $stmt = $mysqli->prepare("SELECT id, role FROM pengguna WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->bind_result($id, $role);
    $stmt->fetch();

    if ($id) {
        // Login berhasil, simpan informasi ke sesi
        $_SESSION['id'] = $id;
        $_SESSION['role'] = $role;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah.";
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Login Sistem Penggajian</h1>
    </header>

    <main>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <?php if (isset($error)) { ?>
            <p style="color: red;"><?= $error ?></p>
        <?php } ?>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>
</body>
</html>