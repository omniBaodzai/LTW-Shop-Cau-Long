<?php
session_start(); // Bắt đầu session
include '../connect.php'; // Kết nối cơ sở dữ liệu

$cart_items_for_checkout = [];
$total_checkout_price = 0;
$shipping_fee = 30000; // Phí vận chuyển cố định
$final_total = 0;

// Lấy thông tin người dùng nếu đã đăng nhập
$user_info = [];
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $sql_user = "SELECT name, email, phone, address, city, district FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    if ($result_user->num_rows > 0) {
        $user_info = $result_user->fetch_assoc();
    }
}

// Xử lý khi nhấn "Mua ngay" từ trang sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'buy_now') {
    $product_id = $_POST['product_id'] ?? null;
    $product_name = htmlspecialchars($_POST['product_name'] ?? 'Sản phẩm không tên');
    $product_price = floatval($_POST['product_price'] ?? 0);
    $product_image = htmlspecialchars($_POST['product_image'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id && $quantity > 0) {
        $_SESSION['buy_now_item'] = [
            'product_id' => $product_id,
            'product_name' => $product_name,
            'price' => $product_price,
            'product_image' => $product_image,
            'quantity' => $quantity
        ];
        header('Location: thanh-toan.php');
        exit();
    } else {
        $error_message = "Không có sản phẩm hợp lệ để thanh toán.";
    }
} elseif (isset($_SESSION['buy_now_item'])) {
    $cart_items_for_checkout[] = $_SESSION['buy_now_item'];
    foreach ($cart_items_for_checkout as $item) {
        $total_checkout_price += $item['price'] * $item['quantity'];
    }
} else {
    // Nếu không phải "Mua ngay", lấy từ giỏ hàng thông thường (session['cart'])
    $cart_items_for_checkout = $_SESSION['cart'] ?? [];
    foreach ($cart_items_for_checkout as $item) {
        $total_checkout_price += $item['price'] * $item['quantity'];
    }
}

$final_total = $total_checkout_price + $shipping_fee;

