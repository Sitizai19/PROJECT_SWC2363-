<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "velvetglow_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update Order Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE customer_orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        $message = "<p class='success'>Order #$order_id status updated to '$new_status'.</p>";
    } else {
        $message = "<p class='error'>Failed to update order status.</p>";   
    }
}

$sql = "
    SELECT co.order_id, o.customer_name, o.customer_email, co.product_details, co.total_price, co.status, o.order_date
    FROM customer_orders co
    INNER JOIN orders o ON co.order_id = o.order_id
    ORDER BY o.order_date DESC
";

$result = $conn->query($sql);

// Check if query fails
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; 
            background-color: #ffe4e1; 
            margin: 0; 
        }
        .container { 
            width: 90%; 
            margin: auto; 
            padding: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white; 
            margin-bottom: 20px; 
        }
        th, td { 
            padding: 12px; 
            text-align: center; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background-color: #ffb6c1; 
            color: white; 
        }
        .btn { padding: 8px 12px; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px; 
            text-decoration: none; 
            display: inline-block; 
            margin: 5px; 
        }
        .btn-update { 
            background-color: #4CAF50; 
            color: white; 
        }
        .btn:hover { 
            opacity: 0.8; 
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
            padding: 10px 0; 
        }
        nav a { 
            color: white; 
            text-decoration: none;
            padding: 14px 20px; 
            display: block; 
            font-weight: bold; 
        }
        nav a:hover { 
            background-color: #ff69b4; 
        }
    </style>
</head>
<body>

<header>
    <h1>Customer Orders</h1>
</header>

<nav>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="manage_stock.php">Manage Stock</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <?php if (!empty($message)) echo $message; ?>

    <h2>All Customer Orders</h2>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Product Details</th>
            <th>Total Price (RM)</th>
            <th>Status</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['order_id']) ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['customer_email']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['product_details'])) ?></td>
                <td>RM<?= number_format($row['total_price'], 2) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <select name="status">
                            <option value="Pending" <?= ($row['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="Processing" <?= ($row['status'] == 'Processing') ? 'selected' : '' ?>>Processing</option>
                            <option value="Shipped" <?= ($row['status'] == 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                            <option value="Completed" <?= ($row['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-update">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No orders found.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
