<?php
// Database connection
$host = 'localhost';
$user = 'root'; // Default MySQL username in XAMPP
$password = ''; // Empty password by default
$database = 'velvetglow_db';

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // To store success/error message

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['E-Mail'];
    $subject = $_POST['subject'];

    // Validate form data
    if (empty($firstname) || empty($lastname) || empty($email) || empty($subject)) {
        $message = "❌ Please fill in all fields.";
    } else {
        // Insert data into database
        $sql = "INSERT INTO contacts (firstname, lastname, email, subject) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $firstname, $lastname, $email, $subject);

        if ($stmt->execute()) {
            $message = "✅ Your message has been successfully submitted!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VelvetGlow - Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff0f5;
            text-align: center;
            line-height: 1.6;
        }
        h2 {
            font-family: 'Playfair Display', serif;
            color: #d63384;
            margin-top: 30px;
            font-size: 2.5rem;
        }
        nav {
            display: flex;
            justify-content: center;
            background-color: #ffb6c1;
            padding: 10px 0;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: background 0.3s;
        }
        nav a:hover {
            background-color: #ff69b4;
            border-radius: 5px;
        }
        .container {
            border-radius: 5px;
            padding: 10px;
            background-color: #fff0f5;
        }
        .column {
            float: left;
            width: 50%;
            margin-top: 6px;
            padding: 20px;
        }

/* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        input[type=text], textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            margin-top: 6px;
            margin-bottom: 16px;
            resize: vertical;
        }
        input[type=submit] {
            background-color: #ff69b4;
            color: white;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background-color: #ff1493;
        }
        .message {
            margin-top: 20px;
            font-size: 1.2rem;
        }
        @media screen and (max-width: 600px) {
            .column, input[type=submit] {
                width: 80%;
                margin-top: 0;
            }
        }
        footer {
            background-color: #ffb6c1;
            color: white;
            padding: 20px 0;
            text-align: center;
            width: 100%;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="cosmetics.php">Cosmetics</a>
        <a href="skincare.php">Skincare</a>
        <a href="tools.php">Tools & Accessories</a>
    </nav>
    <div class="container">
        <h2>Contact Us</h2>
        <p>Have questions or need assistance? We're here to help!</p>
    <div class="row">
    <div class="column">
      <img src="img/contact.png" style="width:50%">
    </div>
    <div class="column">
        <form action="" method="POST">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="firstname" placeholder="Your name.." required>

            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lastname" placeholder="Your last name.." required>

            <label for="email">E-Mail Address</label>
            <input type="text" id="email" name="E-Mail" placeholder="Your E-Mail address.." required>

            <label for="subject">Subject</label>
            <textarea id="subject" name="subject" placeholder="Write something.." style="height:170px" required></textarea>

            <input type="submit" value="Submit">
        </form>

        <!-- Display message here -->
        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</div>

    <footer>
        <p>&copy; 2025 VelvetGlow. All rights reserved.</p>
    </footer>
</body>
</html>
