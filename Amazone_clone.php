<?php
// Amazon Clone - Single Page PHP Application

// Database simulation (in a real app, use MySQL or another DB)
$products = [
    1 => ['id' => 1, 'name' => 'Wireless Headphones', 'price' => 59.99, 'image' => 'headphones.jpg'],
    2 => ['id' => 2, 'name' => 'Smart Watch', 'price' => 199.99, 'image' => 'smartwatch.jpg'],
    3 => ['id' => 3, 'name' => 'Bluetooth Speaker', 'price' => 39.99, 'image' => 'speaker.jpg'],
    4 => ['id' => 4, 'name' => 'E-Reader', 'price' => 129.99, 'image' => 'ereader.jpg'],
];

// Initialize cart if not exists
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'add':
            $productId = (int)$_GET['id'];
            if (isset($products[$productId])) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity']++;
                } else {
                    $_SESSION['cart'][$productId] = [
                        'product' => $products[$productId],
                        'quantity' => 1
                    ];
                }
            }
            break;
        case 'remove':
            $productId = (int)$_GET['id'];
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
            break;
        case 'update':
            $productId = (int)$_GET['id'];
            $quantity = (int)$_GET['quantity'];
            if (isset($_SESSION['cart'][$productId]) && $quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
            break;
        case 'checkout':
            // In a real app, process payment and save order
            $_SESSION['cart'] = [];
            $checkoutSuccess = true;
            break;
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Calculate cart total
$cartTotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['product']['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon Clone</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 0; background-color: #f3f3f3; }
        .header { background-color: #232f3e; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; color: #ff9900; }
        .search-bar { flex-grow: 1; margin: 0 20px; }
        .search-bar input { width: 100%; padding: 8px; border-radius: 4px; border: none; }
        .cart { color: white; text-decoration: none; }
        .container { display: flex; padding: 20px; }
        .products { flex: 3; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product { background: white; border-radius: 4px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .product img { max-width: 100%; height: 200px; object-fit: contain; }
        .product h3 { margin: 10px 0; }
        .product .price { color: #b12704; font-weight: bold; font-size: 18px; }
        .add-to-cart { background: #ffd814; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%; }
        .sidebar { flex: 1; margin-left: 20px; }
        .cart-summary { background: white; padding: 15px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .cart-item { display: flex; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .cart-item img { width: 50px; height: 50px; object-fit: contain; margin-right: 10px; }
        .cart-item-info { flex-grow: 1; }
        .checkout-btn { background: #ffa41c; border: none; padding: 10px; width: 100%; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .success { color: green; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">amazon</div>
        <div class="search-bar">
            <input type="text" placeholder="Search products...">
        </div>
        <a href="#cart" class="cart">Cart (<?php echo count($_SESSION['cart']); ?>)</a>
    </div>

    <?php if (isset($checkoutSuccess)): ?>
        <div class="success">Thank you for your order!</div>
    <?php endif; ?>

    <div class="container">
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="https://via.placeholder.com/200x200?text=<?php echo urlencode($product['name']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                    <button class="add-to-cart" onclick="window.location.href='?action=add&id=<?php echo $product['id']; ?>'">Add to Cart</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sidebar">
            <div class="cart-summary" id="cart">
                <h2>Your Cart</h2>
                <?php if (empty($_SESSION['cart'])): ?>
                    <p>Your cart is empty</p>
                <?php else: ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="cart-item">
                            <img src="https://via.placeholder.com/100x100?text=<?php echo urlencode($item['product']['name']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                            <div class="cart-item-info">
                                <h4><?php echo htmlspecialchars($item['product']['name']); ?></h4>
                                <p>$<?php echo number_format($item['product']['price'], 2); ?></p>
                                <div>
                                    Qty: 
                                    <input type="number" min="1" value="<?php echo $item['quantity']; ?>" 
                                           onchange="window.location.href='?action=update&id=<?php echo $item['product']['id']; ?>&quantity='+this.value" style="width: 50px;">
                                    <a href="?action=remove&id=<?php echo $item['product']['id']; ?>" style="color: #0066c0; margin-left: 10px;">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div style="margin-top: 20px; font-weight: bold; font-size: 18px;">
                        Subtotal (<?php echo array_sum(array_column($_SESSION['cart'], 'quantity')); ?> items): $<?php echo number_format($cartTotal, 2); ?>
                    </div>
                    <button class="checkout-btn" onclick="window.location.href='?action=checkout'">Proceed to Checkout</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>