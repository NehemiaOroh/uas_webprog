<?php
$host = 'localhost'; // biasanya 'localhost' jika berjalan di server lokal
$dbname = 'unite';   // nama database
$username = 'root';  // username database MySQL (default: root)
$password = '';      // password untuk user MySQL (default: kosong di XAMPP)

try {
    // Membuat koneksi menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set agar PDO menangani kesalahan sebagai Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set karakter yang digunakan ke UTF-8
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error
    die("Connection failed: " . $e->getMessage());
}
?>
