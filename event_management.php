<?php
// Periksa apakah session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

// Handle form submission for adding an event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];
    $banner_image = $_FILES['banner_image']['name'];

    // Move uploaded image to desired directory
    move_uploaded_file($_FILES['banner_image']['tmp_name'], 'uploads/' . $banner_image);

    // Insert event into the database
    $stmt = $pdo->prepare("INSERT INTO events (event_name, event_description, event_date, event_time, location, max_participants, status, banner_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$event_name, $event_description, $event_date, $event_time, $location, $max_participants, $status, $banner_image]);

    header('Location: admindashboard.php');
    exit();
}

// Handle event deletion
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    header('Location: admindashboard.php');
    exit();
}

// Fetch all events
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user registrations per event
$registrations = [];
foreach ($events as $event) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
    $stmt->execute([$event['event_id']]);
    $registrations[$event['event_id']] = $stmt->fetchColumn();
}

// Fetch all users for user management
$user_stmt = $pdo->query("SELECT * FROM users");
$users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);

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

// Logout handler
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="event_management.css">
    <title>Admin Dashboard</title>
</head>
<body>

<div class="container">
<section id="sidebar">
		<a href="#" class="brand">
			
			<span class="text" style="padding-left: 20px;">Unite</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="admindashboard.php">
					
					<span class="text" style="padding-left: 20px;">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="event_management.php">
			
					<span class="text" style="padding-left: 20px;">Event Management</span>
				</a>
			</li>
			<li>
				<a href="user_management.php">

					<span class="text" style="padding-left: 20px;">User Management</span>
				</a>
			</li>
			
		</ul>
		<ul class="side-menu">
			<li>
            <form method="POST" style="display: inline;">
                <button name="logout" class="button" style="padding-left:20px;">Logout</button>
            </form>
        </li>
		</ul>
	</section>

    <main class="main-content">

        <section class="event-management">
            <h2>Manage Events</h2>
            <h3>Add New Event</h3>
            <br>
            <form method="POST" enctype="multipart/form-data">
                <label for="event_name">Event Name:</label>
                <input type="text" name="event_name" required>
                <br>
                <label for="event_description">Event Description:</label>
                <textarea name="event_description" required></textarea>
                <br>
                <label for="event_date">Event Date:</label>
                <input type="date" name="event_date" required>
                <br>
                <label for="event_time">Event Time:</label>
                <input type="time" name="event_time" required>
                <br>
                <label for="location">Location:</label>
                <input type="text" name="location" required>
                <br>
                <label for="max_participants">Max Participants:</label>
                <input type="number" name="max_participants" required>
                <br>
                <label for="status">Status:</label>
                <input type="text" name="status" required>
                <br>
                <label for="banner_image">Banner Image:</label>
                <input type="file" name="banner_image" accept="image/*" required>
                <br>
                <button type="submit" name="add_event" href="admindashboard.php">Add Event</button>
            </form>

            <section id="content">
		
		<main>
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Available Events</h3>
					</div>
					<table>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Registrants</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($registrations[$event['event_id']]); ?></td>
                        <td>
                        <a href="edit_event.php?id=<?php echo $event['event_id']; ?>" class="button" style="margin-left:-50px;">Edit</a>
                        <a href="?delete_event=<?php echo $event['event_id']; ?>"class="button" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        </td>
                  	  </tr>
                <?php endforeach; ?>
            </table>
					
				</div>
			</div>
		</main>
	</section>
        </section>

    </main>
</div>

</body>
</html>
