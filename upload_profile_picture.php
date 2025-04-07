<?php
session_start();

// Check if the user is logged in and has the necessary session variables
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Redirect to the correct login page based on role
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: login_now_admin.html?error=not_logged_in");
    } else {
        header("Location: login_now.html?error=not_logged_in");
    }
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "velvetglow_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES["profile_picture"]["name"]));
    $target_file = $target_dir . $file_name;

    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($imageFileType, $allowed_extensions)) {
        die("Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: my_account.php?success=profile_updated");
        exit();
    } else {
        die("Error uploading file. Check folder permissions.");
    }
}

$conn->close();

// Role-based redirection (only if needed)
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_account.php");
    exit();
} elseif ($_SESSION['role'] === 'customer') {
    header("Location: my_account.php");
    exit();
}

?>