$error_message = '';
$success_message = '';

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'place_order') {
    $full_name = htmlspecialchars(trim($_POST['fullName']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = htmlspecialchars(trim($_POST['email']));
    $address = htmlspecialchars(trim($_POST['address']));
    $city = htmlspecialchars(trim($_POST['city']));
    $district = htmlspecialchars(trim($_POST['district']));
    $payment_method = htmlspecialchars(trim($_POST['paymentMethod']));

    if (empty($full_name) || empty($phone) || empty($address) || empty($city) || empty($district) || empty($payment_method)) {
        $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc.";
    } elseif (empty($cart_items_for_checkout)) {
        $error_message = "Không có sản phẩm nào trong đơn hàng để thanh toán.";
    } else {
        $conn->begin_transaction(); // Bắt đầu giao dịch

        try {
            // Lấy user_id nếu đã đăng nhập
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            // Chuẩn bị câu truy vấn chèn đơn hàng
            $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, email, address, city, district, payment_method, total_price, shipping_fee, final_total, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssssssddd", $user_id, $full_name, $phone, $email, $address, $city, $district, $payment_method, $total_checkout_price, $shipping_fee, $final_total);
            $stmt->execute();
            $order_id = $conn->insert_id; // Lấy ID đơn hàng vừa tạo
            $stmt->close();

            // Chuẩn bị câu truy vấn chèn từng sản phẩm vào order_items
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
            foreach ($cart_items_for_checkout as $item) {
                $stmt_item->bind_param("iisdi", $order_id, $item['product_id'], $item['product_name'], $item['price'], $item['quantity']);
                $stmt_item->execute();
            }
            $stmt_item->close();

            $conn->commit(); // Xác nhận giao dịch

            unset($_SESSION['cart']);
            unset($_SESSION['buy_now_item']);

            // Chuyển hướng đến trang xác nhận đơn hàng
            header('Location: chi-tiet-don-hang.php?order_id=' . $order_id);
            exit();
        } catch (Exception $e) {
            $conn->rollback(); // Hủy giao dịch nếu có lỗi
            $error_message = "Có lỗi xảy ra khi tạo đơn hàng: " . $e->getMessage();
            error_log("Lỗi tạo đơn hàng: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thanh toán</title>
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<style>
    body {
        font-family: "Segoe UI", Tahoma, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

        .checkout-container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .section-title {
            font-size: 1.5rem; /* Kích thước font */
            font-weight: 800;
            color: #1d3557;
            margin-bottom: 16px;
            position: relative;
            display: inline-block;
            text-align: center; /* Căn giữa nội dung */
            width: 100%; /* Đảm bảo nội dung chiếm toàn bộ chiều ngang */
        }
        .section-title::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -8px;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #e63946 0%, #ff7f2a 100%);
            border-radius: 2px;
        }
        .section-title span {
            color: #e63946;
        }
        h1 {
            text-align: center;
            color:rgb(31, 141, 45);
            margin-bottom: 30px;
            font-size: 2.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
            color: #000; /* Màu chữ đen */
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group input[type="tel"]:focus,
        .form-group textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-group i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #007bff;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-actions .save-button {
            background-color: #28a745;
            color: white;
        }

        .form-actions .save-button:hover {
            background-color: #218838;
        }

        .form-actions .cancel-button {
            background-color: #dc3545;
            color: white;
        }

        .form-actions .cancel-button:hover {
            background-color: #c82333;
        }
    .payment-methods {
        margin-top: 20px;
    }

    .payment-methods label {
        display: block;
        margin-bottom: 10px;
        font-size: 1rem;
        color: #333;
    }

    .payment-methods input[type="radio"] {
        margin-right: 10px;
    }

    .order-summary {
        border: 1px solid #eee;
        padding: 20px;
        margin-top: 30px;
        background-color: #f9f9f9;
        border-radius: 5px;
    }

    .order-summary ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .order-summary ul li {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .order-summary ul li span:first-child {
        font-weight: bold;
    }

    .order-total {
        font-size: 1.4rem;
        font-weight: bold;
        text-align: right;
        margin-top: 15px;
        color: #dc3545;
    }

    .place-order-button {
        display: block;
        width: 100%;
        padding: 15px;
        background: linear-gradient(90deg, #21c4dd 0%, #39b523 100%);
        color: white;
        border: none;
        
        border-radius: 22px;
        font-size: 1.2rem;
        cursor: pointer;
        margin-top: 30px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .place-order-button:hover {
        background-color: #218838;
    }

    .message {
        display: none;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        font-size: 1rem;
    }

    .message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.show {
        display: block;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .action-buttons button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .action-buttons .save-button {
        background-color: #28a745;
        color: white;
    }

    .action-buttons .save-button:hover {
        background-color: #218838;
    }

    .action-buttons .cancel-button {
        background-color: #dc3545;
        color: white;
    }

    .action-buttons .cancel-button:hover {
        background-color: #c82333;
    }
</style>
<div class="checkout-container">
    <h1>Thanh toán đơn hàng</h1>

    <?php if (!empty($error_message)) : ?>
        <p class="message error show"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="action" value="place_order">

        <div class="section-title">Thông tin khách hàng</div>
        <div class="form-group">
            <label for="fullName">Họ và tên *</label>
            <input type="text" name="fullName" id="fullName" value="<?= htmlspecialchars($user_info['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Số điện thoại *</label>
            <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user_info['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="address">Địa chỉ *</label>
            <input type="text" name="address" id="address" value="<?= htmlspecialchars($user_info['address'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="city">Tỉnh/Thành phố *</label>
            <input type="text" name="city" id="city" value="<?= htmlspecialchars($user_info['city'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="district">Quận/Huyện *</label>
            <input type="text" name="district" id="district" value="<?= htmlspecialchars($user_info['district'] ?? '') ?>" required>
        </div>

        <div class="section-title">Phương thức thanh toán</div>
        <div class="payment-methods">
            <label><input type="radio" name="paymentMethod" value="cod" checked> Thanh toán khi nhận hàng (COD)</label>
            <label><input type="radio" name="paymentMethod" value="bank"> Chuyển khoản ngân hàng</label>
        </div>

        <div class="section-title">Tóm tắt đơn hàng</div>
        <div class="order-summary">
            <ul>
                <?php foreach ($cart_items_for_checkout as $item): ?>
                    <li><span><?= htmlspecialchars($item['product_name']); ?> (x<?= htmlspecialchars($item['quantity']); ?>)</span><span><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>₫</span></li>
                <?php endforeach; ?>
                <li><span>Tạm tính:</span><span><?= number_format($total_checkout_price, 0, ',', '.') ?>₫</span></li>
                <li><span>Phí vận chuyển:</span><span><?= number_format($shipping_fee, 0, ',', '.') ?>₫</span></li>
            </ul>
            <div class="order-total">Tổng cộng: <?= number_format($final_total, 0, ',', '.') ?>₫</div>
        </div>

        <button type="submit" class="place-order-button">Đặt hàng</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>