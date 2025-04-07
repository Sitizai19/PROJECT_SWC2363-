<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "velvetglow_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$email = $_POST['email'];
$password = $_POST['password'];

// Prevent SQL injection
$sql = "SELECT id, name, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // Set session variables
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['email'] = $email;


    // Redirect to my_account.php
    header("Location: my_account.php");
    exit();
} else {
    // Redirect back to login with error
    header("Location: login_now.html?error=not_authorized");
    exit();
}

$stmt->close();
$conn->close();
?>
