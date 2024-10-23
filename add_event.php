<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];

    $insert_query = "INSERT INTO events (event_name, event_date, location) VALUES ('$event_name', '$event_date', '$location')";

    if ($conn->query($insert_query) === TRUE) {
        echo "New event added successfully!";
    } else {
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }
}

$conn->close();

header("Location: dashboard.php");
exit();
?>
