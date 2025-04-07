<?php
include 'db_connect.php';

if (!isset($_GET['order_id'])) {
    die("Invalid request.");
}

$order_id = $_GET['order_id'];
$query = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();


if (!$order) {
    die("Order not found.");
}

// Fetch order items
$item_query = "SELECT product_name, quantity, price, subtotal 
               FROM order_items 
               WHERE order_id = ?";
$stmt = $conn->prepare($item_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$item_result = $stmt->get_result();
$cart_items = $item_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - VelvetGlow</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #ffe4e1; }
        .receipt-container { max-width: 600px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        h1 { color: #d63384; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; }
        th { background-color: #ffb6c1; color: white; }
        .total { font-weight: bold; font-size: 1.5rem; margin-top: 20px; }
        .btn { background-color: #ff69b4; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <h1>Order Receipt</h1>
        <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
        <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
        <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
        <p><strong>Address:</strong> <?php echo $order['shipping_address']; ?></p>
        <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (RM)</th>
                    <th>Quantity</th>
                    <th>Subtotal (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($cart_items as $item) {
                    echo "<tr>
                            <td>{$item['product_name']}</td>
                            <td>" . number_format($item['price'], 2) . "</td>
                            <td>{$item['quantity']}</td>
                            <td>" . number_format($item['subtotal'], 2) . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <p class="total">Total: RM <?php echo number_format($order['total_price'], 2); ?></p>
        <a href="order_history.php" class="btn">View Order History</a>
        <a href="index.html" class="btn">Home</a>
    </div>
</body>
</html>
