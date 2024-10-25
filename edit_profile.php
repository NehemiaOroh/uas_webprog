<?php
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database 'unite'
$host = 'localhost';
$dbname = 'unite';
$username = 'root';
$password = '';

try {
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}

// Ambil data user dari database berdasarkan session
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Proses update profil jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $profileImage = $_FILES['profile_image'] ?? null;
    $currentPassword = $_POST['current_password'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;

    // Check if required fields are set
    if ($username && $email && $phone) {
        // Cek jika password diubah
        if ($newPassword) {
            // Validasi password saat ini
            if (password_verify($currentPassword, $user['password'])) {
                // Hash password baru
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password di database
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
                $stmt->execute([$hashedNewPassword, $_SESSION['user_id']]);
            } else {
                $error = 'Password saat ini salah.';
            }
        }

        // Folder upload untuk gambar profil
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($profileImage['name']);

        // Cek apakah folder upload ada, jika tidak buat folder tersebut
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Proses upload file jika ada file gambar yang dipilih
        if ($profileImage && $profileImage['error'] == UPLOAD_ERR_OK) {
            if (move_uploaded_file($profileImage['tmp_name'], $uploadFile)) {
                // Jika berhasil upload, update data pengguna termasuk gambar profil
                $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, phone = ?, profile_image = ? WHERE user_id = ?');
                $stmt->execute([$username, $email, $phone, $uploadFile, $_SESSION['user_id']]);
            } else {
                $error = 'Gagal mengunggah gambar.';
            }
        } else {
            // Jika tidak ada file yang dipilih, hanya update data pengguna tanpa gambar
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, phone = ? WHERE user_id = ?');
            $stmt->execute([$username, $email, $phone, $_SESSION['user_id']]);
        }

        // Perbarui session username
        $_SESSION['username'] = $username;

        // Redirect ke halaman utama setelah update
        header('Location: index.php');
        exit();
    } else {
        $error = 'Silakan lengkapi semua field yang diperlukan.';
    }
}
$isLoggedIn = isset($_SESSION['user_id']);
$profileImage = 'default.png'; // Gambar default

if ($isLoggedIn) {
    // Ambil data user dari database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && !empty($user['profile_image'])) {
        $profileImage = $user['profile_image'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="login.css">
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
<div class="login-form">
    <h2>Edit Profile</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label style="font-family:Sans-Bold" for="username">Username</label style="font-family:Sans-Bold"><br>
        <input style="font-family: SemiBold;"type="text" name="username" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>

        <label style="font-family:Sans-Bold" for="email">Email</label style="font-family:Sans-Bold"><br>
        <input style="font-family: SemiBold;"type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

        <label style="font-family:Sans-Bold" for="phone">Phone</label style="font-family:Sans-Bold"><br>
        <input style="font-family: SemiBold;"type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br><br>

        <label style="font-family:Sans-Bold" for="current_password">Current Password</label style="font-family:Sans-Bold"><br>
        <input style="font-family: SemiBold;"type="password" name="current_password" required><br><br>

        <label style="font-family:Sans-Bold" for="new_password">New Password</label style="font-family:Sans-Bold"><br>
        <input style="font-family: SemiBold;"type="password" name="new_password"><br><br>

        <button type="submit" name="update" class="button">Update Profile</button>
    </form>
    <br>
    <a href="index.php" class="help-link">Kembali ke Beranda</a>
    </div>
</div>
</body>
</html>
