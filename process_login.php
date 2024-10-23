<?php
session_start();

// Simulasi data pengguna (username dan password) dari database
$valid_username = "user123";
$valid_password = "password123";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi login
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;  // Menyimpan username di sesi
        header('Location: index.php');      // Redirect ke halaman utama setelah login berhasil
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password!";
        header('Location: login.php');      // Redirect ke halaman login jika gagal
        exit();
    }
}
?>
