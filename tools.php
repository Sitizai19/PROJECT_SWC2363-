<?php
include 'db_connect.php';

// Fetch products from the database
$sql = "SELECT product_id, name, price, image, description, stock_quantity FROM tools";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VelvetGlow - Tools And Accessories</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            padding-top: 60px; /* Adjust this based on navbar height */
            font-family: 'Poppins', sans-serif;
            background-color: #ffe4e1;
            text-align: center;
            line-height: 1.6;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            color: #d63384;
            margin-top: 30px;
            font-size: 2.5rem;
        }
        nav {
            position: fixed; /* Make the navbar stay at the top */
            top: 0; /* Stick it to the top */
            left: 0; /* Align to the left */
            width: 100%; /* Full width */
            background-color: #ffb6c1; /* Your existing background color */
            padding: 10px 0; 
            display: flex; /* Keep links aligned */
            justify-content: center; /* Center the links */
            z-index: 1000; /* Ensure it stays on top */
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
        .cart-icon {
            margin-left: auto;
            padding: 14px 20px;
            color: white;
            cursor: pointer;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .product-card {
            background-color:whitesmoke;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-10px);
        }
        .product-card img {
            width: 60%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .product-card h2 {
            font-family: 'Playfair Display', serif;
            color: #d63384;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .product-card p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 10px;
        }
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #c2185b;
            margin: 15px 0;
        }
        .add-to-cart {
            background-color: #ff69b4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .add-to-cart:hover {
            background-color: #d63384;
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
        <div class="cart-icon" onclick="location.href='cart.php'">🛒 Cart (<span id="cart-count">0</span>)</div>
    </nav>
    <h1>Tools And Accessories Collection</h1>
    <div class="grid-container">
        <?php
        if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="product-card">';
        echo '<img src="' . $row["image"] . '" alt="' . htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8') . '">';
        echo '<h2>' . htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8') . '</h2>';
        echo '<p>' . htmlspecialchars($row["description"], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p class="price">RM ' . number_format($row["price"], 2) . '</p>';
        echo '<p>Stock: ' . $row["stock_quantity"] . ' available</p>'; // 🆕 Display stock quantity
        echo '<button class="add-to-cart" onclick="addToCart(' . $row["product_id"] . ', \'' . addslashes($row["name"]) . '\', ' . $row["price"] . ')">Add to Cart</button>';
        echo '</div>';
    }
} else {
            echo '<p>No products available.</p>';
        }
        $conn->close();
        ?>
    </div>

    <script>
        // Add to Cart Function with Unique Product ID
function addToCart(id, name, price) {
    // Retrieve the current cart or initialize it
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Check if the product with the same id already exists
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        // If product exists, increase quantity
        existingItem.quantity += 1;
    } else {
        // If new product, add it to the cart
        cart.push({ id, name, price, quantity: 1 });
    }

    // Save updated cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount(); // Update the cart count display

    // ✅ Show success message
    alert(`${name} has been added to your cart!`);
}

// Update Cart Count Function
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    document.getElementById('cart-count').textContent = cartCount;
}

// Ensure the cart count updates on page load
window.onload = updateCartCount;

    </script>
    <footer>
        <p>&copy; 2025 VelvetGlow. All rights reserved.</p>
    </footer>
</body>
</html>
