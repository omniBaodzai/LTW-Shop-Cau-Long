<?php
session_start();

// Kiểm tra xem giỏ hàng đã được khởi tạo trong session chưa
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm sản phẩm từ trang chi tiết sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_to_cart') {
    $product_id = $_POST['product_id'] ?? null;
    $product_type = $_POST['product_type'] ?? 'vot';
    $product_name = htmlspecialchars($_POST['product_name'] ?? 'Sản phẩm không tên');
    $product_price = floatval($_POST['product_price'] ?? 0);
    $product_image = htmlspecialchars($_POST['product_image'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id && $quantity > 0) {
        $unique_item_id = $product_type . '_' . $product_id;

        if (isset($_SESSION['cart'][$unique_item_id])) {
            $_SESSION['cart'][$unique_item_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$unique_item_id] = [
                'id' => $product_id,
                'type' => $product_type,
                'name' => $product_name,
                'price' => $product_price,
                'image' => $product_image,
                'quantity' => $quantity
            ];
        }
        // Chuyển hướng về trang giỏ hàng sau khi thêm để tránh gửi lại form khi refresh
        header('Location: /ap_cau_long/templates/header/giohang.php?status=added');
        exit;
    }
}

// Xử lý cập nhật số lượng hoặc xóa sản phẩm trong giỏ hàng (từ chính trang giỏ hàng)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && ($_POST['action'] == 'update' || $_POST['action'] == 'remove')) {
    $unique_item_id = $_POST['unique_item_id'] ?? null;
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($unique_item_id && isset($_SESSION['cart'][$unique_item_id])) {
        if ($_POST['action'] == 'update') {
            $_SESSION['cart'][$unique_item_id]['quantity'] = max(1, $quantity);
        } elseif ($_POST['action'] == 'remove') {
            unset($_SESSION['cart'][$unique_item_id]);
        }
    }
    // Chuyển hướng lại trang giỏ hàng
    header('Location: /ap_cau_long/templates/header/giohang.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    background-color: #f9f9f9;
}

.cart-container {
    max-width: 900px;
    margin: 20px auto 80px; /* Tạo khoảng cách với header và footer */
    border: 1px solid #eee;
    padding: 30px 25px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    background-color: white;
    min-height: 500px; /* Giữ khung có độ cao tối thiểu, đẩy footer xuống */
    border-radius: 8px;
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

.cart-items {
    margin-bottom: 30px;
}

.cart-item {
    display: flex;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding: 15px 0;
    gap: 20px;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
}

.item-details {
    flex-grow: 1;
}

.item-details h3 {
    margin: 0 0 5px 0;
    font-size: 1.1em;
    color: #333;
}

.item-details p {
    margin: 0;
    color: #666;
    font-size: 0.9em;
}

.item-price {
    font-weight: bold;
    color: #e74c3c;
    margin-top: 5px;
}

.item-quantity {
    display: flex;
    align-items: center;
    gap: 5px;
}

.item-quantity input {
    width: 50px;
    padding: 5px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.item-quantity button {
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 4px;
}

.item-quantity button:hover {
    background-color: #e0e0e0;
}

.item-actions button {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.item-actions button:hover {
    background-color: #c82333;
}

.cart-summary {
    text-align: right;
    margin-top: 30px;
    border-top: 1px solid #eee;
    padding-top: 20px;
}

.cart-summary h2 {
    margin: 0 0 15px 0;
    color: #333;
}

.cart-summary button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 12px 25px;
    font-size: 1.1em;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 15px;
    transition: background-color 0.2s;
}

.cart-summary button:hover {
    background-color: #218838;
}

.message.success {
    color: green;
    margin-bottom: 15px;
    text-align: center;
}

    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="cart-container">
        <h1>Giỏ hàng của bạn</h1>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'added') {
            echo '<p class="message success">Sản phẩm đã được thêm vào giỏ hàng!</p>';
        } ?>
        <div class="cart-items">
            <?php
            $total_cart_price = 0;
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $unique_item_id => $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_cart_price += $subtotal;
            ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p>Loại: <?= htmlspecialchars(ucfirst($item['type'])) ?></p>
                            <p class="item-price"><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</p>
                        </div>
                        <div class="item-quantity">
                            <form action="/ap_cau_long/templates/header/giohang.php" method="POST" style="display:inline;">
                                <input type="hidden" name="unique_item_id" value="<?= htmlspecialchars($unique_item_id) ?>">
                                <input type="hidden" name="action" value="update">
                                <button type="submit" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>">-</button>
                                <input type="text" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" readonly>
                                <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>">+</button>
                            </form>
                        </div>
                        <div class="item-actions">
                            <form action="/ap_cau_long/templates/header/giohang.php" method="POST" style="display:inline;">
                                <input type="hidden" name="unique_item_id" value="<?= htmlspecialchars($unique_item_id) ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit">Xóa</button>
                            </form>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p style='text-align:center;'>Giỏ hàng của bạn đang trống.</p>";
            }
            ?>
        </div>

        <div class="cart-summary">
            <h2>Tổng cộng: <?= number_format($total_cart_price, 0, ',', '.') ?> VNĐ</h2>
            <?php if ($total_cart_price > 0) { ?>
                <button onclick="window.location.href='/ap_cau_long/templates/header/thanhtoan.php'">Tiến hành thanh toán</button>
            <?php } ?>
        </div>
    </div>
    <?php include_once __DIR__ . '/../footer.php'; ?>

<script src="/ap_cau_long/js/main.js"></script>
</body>
</html>