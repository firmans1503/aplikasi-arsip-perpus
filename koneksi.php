<?php
// Nama server database
$host = "localhost";

// Username MySQL (default XAMPP)
$user = "root";

// Password MySQL (default XAMPP kosong)
$pass = "";

// Nama database di phpMyAdmin
$db   = "perpustakaan";  

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
