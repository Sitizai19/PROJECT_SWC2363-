<?php
// Database connection without login
$host = 'localhost';
$user = 'root'; // Default MySQL username in XAMPP
$password = ''; // Empty password (no password by default)
$database = 'velvetglow_db';

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
