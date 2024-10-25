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








// Session timeout logic
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // Last activity was over 30 minutes ago (1800 seconds)
    session_unset();     // Unset session variables
    session_destroy();   // Destroy the session
    header('Location: index.php');
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time


// Handle event deletion
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    header('Location: dashboard.php');
    exit();
}

$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user registrations per event
$registrations = [];
foreach ($events as $event) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_registrations WHERE event_id = ?");
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
    <link rel="stylesheet" href="admin.css">
    <title>Admin Dashboard</title>
</head>
<body>

<section id="sidebar">
		<a href="#" class="brand">
			
			<span class="text" style="padding-left: 20px;">Unite</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="admindashboard">
					
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
            <li>
				<a href="events.php">

					<span class="text" style="padding-left: 20px;">See Events Page</span>
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

        <section class="event-management">
            
            <section id="content">
		<!-- NAVBAR -->
	
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3 style="font-family: Sans-Bold; font-size: 40px;">Available Events</h3>
					</div>
				<table>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Registrants</th>
                    </tr>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($event['current_participants']); ?></td>
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
