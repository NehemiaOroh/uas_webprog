<?php
// Database connection
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

// Check if user and event IDs are provided
if (isset($_GET['user_id'], $_GET['event_id'])) {
    $userId = (int)$_GET['user_id'];
    $eventId = (int)$_GET['event_id'];

    // Check if the registration exists
    $checkRegistration = $pdo->prepare("SELECT * FROM event_registrations WHERE user_id = :user_id AND event_id = :event_id");
    $checkRegistration->execute(['user_id' => $userId, 'event_id' => $eventId]);

    if ($checkRegistration->rowCount() > 0) {
        // Delete registration
        $cancelRegistration = $pdo->prepare("DELETE FROM event_registrations WHERE user_id = :user_id AND event_id = :event_id");
        $cancelRegistration->execute(['user_id' => $userId, 'event_id' => $eventId]);

        // Update current participants count in events table
        $updateEvent = $pdo->prepare("UPDATE events SET current_participants = current_participants - 1 WHERE event_id = :event_id");
        $updateEvent->execute(['event_id' => $eventId]);

        echo "Registration canceled successfully.";
    } else {
        echo "You are not registered for this event.";
    }
} else {
    echo "User or Event ID missing.";
}
?>
