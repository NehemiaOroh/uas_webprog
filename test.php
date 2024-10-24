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

// Fetch available events
$stmt = $pdo->prepare("SELECT * FROM events WHERE status = 'available'");
$stmt->execute();
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

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style.css">

	<title>AdminHub</title>
</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			
			<span class="text" style="padding-left: 20px;">AdminHub</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="#">
					
					<span class="text" style="padding-left: 20px;">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="#">
			
					<span class="text" style="padding-left: 20px;">My Store</span>
				</a>
			</li>
			<li>
				<a href="#">

					<span class="text" style="padding-left: 20px;">Analytics</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="text" style="padding-left: 20px;">Message</span>
				</a>
			</li>
			<li>
				<a href="#">
					
					<span class="text" style="padding-left: 20px;">Team</span>
				</a>	
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="#">
					<i class='bx bxs-cog' ></i>
					<span class="text" style="padding-left: 20px;">Settings</span>
				</a>
			</li>
			<li>
				<a href="#" class="logout">
					<i class='bx bxs-log-out-circle' ></i>
					<span class="text" style="padding-left: 20px;">Logout</span>
				</a>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
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

			
	
	<!-- CONTENT -->
	

	<script src="script.js"></script>
</body>
</html>