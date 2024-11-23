<?php
// File: solution_x105_integration.php
// Deskripsi: Skrip untuk mengambil data absensi dari perangkat Solution X105 menggunakan SDK

// Pastikan SDK sudah di-load ke sistem (contoh: via extension .dll atau .so)

// Langkah awal: koneksi ke perangkat Solution X105
// Pastikan Anda mengganti IP dan port dengan konfigurasi perangkat Anda
$deviceIP = '192.168.1.100'; // IP perangkat Solution X105
$devicePort = 4370; // Port default untuk komunikasi perangkat

// Fungsi untuk membuka koneksi ke perangkat
function connectToDevice($ip, $port) {
    $connection = fsockopen($ip, $port, $errno, $errstr, 5); // timeout 5 detik

    if (!$connection) {
        die("Koneksi ke perangkat gagal: [$errno] $errstr\n");
    }

    return $connection;
}

// Fungsi untuk menarik data absensi dari perangkat
function getAttendanceLog($connection) {
    // Perintah SDK atau protokol khusus untuk menarik log absensi
    // Perhatikan dokumentasi SDK untuk perintah ini

    // Contoh pseudo-code:
    fwrite($connection, "GET LOG\n");
    $response = fread($connection, 1024); // Baca respons data absensi

    // Parsing data log absensi sesuai format SDK
    $attendanceData = [];
    // Misalnya, setiap log adalah baris baru yang dipisahkan dengan newline
    $logs = explode("\n", $response);

    foreach ($logs as $log) {
        $fields = explode(",", $log); // Misal data dipisah dengan koma
        if (count($fields) >= 3) {
            $attendanceData[] = [
                'id_karyawan' => $fields[0],
                'tanggal' => $fields[1],
                'jam' => $fields[2],
            ];
        }
    }

    return $attendanceData;
}

// Fungsi untuk menyimpan data ke database
function saveAttendanceToDatabase($attendanceData) {
    // Koneksi database
    $mysqli = new mysqli("localhost", "root", "", "sistem_penggajian");

    if ($mysqli->connect_error) {
        die("Koneksi database gagal: " . $mysqli->connect_error);
    }

    foreach ($attendanceData as $data) {
        $stmt = $mysqli->prepare("INSERT INTO absensi (id_karyawan, tanggal, jam_masuk) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['id_karyawan'], $data['tanggal'], $data['jam']);

        if (!$stmt->execute()) {
            echo "Gagal menyimpan log absensi: " . $stmt->error . "\n";
        }
    }

    $mysqli->close();
}

// Main execution
$connection = connectToDevice($deviceIP, $devicePort);
$attendanceLogs = getAttendanceLog($connection);

if (!empty($attendanceLogs)) {
    saveAttendanceToDatabase($attendanceLogs);
    echo "Log absensi berhasil disimpan ke database.\n";
} else {
    echo "Tidak ada data absensi yang ditemukan.\n";
}

// Tutup koneksi perangkat
fclose($connection);
?>