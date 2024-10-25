<?php
// Koneksi ke database 'unite'
$host = 'localhost';
$dbname = 'unite'; // nama database 'unite'
$username = 'root'; // username MySQL
$password = ''; // password MySQL (kosong di XAMPP)

try {
    // Koneksi ke database 'unite'
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null; // Cast to int for safety



if ($event_id) {
    // Logic to cancel registration
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Assuming you have a registrations table to manage user registrations
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE user_id = ? AND event_id = ?");
    if ($stmt->execute([$user_id, $event_id])) {
        echo json_encode(['status' => 'success', 'message' => 'Registration canceled successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to cancel registration.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Event ID is missing.']);
}
?>
