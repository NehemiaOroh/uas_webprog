<?php
// Start the session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "unite";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users and events data
$users_query = "SELECT * FROM users";
$users_result = $conn->query($users_query);

$events_query = "SELECT * FROM events";
$events_result = $conn->query($events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Admin Dashboard</h1>

    <div class="row mt-5">
        <!-- Users Section -->
        <div class="col-md-6">
            <h2>Users</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['name']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['role']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Events Section -->
        <div class="col-md-6">
            <h2>Events</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $events_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['event_name']; ?></td>
                            <td><?= $row['event_date']; ?></td>
                            <td><?= $row['location']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Event Form -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h2>Add New Event</h2>
            <form action="add_event.php" method="POST">
                <div class="form-group">
                    <label for="event_name">Event Name</label>
                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                </div>
                <div class="form-group">
                    <label for="event_date">Event Date</label>
                    <input type="date" class="form-control" id="event_date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Event</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
