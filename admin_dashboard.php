<?php
session_start(); // ✅ Start the session at the top of every page that needs session data

// Ensure the admin is logged in and session is active
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginnow_admin.html?error=Unauthorized access.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VelvetGlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Prata&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffe4e1;
            color: #4a4a4a;
        }
        header {
            background-color: #ffb6c1;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            display: flex;
            justify-content: center;
            background-color: #ffb6c1;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: block;
        }
        nav a:hover {
            background-color: #ff69b4;
        }
        .container {
            text-align: center;
            padding: 20px;
        }
        .dashboard-card {
            background-color: white;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff69b4;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #ff1493;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin!</p>
    </header>
    <nav>
        <a href="index.html">Home</a>
        <a href="admin_account.php">Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
    <div class="container">
        <div class="dashboard-card">
            <h2>Product Inventory Management</h2>
            <p>Update and track product inventory.</p>
            <a href="manage_stock.php" class="logout-btn">Manage Stock</a>
        </div>
        <div class="dashboard-card">
            <h2>Customer Order Management</h2>
            <p>View, update, and track customer orders efficiently.</p>
            <a href="customer_orders.php" class="logout-btn">Manage Customer Orders</a>
        </div>
    </div>
</body>
</html>
