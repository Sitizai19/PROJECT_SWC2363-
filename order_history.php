<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login_now.html?error=not_authorized");
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

$conn->set_charset("utf8");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// Fetch orders based on email (case-insensitive)
$stmt = $conn->prepare("SELECT order_id, payment_method, order_date, total_price FROM orders WHERE LOWER(customer_email) = LOWER(?)");
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();



// Fetch order items for each order
$order_items = [];
foreach ($orders as $order) {
    $order_id = $order['order_id'];
    $stmt = $conn->prepare("SELECT product_name, quantity FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_items[$order_id] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - VelvetGlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffe4e1;
            color: #4a4a4a;
            padding: 20px;
        }
        .order-history {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            max-width: 800px;
            margin: auto;
        }
        .order-history h2 {
            color: #ff69b4;
            text-align: center;
        }
        .order-history table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-history th, .order-history td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .order-history th {
            background-color: #ff69b4;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff69b4;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #d63384;
        }

    </style>
</head>
<body>
    <div class="order-history">
        <h2>Order History</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Items</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                    <th>Total (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td>
                            <?php
                            if (isset($order_items[$order['order_id']])) {
                                foreach ($order_items[$order['order_id']] as $item) {
                                    echo htmlspecialchars($item['product_name']) . " (x" . htmlspecialchars($item['quantity']) . ")<br>";
                                }
                            } else {
                                echo "No items";
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="my_account.php" class="my-account-btn">My Account</a>
    </div>
</body>
</html>
