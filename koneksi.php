<?php
$servername = "localhost";
$database = "sistem_penggajian_berbasis_web";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn){
    die("Koneksi gagal : " . mysqli_connect_error());

}else {
    // echo "Koneksi Berhasil";
}