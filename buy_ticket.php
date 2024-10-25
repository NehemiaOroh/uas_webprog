<?php
session_start();
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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && isset($_POST['event_id'])) {
        $userId = $_SESSION['user_id'];
        $eventId = $_POST['event_id'];

        // Check if the user has already registered for this event
        $stmt = $pdo->prepare("SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$userId, $eventId]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'You have already registered for this event.']);
            exit();
        }

        // Insert the registration
        $stmt = $pdo->prepare("INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)");
        if ($stmt->execute([$userId, $eventId])) {
            // Update the current participants count
            $stmt = $pdo->prepare("UPDATE events SET current_participants = current_participants + 1 WHERE event_id = ?");
            $stmt->execute([$eventId]);

            echo json_encode(['status' => 'success', 'message' => 'Ticket purchased successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to purchase ticket.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User  not logged in or event ID not provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}




$eventId = $_POST['event_id'];
$userId = $_SESSION['user_id'];

// Check if the user is already registered for the event
$stmt = $pdo->prepare("SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$userId, $eventId]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'You are already registered for this event.']);
    exit();
}

// Insert registration into database
$stmt = $pdo->prepare("INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)");
$stmt->execute([$userId, $eventId]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Ticket purchased successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to purchase ticket. Please try again.']);
}

?>