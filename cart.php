<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - VelvetGlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color:#ffe4e1;
            text-align: center;
            line-height: 1.6;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            color: #d63384;
            margin: 30px 0;
        }
        .cart-container, .checkout-form {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #ffb6c1;
            color: white;
        }
        .cart-total {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 20px;
        }
        button {
            background-color: #ff69b4;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 10px;
        }
        button:hover {
            background-color: #d63384;
        }
        .checkout-form input, .checkout-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .checkout-form button {
            width: 100%;
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

    <h1>Your Shopping Cart</h1>
    <div class="cart-container" id="cart-container"></div>
    <div class="cart-total" id="cart-total">Total: RM 0.00</div>
    <button onclick="clearCart()">Clear Cart</button>
    <button onclick="continueShopping()">Continue Shopping</button>

    <!-- Checkout Form -->
    <div class="checkout-form">
        <h2>Checkout</h2>
        <form id="checkoutForm" action="process_checkout.php" method="post" onsubmit="return validateCart()">
            <input type="hidden" name="total" id="hiddenTotal">
            <input type="hidden" name="cart_items" id="cartItems">
            
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="address">Shipping Address</label>
            <input type="text" id="address" name="address" required>

            <label for="payment">Payment Method</label>
            <select id="payment" name="payment" required>
                <option value="tng">Touch And Go E-Wallet</option>
                <option value="debit_card">Debit Card</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="COD">Cash On Delivery (COD)</option>
            </select>

            <button type="submit">Complete Purchase</button>
        </form>
    </div>

    <script>
        function displayCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cart-container');
            const cartTotal = document.getElementById('cart-total');
            const hiddenTotal = document.getElementById('hiddenTotal');
            const cartItems = document.getElementById('cartItems');

            if (cart.length === 0) {
                cartContainer.innerHTML = "<p>Your cart is empty.</p>";
                cartTotal.textContent = "Total: RM 0.00";
                hiddenTotal.value = "0";
                cartItems.value = "[]";
                return;
            }

            let tableHTML = `
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price (RM)</th>
                            <th>Quantity</th>
                            <th>Subtotal (RM)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            let total = 0;
            let cartData = [];

            cart.forEach((item, index) => {
                if (!item.id || !item.name || isNaN(item.price) || isNaN(item.quantity) || item.quantity <= 0) {
                    console.error("Invalid cart item detected:", item);
                    return;
                }

                const subtotal = item.price * item.quantity;
                total += subtotal;

                tableHTML += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.price.toFixed(2)}</td>
                        <td>${item.quantity}</td>
                        <td>${subtotal.toFixed(2)}</td>
                        <td><button onclick="removeItem(${index})">Remove</button></td>
                    </tr>
                `;

                cartData.push({ id: item.id, name: item.name, price: item.price, quantity: item.quantity });
            });

            tableHTML += '</tbody></table>';

            cartContainer.innerHTML = tableHTML;
            cartTotal.textContent = "Total: RM " + total.toFixed(2);
            hiddenTotal.value = total.toFixed(2);
            cartItems.value = JSON.stringify(cartData);
        }

        function removeItem(index) {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            displayCart();
        }

        function continueShopping() {
            location.href = 'cosmetics.php';
        }

        function clearCart() {
            if (confirm("Are you sure you want to clear your cart?")) {
                localStorage.removeItem('cart');
                displayCart();
                alert("Your cart has been cleared.");
                location.reload();
            }
        }

        function validateCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart.length === 0) {
                alert("Your cart is empty. Please add items before proceeding to checkout.");
                return false;
            }
            return true;
        }

        window.onload = function() {
            displayCart();
        };
    </script>

    <footer>
        <p>&copy; 2025 VelvetGlow. All rights reserved.</p>
    </footer>
</body>
</html>
