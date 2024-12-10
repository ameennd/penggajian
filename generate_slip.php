<?php
session_start();

// Cek apakah pengguna adalah staff payroll
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'payroll') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Jumlah hari kerja dalam 1 bulan
$hari_kerja = 20;

// Query untuk mengambil data karyawan dan menghitung total gaji berdasarkan kehadiran yang valid
$query = "
    SELECT 
        karyawan.id_karyawan, 
        karyawan.nama, 
        karyawan.jabatan, 
        karyawan.gaji_pokok, 
        COUNT(absensi.id_karyawan) AS total_hadir,
        (karyawan.gaji_pokok / $hari_kerja) * COUNT(absensi.id_karyawan) AS gaji_total
    FROM karyawan
    LEFT JOIN absensi ON karyawan.id_karyawan = absensi.id_karyawan 
        AND absensi.status_approval = 'approved'
    GROUP BY karyawan.id_karyawan
";

$result = $mysqli->query($query);

if (!$result) {
    die("Error saat mengambil data gaji: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Slip Gaji</title>
    <link rel="stylesheet" href="css/generate_slip.css">
    <style>
        /* Styles for Popup */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 400px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            display: none;
            z-index: 1000;
        }

        .popup.success {
            border-left: 5px solid #28a745;
        }

        .popup.error {
            border-left: 5px solid #dc3545;
        }

        .popup h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .popup p {
            margin-bottom: 20px;
            color: #555;
        }

        .popup button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .popup button:hover {
            background-color: #0056b3;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .overlay.show {
            display: block;
        }
    </style>
</head>

<body>
    <header>
        <h1>Generate Slip Gaji</h1>
    </header>

    <main>
        <table border="1">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Gaji Pokok</th>
                    <th>Total Kehadiran</th>
                    <th>Gaji Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td>Rp <?= number_format($row['gaji_pokok'], 2, ',', '.') ?></td>
                        <td><?= $row['total_hadir'] ?></td>
                        <td>Rp <?= number_format($row['gaji_total'], 2, ',', '.') ?></td>
                        <td>
                            <button class="save-slip" data-id="<?= $row['id_karyawan'] ?>">Simpan Slip</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dashboard.php">Kembali ke Dashboard</a>
    </main>

    <!-- Overlay and Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="popup">
        <h3 id="popup-title"></h3>
        <p id="popup-message"></p>
        <button onclick="closePopup()">OK</button>
    </div>

    <footer>
        <p>&copy; 2024 Sistem Penggajian Karyawan</p>
    </footer>

    <script>
        // Close the popup
        function closePopup() {
            document.getElementById("popup").style.display = "none";
            document.getElementById("overlay").classList.remove("show");
        }

        // Add event listener to all "Simpan Slip" buttons
        document.querySelectorAll(".save-slip").forEach(button => {
            button.addEventListener("click", function () {
                const id = this.getAttribute("data-id");

                // Send AJAX request
                fetch("save_slip.php?id=" + id, {
                    method: "GET",
                })
                    .then(response => response.json())
                    .then(data => {
                        const popup = document.getElementById("popup");
                        const overlay = document.getElementById("overlay");
                        const title = document.getElementById("popup-title");
                        const message = document.getElementById("popup-message");

                        if (data.status === "success") {
                            title.innerText = "Berhasil";
                            popup.classList.add("success");
                            popup.classList.remove("error");
                        } else {
                            title.innerText = "Gagal";
                            popup.classList.add("error");
                            popup.classList.remove("success");
                        }
                        message.innerText = data.message;

                        popup.style.display = "block";
                        overlay.classList.add("show");
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
            });
        });
    </script>
</body>

</html>