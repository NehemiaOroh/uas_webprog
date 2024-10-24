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

    header('Location: dashboard.php');
    exit();
}

// Handle event deletion
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    header('Location: dashboard.php');
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Admin Dashboard</title>
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <h2>Admin Dashboard</h2>
        <nav>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="event_management.php">Event Management</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="">User Management</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>Welcome, Admin!</h1>
        </header>

        <section class="event-management">
            <h2>Manage Events</h2>
            <h3>Add New Event</h3>
            <form method="POST" enctype="multipart/form-data">
                <label for="event_name">Event Name:</label>
                <input type="text" name="event_name" required>
                
                <label for="event_description">Event Description:</label>
                <textarea name="event_description" required></textarea>
                
                <label for="event_date">Event Date:</label>
                <input type="date" name="event_date" required>
                
                <label for="event_time">Event Time:</label>
                <input type="time" name="event_time" required>
                
                <label for="location">Location:</label>
                <input type="text" name="location" required>
                
                <label for="max_participants">Max Participants:</label>
                <input type="number" name="max_participants" required>
                
                <label for="status">Status:</label>
                <input type="text" name="status" required>
                
                <label for="banner_image">Banner Image:</label>
                <input type="file" name="banner_image" accept="image/*" required>

                <button type="submit" name="add_event">Add Event</button>
            </form>

            <section id="content">
		<!-- NAVBAR -->
		<nav>		
			<img src="img/people.png">
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
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
                            <a href="edit_event.php?id=<?php echo $event['event_id']; ?>">Edit</a>
                            <a href="?delete_event=<?php echo $event['event_id']; ?>" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        </td>
                  	  </tr>
                <?php endforeach; ?>
            </table>
					
				</div>
			</div>
		</main>
	</section>
        </section>

        <section class="user-management">
            <h2>User Management</h2>
            <h3>Registered Users</h3>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </main>
</div>

</body>
</html>
