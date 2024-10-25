<?php 
session_start();
require 'db_connect.php'; // Hubungkan dengan file koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; 
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $confirm_password = $_POST['confirm_password']; 

    // Validasi password (minimal 8 karakter, kombinasi huruf besar, kecil, dan angka)
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $_SESSION['error'] = "Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, and one number.";
        header('Location: register.php');
        exit();
    }

    // Validasi apakah password dan konfirmasi password sama
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
    <link rel="stylesheet" href="register.css">
    <title>Register - unite</title>
</head>
<body>

<header class="header" style="border: 5px black; border-bottom: 0.1px solid black;">
    <h1>Unite</h1>
    <nav class="navigation_">
        <a href="index.php" style="color: black;">Home</a>
        <a href="events.php" style="color: black;">Events</a>

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
    <form action="register.php" method="POST" id="registerForm">
    <div class="form-group">
        <input style="font-family: SemiBold;" type="text" name="name" placeholder="Enter Your Name" required><br><br>
        <input style="font-family: SemiBold;" type="email" name="email" placeholder="Enter Your Email" required><br><br>
        
        <div class="toggle-password">
            <input style="font-family: SemiBold;" type="password" name="password" id="password" placeholder="Enter Password" required><br>
            <i id="togglePassword" class="fa fa-eye"></i>
        </div>
        <h6><label for="password" style="font-family: SemiBold;">Min. 8 karakter berupa kombinasi angka, huruf besar, dan huruf kecil.</label></h6>

        <div class="toggle-password">
            <input style="font-family: SemiBold;" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required><br>
            <i id="toggleConfirmPassword" class="fa fa-eye"></i>
        </div>
    </div>
        <button type="submit" class="button">Register</button>
    </form>
    <h4><a href="login.php" class="help-link">Udah punya akun? Log in aja!</h4></a>
</div>
</div>

<script>
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        const confirm_password = document.getElementById('confirm_password').value;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/;

        if (!passwordRegex.test(password)) {
            alert('Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, and one number.');
            event.preventDefault();
        }
        if (password !== confirm_password) {
            alert('Password and Confirm Password do not match!');
            event.preventDefault();
        }
    });

    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#password');
    togglePassword.addEventListener('click', function (e) {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
    const confirmPasswordField = document.querySelector('#confirm_password');
    toggleConfirmPassword.addEventListener('click', function (e) {
        const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
</script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

</body>
</html>
