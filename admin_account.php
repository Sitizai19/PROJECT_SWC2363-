<?php
session_start();
include 'db_connect.php'; // Ensure database connection is included

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: loginnow_admin.html?error=Unauthorized access");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

// Fetch admin details from the database
$stmt = $conn->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - VelvetGlow</title>
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
            background-image: url('img/header2.png');
            background-size: cover;
            color:#ffb6c1;
            padding: 15px 20px;
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
            font-weight: 500;
        }
        nav a:hover {
            background-color: #ff69b4;
        }
        main {
            padding: 20px;
            text-align: center;
        }
        .account-section {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            text-align: left;
            max-width: 800px;
        }
        .account-section h2 {
            color: #ff69b4;
            text-align: center;
        }
        .profile-picture {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ff69b4;
        }
        .upload-form {
            text-align: center;
            margin-top: 10px;
        }
        .upload-form input[type="file"] {
            margin-bottom: 10px;
        }
        .account-details p {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .account-actions {
            margin-top: 20px;
            text-align: center;
        }
        .account-actions a {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            font-size: 16px;
            background-color: #ff69b4;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .account-actions a:hover {
            background-color: #ff1493;
        }
        .dashboard-btn {
            display: block;
            margin: 20px auto;
            padding: 12px 24px;
            background-color: #ff4500;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            text-align: center;
            width: fit-content;
        }
        .dashboard-btn:hover {
            background-color: #ff2400;
        }
        footer {
            background-color: #ffb6c1;
            color: white;
            padding: 20px 0;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Profile</h1>
        <p>Welcome, <?php echo htmlspecialchars($name); ?>!</p>
    </header>

    <nav>
        <a href="index.html">Home</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="cosmetics.php">Cosmetics</a>
        <a href="skincare.php">Skincare</a>
        <a href="tools.php">Tools and Accessories</a>
    </nav>

    <main>
        <section class="account-section">
            <h2>Admin Details</h2>
            
            <div class="profile-picture">
                <img src="<?php echo !empty($profile_picture) ? htmlspecialchars($profile_picture) : 'img/default-avatar.png'; ?>" alt="Profile Picture">
            </div>

            <div class="upload-form">
                <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_picture" accept="image/*" required>
                    <button type="submit">Upload Profile Picture</button>
                </form>
            </div>

            <div class="account-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst($role); ?></p>
            </div>

            <div class="account-actions">
                <a href="logout.php">Logout</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 VelvetGlow. All rights reserved.</p>
    </footer>
</body>
</html>
