<?php
session_start();

$cart_items_for_checkout = [];
$total_checkout_price = 0;
$shipping_fee = 30000; // Phí vận chuyển cố định
$final_total = 0;

// Xử lý khi nhấn "Mua ngay" từ trang sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'buy_now') {
    $product_id = $_POST['product_id'] ?? null;
    $product_type = $_POST['product_type'] ?? 'vot';
    $product_name = htmlspecialchars($_POST['product_name'] ?? 'Sản phẩm không tên');
    $product_price = floatval($_POST['product_price'] ?? 0);
    $product_image = htmlspecialchars($_POST['product_image'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id && $quantity > 0) {
        $cart_items_for_checkout[$product_type . '_' . $product_id] = [
            'id' => $product_id,
            'type' => $product_type,
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image,
            'quantity' => $quantity
        ];
        $total_checkout_price = $product_price * $quantity;
        $_SESSION['buy_now_item'] = $cart_items_for_checkout;
    } else {
        // Chuyển hướng về giỏ hàng nếu không có sản phẩm hợp lệ
        header('Location: /ap_cau_long/templates/header/giohang.php?error=no_product_to_checkout');
        exit;
    }
} elseif (isset($_SESSION['buy_now_item'])) {
    $cart_items_for_checkout = $_SESSION['buy_now_item'];
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'place_order') {
    $full_name = htmlspecialchars(trim($_POST['fullName'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $city = htmlspecialchars(trim($_POST['city'] ?? ''));
    $district = htmlspecialchars(trim($_POST['district'] ?? ''));
    $payment_method = htmlspecialchars(trim($_POST['paymentMethod'] ?? ''));

    if (empty($full_name) || empty($phone) || empty($address) || empty($city) || empty($district) || empty($payment_method)) {
        $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc.";
    } elseif (empty($cart_items_for_checkout)) {
        $error_message = "Không có sản phẩm nào trong đơn hàng để thanh toán.";
    } else {
        // --- BẮT ĐẦU PHẦN PHP CẦN BỔ SUNG CHO CHỨC NĂNG THỰC TẾ ---
        // 2. Lưu đơn hàng vào cơ sở dữ liệu
        // Ví dụ:
        // include '../../connect.php'; // Điều chỉnh đường dẫn đến file connect.php
        // $conn->begin_transaction(); // Bắt đầu giao dịch

        // try {
        //     $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, email, address, city, district, total_amount, shipping_fee, payment_method, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending')");
        //     $user_id = $_SESSION['user_id'] ?? null;
        //     $stmt->bind_param("issssssdds", $user_id, $full_name, $phone, $email, $address, $city, $district, $final_total, $shipping_fee, $payment_method);
        //     $stmt->execute();
        //     $order_id = $conn->insert_id;

        //     foreach ($cart_items_for_checkout as $item) {
        //         $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_type, product_name, price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        //         $stmt_item->bind_param("iisssd", $order_id, $item['id'], $item['type'], $item['name'], $item['price'], $item['quantity']);
        //         $stmt_item->execute();
        //         $stmt_item->close();
        //     }

        //     $conn->commit();
        //     unset($_SESSION['cart']);
        //     unset($_SESSION['buy_now_item']);
        //     // Chuyển hướng đến trang xác nhận đơn hàng, thay đổi đường dẫn nếu cần
        //     header('Location: /ap_cau_long/order_confirmation.php?order_id=' . $order_id);
        //     exit;

        // } catch (Exception $e) {
        //     $conn->rollback();
        //     $error_message = "Có lỗi xảy ra khi tạo đơn hàng: " . $e->getMessage();
        //     error_log("Lỗi tạo đơn hàng: " . $e->getMessage());
        // }
        // --- KẾT THÚC PHẦN PHP CẦN BỔ SUNG ---

        // Ví dụ: Xử lý thành công giả lập
        $success_message = "Đơn hàng của bạn đã được xác nhận thành công! (Chức năng lưu DB đang được phát triển)";
        unset($_SESSION['cart']);
        unset($_SESSION['buy_now_item']);
        // Có thể chuyển hướng đến một trang xác nhận đơn hàng
        // header('Location: /ap_cau_long/templates/header/order_confirmation.php'); // Đặt đường dẫn thích hợp
        // exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .checkout-container { max-width: 800px; margin: 20px auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-bottom: 30px; }
        .section-title { font-size: 1.3em; margin-bottom: 15px; color: #007bff; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea,
        .form-group select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .payment-methods { margin-top: 20px; }
        .payment-methods label { display: block; margin-bottom: 10px; }
        .payment-methods input[type="radio"] { margin-right: 10px; }
        .order-summary { border: 1px solid #eee; padding: 20px; margin-top: 30px; background-color: #f9f9f9; border-radius: 5px; }
        .order-summary ul { list-style: none; padding: 0; }
        .order-summary ul li { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .order-summary ul li span:first-child { font-weight: bold; }
        .order-total { font-size: 1.4em; font-weight: bold; text-align: right; margin-top: 15px; color: #dc3545; }
        .place-order-button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            margin-top: 30px;
            text-align: center;
        }
        .message {
  display: none; /* Ẩn mặc định */
  padding: 10px; 
  margin-bottom: 15px; 
  border-radius: 4px;
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
  display: block; /* Hiện khi có class này */
}

    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Thanh toán đơn hàng</h1>

        <?php if (!empty($error_message)) {
            echo '<p class="message error show">' . htmlspecialchars($error_message) . '</p>';
        } ?>
        <?php if (!empty($success_message)) {
            echo '<p class="message success show">' . htmlspecialchars($success_message) . '</p>';
        } ?>

        <div class="shipping-info">
            <h2 class="section-title">Thông tin giao hàng</h2>
            <form action="/ap_cau_long/templates/header/thanhtoan.php" method="POST">
                <input type="hidden" name="action" value="place_order">
                <div class="form-group">
                    <label for="fullName">Họ và tên:</label>
                    <input type="text" id="fullName" name="fullName" required value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ:</label>
                    <textarea id="address" name="address" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="city">Tỉnh/Thành phố:</label>
                    <input type="text" id="city" name="city" required value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="district">Quận/Huyện:</label>
                    <input type="text" id="district" name="district" required value="<?php echo isset($_POST['district']) ? htmlspecialchars($_POST['district']) : ''; ?>">
                </div>

                <div class="payment-methods">
                    <h2 class="section-title">Phương thức thanh toán</h2>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="paymentMethod" value="cod" <?php echo (isset($_POST['paymentMethod']) && $_POST['paymentMethod'] == 'cod') ? 'checked' : 'checked'; ?>> Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="paymentMethod" value="banktransfer" <?php echo (isset($_POST['paymentMethod']) && $_POST['paymentMethod'] == 'banktransfer') ? 'checked' : ''; ?>> Chuyển khoản ngân hàng
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="paymentMethod" value="momo" <?php echo (isset($_POST['paymentMethod']) && $_POST['paymentMethod'] == 'momo') ? 'checked' : ''; ?>> Ví điện tử MoMo
                        </label>
                    </div>
                </div>

                <div class="order-summary">
                    <h2 class="section-title">Tóm tắt đơn hàng</h2>
                    <ul>
                        <?php if (!empty($cart_items_for_checkout)) {
                            foreach ($cart_items_for_checkout as $item) { ?>
                                <li>
                                    <span><?= htmlspecialchars($item['name']); ?> (x<?= htmlspecialchars($item['quantity']); ?>)</span>
                                    <span><?= number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</span>
                                </li>
                        <?php }
                        } else { ?>
                            <li>Không có sản phẩm nào trong đơn hàng.</li>
                        <?php } ?>
                        <li><span>Phí vận chuyển</span> <span><?= number_format($shipping_fee, 0, ',', '.'); ?> VNĐ</span></li>
                    </ul>
                    <div class="order-total">
                        Tổng cộng: <?= number_format($final_total, 0, ',', '.'); ?> VNĐ
                    </div>
                </div>

                <button type="submit" class="place-order-button">Xác nhận đơn hàng</button>
            </form>
        </div>
    </div>
</body>
</html>