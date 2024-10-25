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
    header('Location: admindashboard.php');
    exit();
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    header('Location: admindashboard.php');
    exit();
}

// add admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);  // Hash password for security

    // Insert new admin into the database
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute([$admin_name, $admin_email, $admin_password]);

    header('Location: admindashboard.php');
    exit();
}


// Fetch all events
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

$user_events = [];
foreach ($users as $user) {
    $stmt = $pdo->prepare("
        SELECT e.event_name, e.event_date 
        FROM event_registrations er 
        JOIN events e ON er.event_id = e.event_id 
        WHERE er.user_id = ?
    ");
    $stmt->execute([$user['user_id']]);
    $user_events[$user['user_id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Admin Creation Form -->
    <section class="user-management" style="margin-top:-20vh; margin-bottom:-40vh">
    <div class="user-management-container">
        <h3 style="font-family: sans-Bold">Add New Admin</h3>
        <form method="POST" action="" class="newadmin">
            <table>
                <tr>
                    <td><label for="admin_name">Admin Name:</label></td>
                    <td><input type="text" name="admin_name" required></td>
                </tr>
                <tr>
                    <td><label for="admin_email">Admin Email:</label></td>
                    <td><input type="email" name="admin_email" required></td>
                </tr>
                <tr>
                    <td><label for="admin_password">Admin Password:</label></td>
                    <td><input type="password" name="admin_password" required></td>
                </tr>
            </table>
            <button type="submit" name="add_admin" class="button">Add Admin</button>
        </form>
    </div>
    </section>


    <section class="user-management" style="margin-top:vh;">
    <div class="user-management-container">
        <h3 style="font-family: sans-Bold">Registered Users</h3>
        <table>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered Events</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <ul>
                            <?php foreach ($user_events[$user['user_id']] as $event): ?>
                                <li><?php echo htmlspecialchars($event['event_name']) . ' (' . htmlspecialchars($event['event_date']) . ')'; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

</main>
</div>

</body>
</html>

    </main>
</div>

</body>
</html>
