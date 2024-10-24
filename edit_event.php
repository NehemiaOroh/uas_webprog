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
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get the event ID from the URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch the event details
    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die('Event not found.');
    }
} else {
    die('Event ID is missing.');
}

// Handle form submission to update the event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_event'])) {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    // Handle optional image upload
    if (!empty($_FILES['banner_image']['name'])) {
        $banner_image = $_FILES['banner_image']['name'];
        move_uploaded_file($_FILES['banner_image']['tmp_name'], 'uploads/' . $banner_image);
    } else {
        // Keep the old image if no new one is uploaded
        $banner_image = $event['banner_image'];
    }

    // Update the event in the database
    $stmt = $pdo->prepare("UPDATE events SET event_name = ?, event_description = ?, event_date = ?, event_time = ?, location = ?, max_participants = ?, status = ?, banner_image = ? WHERE event_id = ?");
    $stmt->execute([$event_name, $event_description, $event_date, $event_time, $location, $max_participants, $status, $banner_image, $event_id]);

    // Redirect back to the admin dashboard or event management page
    header('Location: admindashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
</head>
<body>
    <h2>Edit Event</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
        <br>
        <label for="event_description">Event Description:</label>
        <textarea name="event_description" required><?php echo htmlspecialchars($event['event_description']); ?></textarea>
        <br>
        <label for="event_date">Event Date:</label>
        <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
        <br>
        <label for="event_time">Event Time:</label>
        <input type="time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
        <br>
        <label for="location">Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
        <br>
        <label for="max_participants">Max Participants:</label>
        <input type="number" name="max_participants" value="<?php echo htmlspecialchars($event['max_participants']); ?>" required>
        <br>
        <label for="status">Status:</label>
        <input type="text" name="status" value="<?php echo htmlspecialchars($event['status']); ?>" required>
        <br>
        <label for="banner_image">Banner Image:</label>
        <input type="file" name="banner_image" accept="image/*">
        <br>
        <img src="uploads/<?php echo htmlspecialchars($event['banner_image']); ?>" alt="Current Banner" style="max-width: 200px;">
        <br>
        <button type="submit" name="update_event">Update Event</button>
    </form>
</body>
</html>
