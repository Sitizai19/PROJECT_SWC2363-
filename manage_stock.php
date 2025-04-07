<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "velvetglow_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to add product (Updated)
function addProduct($conn, $table, $name, $price, $stock_quantity, $description) {
    if (empty($name) || empty($price) || empty($stock_quantity) || empty($_FILES['image']['name']) || empty($description)) {
        return "<p class='error'>Error: All fields are required.</p>";
    }

    // Handle Image Upload
    $target_dir = "uploads/"; // Folder to store images
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        return "<p class='error'>Error: File is not an image.</p>";
    }

    // Move uploaded file to 'uploads/' directory
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        return "<p class='error'>Error: Failed to upload image.</p>";
    }

    // Insert product into database with image path
    $stmt = $conn->prepare("INSERT INTO $table (name, price, stock_quantity, image, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdiss", $name, $price, $stock_quantity, $target_file, $description);
    $stmt->execute();

    return "<p class='success'>Product added successfully!</p>";
}


// Function to update product stock by ID
function updateProductStock($conn, $table, $id, $stock_quantity) {
    if (empty($id) || empty($stock_quantity)) {
        return "<p class='error'>Error: Product ID and Stock Quantity are required.</p>";
    }
    $stmt = $conn->prepare("UPDATE $table SET stock_quantity = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $stock_quantity, $id);
    $stmt->execute();
    return "<p class='success'>Stock updated successfully in $table!</p>";
}

// Function to remove product by ID
function removeProduct($conn, $table, $id) {
    if (empty($id)) {
        return "<p class='error'>Error: Product ID is required.</p>";
    }
    $stmt = $conn->prepare("DELETE FROM $table WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return "<p class='success'>Product ID '$id' removed.</p>";
}

// Function to view products (Updated)
function viewProducts($conn, $table) {
    $result = $conn->query("SELECT * FROM $table");
    if ($result->num_rows == 0) {
        return "<p class='error'>No products found in $table.</p>";
    }
    $output = "<h3>Products in " . ucfirst($table) . ":</h3>";
    $output .= "<table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Price (RM)</th>
                        <th>Stock</th>
                        <th>Description</th>
                    </tr>";
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>
                        <td>{$row['product_id']}</td>
                        <td>{$row['name']}</td>
                        <td><img src='{$row['image']}' width='50'></td>
                        <td>RM{$row['price']}</td>
                        <td>{$row['stock_quantity']}</td>
                        <td>{$row['description']}</td>
                    </tr>";
    }
    $output .= "</table>";
    return $output;
}


// Handle Form Actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $type = $_POST['type'] ?? 'cosmetics'; // Default category
    $validTables = ['cosmetics', 'tools', 'skincare'];

    if (!in_array($type, $validTables)) {
        die("Invalid product type.");
    }

    $id = $_POST['product_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock_quantity = $_POST['stock_quantity'] ?? 0;

    if ($action == "Add Product") {
        $message = addProduct($conn, $type, $name, $price, $stock_quantity, $_POST['description']);
    } elseif ($action == "Remove Product") {
        $message = removeProduct($conn, $type, $id);
    } elseif ($action == "View Products") {
        $message = viewProducts($conn, $type);
    } elseif ($action == "Update Stock") {
        $message = updateProductStock($conn, $type, $id, $stock_quantity);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #ffe4e1; margin: 0; }
        .container { width: 90%; margin: auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-bottom: 20px; }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        th { background-color: #ffb6c1; color: white; }
        .btn { padding: 8px 12px; border: none; cursor: pointer; border-radius: 5px; text-decoration: none; display: inline-block; margin: 5px; }
        .btn-edit { background-color: #ff69b4; color: white; }
        .btn-delete { background-color: #ff4d4d; color: white; }
        .btn-add { background-color: #4CAF50; color: white; padding: 10px 15px; font-weight: bold; }
        .btn:hover { opacity: 0.8; }

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
        .action-links { display: flex; justify-content: center; gap: 10px; }
    </style>
</head>
<body>

<header>
    <h1>Stock Management System</h1>
</header>

<nav>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="customer_orders.php">Orders</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <?php if (!empty($message)) echo $message; ?>

    <!-- Add Product -->
    <form method="post" enctype="multipart/form-data">
    <h2>Add a Product</h2>
    <label>Category:</label>
    <select name="type">
        <option value="cosmetics">Cosmetics</option>
        <option value="tools">Tools</option>
        <option value="skincare">Skincare</option>
    </select>
    <label>Name:</label>
    <input type="text" name="name" required>
    <label>Price (RM):</label>
    <input type="number" step="0.01" name="price" required>
    <label>Stock Quantity:</label>
    <input type="number" name="stock_quantity" required>
    
    <label>Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <label>Description:</label>
    <textarea name="description" required></textarea>

    <input type="submit" name="action" value="Add Product" class="btn btn-add">
</form>



    <!-- Remove Product by ID -->
    <form method="post">
        <h2>Remove a Product</h2>
        <label>Category:</label>
        <select name="type">
            <option value="cosmetics">Cosmetics</option>
            <option value="tools">Tools</option>
            <option value="skincare">Skincare</option>
        </select>
        <label>Product ID:</label>
        <input type="number" name="product_id" required>
        <input type="submit" name="action" value="Remove Product" class="btn btn-delete">
    </form>

    <!-- Update Product Stock by ID -->
    <form method="post">
        <h2>Update Product Stock</h2>
        <label>Category:</label>
        <select name="type">
            <option value="cosmetics">Cosmetics</option>
            <option value="tools">Tools</option>
            <option value="skincare">Skincare</option>
        </select>
        <label>Product ID:</label>
        <input type="number" name="product_id" required>
        <label>New Stock Quantity:</label>
        <input type="number" name="stock_quantity" required>
        <input type="submit" name="action" value="Update Stock" class="btn btn-add">
    </form>


    <!-- View Products -->
    <form method="post">
        <h2>View Products</h2>
        <label>Category:</label>
        <select name="type">
            <option value="cosmetics">Cosmetics</option>
            <option value="tools">Tools</option>
            <option value="skincare">Skincare</option>
        </select>
        <input type="submit" name="action" value="View Products" class="btn btn-add">
    </form>

</div>

</body>
</html>
