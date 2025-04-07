<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION["role"])) {
    $role = $_SESSION["role"];

    // Destroy session
    session_unset();
    session_destroy();

    // Redirect based on role
    if ($role === "admin") {
        header("Location: loginnow_admin.html"); // Redirect admins
        exit();
    } else {
        header("Location: login_now.html"); // Redirect customers
        exit();
    }
} else {
    header("Location: login_now.html"); // Default redirect
    exit();
}
?>
