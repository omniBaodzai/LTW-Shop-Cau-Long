<?php

session_start(); // Bắt đầu session
include '../connect.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra xem giỏ hàng đã được khởi tạo trong session chưa
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm sản phẩm từ trang chi tiết sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Truy vấn sản phẩm từ cơ sở dữ liệu
    $sql_product = "SELECT id, name, price, image FROM products WHERE id = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("i", $product_id);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();

    if ($result_product->num_rows > 0) {
        $product = $result_product->fetch_assoc();
        $unique_item_id = 'product_' . $product['id'];

        // Thêm sản phẩm vào giỏ hàng
        if (!isset($_SESSION['cart'][$unique_item_id])) {
            $_SESSION['cart'][$unique_item_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'], // Lưu hình ảnh sản phẩm
                'quantity' => $quantity
            ];
        } else {
            $_SESSION['cart'][$unique_item_id]['quantity'] += $quantity;
        }

        // Trả về dữ liệu JSON
        $total_items = array_sum(array_column($_SESSION['cart'], 'quantity'));
        echo json_encode([
            'product_name' => $product['name'],
            'product_price' => number_format($product['price'], 0, ',', '.'),
            'product_image' => htmlspecialchars($product['image']), // Trả về đường dẫn hình ảnh
            'total_items' => $total_items
        ]);
    }
    exit();
}

// Xử lý cập nhật số lượng hoặc xóa sản phẩm trong giỏ hàng
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
    header('Location: gio-hang.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn</title>
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f9f9f9;
        }
        .cart-container {
            max-width: 900px;
            margin: 20px auto;
            border: 1px solid #eee;
            padding: 30px;
            background-color: white;
            border-radius: 12px; /* Slightly more rounded container */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* More prominent shadow */
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px; /* Slightly more space below heading */
            font-size: 2.2em; /* Larger heading */
        }
        .cart-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 18px 0; /* More vertical padding */
        }
        .cart-item img {
            width: 110px; /* Slightly larger image */
            height: 110px; /* Slightly larger image */
            object-fit: cover;
            border-radius: 8px; /* More rounded image corners */
            margin-right: 20px; /* More space to the right of the image */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* subtle shadow for image */
        }
        .item-details {
            flex-grow: 1;
        }
        .item-details h3 {
            margin: 0;
            font-size: 1.3em; /* Slightly larger item name */
            color: #333;
        }
        .item-details p {
            margin: 8px 0; /* More space for description */
            color: #777; /* Slightly darker grey for better readability */
            font-size: 1em; /* Slightly larger description font */
        }
        .item-price {
            font-weight: bold;
            color: #e63946; /* Using one of your gradient colors for emphasis */
            font-size: 1.25em; /* Larger price font */
        }

        /* Button styles with linear gradients and more rounding */
        .item-actions button,
        .cart-items button,
        .cart-summary button {
            color: white;
            border: none;
            padding: 10px 18px; /* Increased padding for all buttons */
            cursor: pointer;
            border-radius: 25px; /* Fully rounded buttons */
            font-size: 1em; /* Consistent font size for buttons */
            transition: all 0.3s ease; /* Smooth transition for all properties */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for button pop */
        }

        /* Specific styles for "Remove" button */
        .item-actions button {
            background: linear-gradient(90deg, #e63946 0%, #c1121f 100%); /* Red gradient */
        }
        .item-actions button:hover {
            background: linear-gradient(90deg, #c1121f 0%, #e63946 100%); /* Reverse gradient on hover */
            transform: translateY(-2px); /* Slight lift on hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Specific styles for quantity update button */
        .cart-items button {
            background: linear-gradient(90deg,rgb(29, 87, 34) 0%, #457b9d 100%); /* Blue gradient */
        }
        .cart-items button:hover {
            background: linear-gradient(90deg,rgb(53, 143, 83) 0%, #1d3557 100%); /* Reverse gradient on hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Specific styles for "Checkout" button */
        .cart-summary button {
            background: linear-gradient(90deg, #2a9d8f 0%,rgb(194, 109, 30) 100%); /* Green/Teal gradient */
            padding: 15px 30px; /* Larger padding for checkout button */
            font-size: 1.2em; /* Larger font for checkout */
        }
        .cart-summary button:hover {
            background: linear-gradient(90deg,rgb(91, 121, 155) 0%, #2a9d8f 100%); /* Reverse gradient on hover */
            transform: scale(1.03) translateY(-2px); /* Slightly larger and lifted */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }


        .cart-summary {
            text-align: right;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 25px; /* More space above summary content */
        }
        .cart-summary h2 {
            margin: 0 0 15px; /* Space below total heading */
            color: #333;
            font-size: 1.6em; /* Larger total heading */
        }

        .cart-items form {
            display: flex;
            align-items: center;
            gap: 15px; /* More space between quantity input and button */
        }
        .cart-items input[type="number"] {
            width: 70px; /* Slightly wider input */
            padding: 8px; /* More padding */
            border: 1px solid #ccc; /* Slightly darker border */
            border-radius: 6px; /* Rounded corners for input */
            text-align: center;
            font-size: 1em;
        }
        .message.success {
            text-align: center;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 1.1em; /* Slightly larger success message */
        }
    </style>
</head>
<body>
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
                            <p>Ưu đãi: <?= htmlspecialchars($item['promotion'] ?? 'Không có ưu đãi') ?></p>
                            <p class="item-price"><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</p>
                            <form action="gio-hang.php" method="POST">
                                <input type="hidden" name="unique_item_id" value="<?= htmlspecialchars($unique_item_id) ?>">
                                <input type="hidden" name="action" value="update">
                                <label for="quantity">Số lượng:</label>
                                <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" min="1" max="99">
                                <button type="submit">Cập nhật</button>
                            </form>
                        </div>
                        <div class="item-actions">
                            <form action="gio-hang.php" method="POST">
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
                <button onclick="window.location.href='thanh-toan.php'">Tiến hành thanh toán</button>
            <?php } ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>