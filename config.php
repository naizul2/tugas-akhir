<?php
// config.php - Konfigurasi Database
$servername = "localhost"; // Biasanya 'localhost' untuk pengembangan lokal
$username = "root";     // <<--- GANTI DENGAN USERNAME DATABASE ANDA
$password = "";         // <<--- GANTI DENGAN PASSWORD DATABASE ANDA (kosong jika tidak ada)
$dbname = "news_website"; // Nama database yang sudah Anda buat

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    // Tampilkan pesan error yang jelas jika koneksi gagal
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>