<?php
// Start session if it hasn't been started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$host = 'localhost';
$dbname = 'unite';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if 'delete_user' is set in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Redirect back to admin dashboard
    header('Location: user_management.php');
    exit();
} else {
    // If no user_id is provided, redirect to the dashboard
    header('Location: admindashboard.php');
    exit();
}
