<?php
include ('koneksi.php');

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="./css/stylesignup.css" />
  </head>
  <body>
    <div class="container">
      <div class="login-box">
        <div class="logo">
          <img src="logo.png" alt="Logo" />
        </div>
        <h2>Create an Account</h2>
        <p>Sign up and start managing your account!</p>
        <form action="register.php" method="POST">
          <div class="input-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required />
          </div>
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <div class="input-group">
            <label for="confirm-password">Confirm Password</label>
            <input
              type="password"
              id="confirm-password"
              name="confirm-password"
              required
            />
          </div>
          <div class="remember-me">
            <input
              type="checkbox"
              id="agree-terms"
              name="agree-terms"
              required
            />
            <label for="agree-terms">I agree to the Terms and Conditions</label>
          </div>
          <a href="login.php">
            <button type="submit" class="btn">Sign Up</button>
          </a>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </div>
  </body>
</html>
