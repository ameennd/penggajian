<?php
session_start(); // Inisialisasi sesi

// Cek jika pengguna sudah login
if (isset($_SESSION['role'])) {
    header("Location: dashboard.php"); // Redirect ke dashboard jika sudah login
    exit();
}

// Variabel untuk pesan error
$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Koneksi database
    $conn = new mysqli("localhost", "root", "", "sistem_penggajian");

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Query untuk cek kredensial pengguna
    $stmt = $conn->prepare("SELECT id, role FROM pengguna WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->bind_result($id, $role);
    $stmt->fetch();

    if ($id) {
        // Jika login berhasil
        $_SESSION['id'] = $id;
        $_SESSION['role'] = $role;

        header("Location: dashboard.php"); // Redirect ke dashboard
        exit();
    } else {
        // Jika login gagal
        $error = "Username atau password salah.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <main class="login-container">
        <!-- Logo di belakang -->
        <div class="login-logo">
            <img src="img/logo.png" alt="Logo Sistem Penggajian">
        </div>

        <!-- Form Login -->
        <form class="login-form" action="login.php" method="POST">
            <h2>Login Sistem</h2>

            <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Masukkan Username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Masukkan Password" required>

            <button type="submit">Login</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Sistem Penggajian. All rights reserved.</p>
    </footer>
</body>

</html>