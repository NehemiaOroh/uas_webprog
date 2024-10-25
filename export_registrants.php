<?php
session_start();

// Koneksi ke database 'unite'
$host = 'localhost';
$dbname = 'unite';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all events
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="registrants.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, ['Event Name', 'User  Name', 'User  Email']);

// Fetch user registrations per event
foreach ($events as $event) {
    $stmt = $pdo->prepare("SELECT u.name, u.email FROM event_registrations er JOIN users u ON er.user_id = u.user_id WHERE er.event_id = ?");
    $stmt->execute([$event['event_id']]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($registrations as $user) {
        fputcsv($output, [ $event['event_name'], $user['name'], $user['email'] ]);
    }
}

// Close output stream
fclose($output);
exit();
?>