<?php
include ('koneksi.php');

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="./css/stylelogin.css" />
  </head>
  <body>
    <div class="container">
      <div class="login-box">
        <div class="logo">
          <img src="logo.png" alt="Logo" />
        </div>
        <h2>Sign in</h2>
        <p>Sign in and start managing!</p>
        <form action="#" method="POST">
          <div class="input-group">
            <label for="login">Login</label>
            <input type="text" id="login" name="login" required />
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <div class="remember-me">
            <input type="checkbox" id="remember-me" name="remember-me" />
            <label for="remember-me">Remember me</label>
          </div>
          <a href="#">Forgot password?</a>
          <p>
            <a href="signup.html">Resgister !</a>
          </p>
          <button type="submit" class="btn">Login</button>
        </form>
      </div>
    </div>
  </body>
</html>
