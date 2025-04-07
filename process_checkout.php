<?php
include 'db_connect.php';

$customer_name = $_POST['name'];
$customer_email = $_POST['email'];
$shipping_address = $_POST['address'];
$total_price = $_POST['total'];
$payment_method = $_POST['payment'];
$cart_items = json_decode($_POST['cart_items'], true); // Decode cart items from JSON

if (empty($cart_items)) {
    echo "Error: No items in the cart.";
    exit();
}

// Insert order into the database
$query = "INSERT INTO orders (customer_name, customer_email, shipping_address, total_price, payment_method, order_status) 
          VALUES (?, ?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssss", $customer_name, $customer_email, $shipping_address, $total_price, $payment_method);
$stmt->execute();

// Get the last inserted order ID
$order_id = $stmt->insert_id;

// 🔹 Insert into `customer_orders` to store the summary of the order
$customer_orders_query = "INSERT INTO customer_orders (order_id, product_details, total_price, status) 
                          VALUES (?, ?, ?, 'Pending')";

// 🔹 Convert cart items into a string
$product_details = "";
foreach ($cart_items as $item) {
    $product_details .= $item['name'] . " x" . $item['quantity'] . ", ";
}
$product_details = rtrim($product_details, ", "); // Remove last comma

$customer_orders_stmt = $conn->prepare($customer_orders_query);
$customer_orders_stmt->bind_param("isd", $order_id, $product_details, $total_price);
$customer_orders_stmt->execute();


// Insert order items and reduce stock
foreach ($cart_items as $item) {
    $product_name = $item['name'];
    $product_price = $item['price'];
    $quantity = $item['quantity'];

    // Insert into order_items table
    $item_query = "INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)";
$item_stmt = $conn->prepare($item_query);
$item_stmt->bind_param("isdi", $order_id, $product_name, $product_price, $quantity);
$item_stmt->execute();


    // Reduce stock based on product name
    $update_stock_query = "
    UPDATE cosmetics 
    SET stock_quantity = stock_quantity - ? 
    WHERE name = ? AND stock_quantity >= ?";
$stock_stmt = $conn->prepare($update_stock_query);
$stock_stmt->bind_param("isi", $quantity, $product_name, $quantity);
$stock_stmt->execute();

// Repeat for tools and skincare tables
$update_stock_query = "
    UPDATE tools 
    SET stock_quantity = stock_quantity - ? 
    WHERE name = ? AND stock_quantity >= ?";
$stock_stmt = $conn->prepare($update_stock_query);
$stock_stmt->bind_param("isi", $quantity, $product_name, $quantity);
$stock_stmt->execute();

$update_stock_query = "
    UPDATE skincare 
    SET stock_quantity = stock_quantity - ? 
    WHERE name = ? AND stock_quantity >= ?";
$stock_stmt = $conn->prepare($update_stock_query);
$stock_stmt->bind_param("isi", $quantity, $product_name, $quantity);
$stock_stmt->execute();
}

// Clear cart and redirect
echo "<script>
    localStorage.removeItem('cart');
    window.location.href = 'receipt.php?order_id={$order_id}';
</script>";

exit();
?>
