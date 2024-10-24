<?php
// Include your database connection
require 'db_connect.php'; // Make sure this file connects to your database

// Define the admin details
$name = "Nehemia"; // Change this to your admin's name
$email = "nehemiaadmin.com"; // Change this to your admin's email
$password = "Dragon59"; // Your admin password

// Hash the password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert the admin user into the database
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
$stmt->execute([
    'name' => $name,
    'email' => $email,
    'password' => $hashed_password,
    'role' => 'admin' // Set role to admin
]);

echo "Admin user created successfully!";
?>
