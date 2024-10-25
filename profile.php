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

// Ambil riwayat pendaftaran acara
$stmt = $pdo->prepare('SELECT e.event_name, er.registration_date FROM event_registrations er JOIN events e ON er.event_id = e.event_id WHERE er.user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px 0;
            padding: 20px;
            background-color: #e0e0e0; /* Warna abu-abu */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #d0d0d0;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>
<body>
<header class="header" style="border: 5px black; border-bottom: 0.1px solid black;">
    <h1>Unite</h1>
    <nav class="navigation_">
        <a href="index.php" style="color: black;">Home</a>
        <a href="events.php" style="color: black;">Events</a>
    </nav>

</header>

<div class="container">
    <h2>Profile Information</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
</div>

<div class="container">
    <h2>Event Registration History</h2>
    <table>
        <tr>
            <th>Event Name</th>
            <th>Registration Date</th>
        </tr>
        <?php if ($registrations): ?>
            <?php foreach ($registrations as $registration): ?>
                <tr>
                    <td><?php echo htmlspecialchars($registration['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($registration['registration_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No event registrations found.</td>
            </tr>
        <?php endif; ?>
        
    </table>
    <a href="index.php" class="button">Kembali ke Beranda</a>
</div>


</body>
</html>
