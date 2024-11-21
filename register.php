<?php

require 'koneksi.php';
$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];
$confirmpassword = $_POST["confirm-password"];

$query_sql = "INSERT INTO users (username, email, password, confirmpassword)
              VALUES ('$username', '$email', '$password', '$confirmpassword')";

                if (mysqli_query($conn, $query_sql))
                {
                    header('Location: login.php');
                } 
                 else 
                {
                    echo 'Pendaftaran Gagal : ' . mysqli_error($conn);
                }
?>