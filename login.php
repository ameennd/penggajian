<?php
include('koneksi.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $login = $_POST['login'];
  $password = $_POST['password'];

  // Validasi input
  if (empty($login) || empty($password)) {
    $_SESSION['login_error'] = 'Username atau password tidak boleh kosong.';
  } else {
    // Query untuk memeriksa kecocokan login dan password
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $login, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      // Pengguna ditemukan, set session dan arahkan ke index.php
      $_SESSION['login'] = $login;
      header('Location: index.php');
      exit();
    } else {
      // Pengguna tidak ditemukan
      $_SESSION['login_error'] = 'Username atau password salah.';
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="./css/stylelogin.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  </head>
  <body>
    <div class="container">
      <div class="login-box">
        <div class="logo">
          <img src="logo.png" alt="Logo" />
        </div>
        <h2>Sign in</h2>
        <p>Sign in and start managing!</p>
<form action="login.php" method="POST">
  <div class="input-group">
    <label for="login">Login</label>
    <input type="text" id="login" name="login" required />
  </div>
  <div class="input-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required />
  </div>
  <button type="submit" class="btn">Login</button>
</form>
      </div>
    </div>
    <?php
if (isset($_SESSION['login_error'])) {
  echo "<script>
    Swal.fire({
      icon: 'error',
      title: 'Login Gagal',
      text: '{$_SESSION['login_error']}',
      showConfirmButton: true
    });
  </script>";
  unset($_SESSION['login_error']);
}
?>

  </body>
</html>
