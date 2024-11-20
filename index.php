<?php
include ('koneksi.php');

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./css/style.css" />
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="logo">
          <img src="logo.png" alt="Logo" />
        </div>
        <ul class="nav">
          <li>
            <a href="index.html"><i class="bi bi-house-door"></i> Dashboard</a>
          </li>
          <li class="dropdown">
            <a
              href="#"
              class="dropdown-toggle"
              id="dataMaster"
              data-toggle="collapse"
              data-target="#dataMasterMenu"
              aria-expanded="false"
            >
              <i class="bi bi-stack"></i> Data Master
              </i>
            </a>
            <ul class="collapse" id="dataMasterMenu">
              <li>
                <a href="datakaryawan.html"
                  ><i class="bi bi-person-lines-fill"></i> Data Karyawan</a
                >
              </li>
              <li>
                <a href="#"><i class="bi bi-briefcase"></i> Data Jabatan</a>
              </li>
            </ul>
          </li>

          <li>
            <a href="absen.html"><i class="bi bi-calendar-check"></i> Absen</a>
          </li>
          <li>
            <a href="gaji.html"><i class="bi bi-wallet2"></i> Gaji</a>
          </li>
          
        </ul>
      </div>

      <!-- Main Content -->
      <div class="content">
        <div class="header">
          <h1>Dashboard</h1>
          <div class="user-profile">
            <i class="bi bi-person-circle"></i>
          </div>
        </div>

        <div class="cards">
          <div class="card">
            <h3>Jumlah Karyawan</h3>
            <p>5</p>
            <a href="datakaryawan.html">Lihat &rarr;</a>
          </div>
          <div class="card">
            <h3>Absen</h3>
            <p>0</p>
            <a href="absen.html">Lihat &rarr;</a>
          </div>
          <div class="card">
            <h3>Pinjaman</h3>
            <p>3</p>
            <a href="#">Lihat &rarr;</a>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>
