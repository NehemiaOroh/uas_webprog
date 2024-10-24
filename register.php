<?php 
session_start();
require 'db_connect.php'; // Hubungkan dengan file koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; // Ambil nama dari form
    $email = $_POST['email']; // Ambil email dari form
    $password = $_POST['password']; // Ambil password dari form
    $confirm_password = $_POST['confirm_password']; // Ambil konfirmasi password

    // Validasi sederhana apakah password dan konfirmasi password sama
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Password and Confirm Password do not match!";
        header('Location: register.php');
        exit();
    }

    // Enkripsi password sebelum menyimpannya di database
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Cek apakah email sudah ada di database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Email is already registered!";
        header('Location: register.php');
        exit();
    }

    // Simpan user baru ke database
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $hashed_password
    ]);

    $_SESSION['success'] = "Registration successful! Please login.";
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Register</title>
</head>
<body>

<header class="header" style="border: 5px black; border-bottom: 0.1px solid black;">
    <h1>Unite</h1>
    <nav class="navigation_">
        <a href="index.php" style="color: black;">Home</a>
        <a href="events.php" style="color: black;">Events</a>
        <a href="about.php" style="color: black;">About Us</a>
    </nav>
</header>

<div class="container8">
<div class="register-form">
    <h2>Register</h2>
    <?php if(isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if(isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <form action="register.php" method="POST">
    <div class="form-group">
        <input type="text" name="name" placeholder="Enter Your Name" required><br><br>
        <input type="email" name="email" placeholder="Enter Your Email" required><br><br>
        <input type="password" name="password" placeholder="Enter Password" required><br><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br>
    </div>
        <button type="submit" class="button">Register</button>
    </form>
    <h4><a href="login.php" class="help-link">Udah punya akun? Log in aja!</h4></a>
</div>

</body>
</html>
