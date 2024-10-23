<?php 
session_start();
require 'db_connect.php'; // Hubungkan dengan file koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['username']; // Ambil email dari input form
    $password = $_POST['password']; // Ambil password dari input form

    // Query untuk mencari user berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika user ditemukan dan password yang dimasukkan cocok
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];  // Simpan user ID ke session
        $_SESSION['username'] = $user['name'];    // Simpan nama pengguna ke session
        header('Location: index.php');            // Redirect ke halaman utama
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password!"; // Tampilkan pesan error
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Login</title>
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

<div class="login-form" style="text-align: center; padding: 50px;">
    <h1>Login</h1>
    <?php if(isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Enter Email" required><br><br>
        <input type="password" name="password" placeholder="Enter Password" required><br><br>
        <button type="submit" class="button">Login</button>
    </form>
</div>

</body>
</html>
