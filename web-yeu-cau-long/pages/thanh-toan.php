<?php
session_start(); // Bắt đầu session
include '../connect.php'; // Kết nối cơ sở dữ liệu

$cart_items_for_checkout = [];
$total_checkout_price = 0;
$shipping_fee = 30000; // Phí vận chuyển cố định
$final_total = 0;
$error_message = '';
$success_message = '';

// Lấy thông tin người dùng nếu đã đăng nhập
$user_info = [];
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $sql_user = "SELECT name, email, phone, address, city, district FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    if ($stmt_user === false) {
        error_log("Failed to prepare statement for user info: " . $conn->error);
    } else {
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($result_user->num_rows > 0) {
            $user_info = $result_user->fetch_assoc();
        }
        $stmt_user->close();
    }
}

// --- Xử lý khi nhấn "Mua ngay" từ trang sản phẩm (POST request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'buy_now') {
    $product_id = $_POST['product_id'] ?? null;
    $product_name = htmlspecialchars($_POST['product_name'] ?? 'Sản phẩm không tên');
    $product_price = floatval($_POST['product_price'] ?? 0);
    $product_image = htmlspecialchars($_POST['product_image'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);
    
    // Thêm truy vấn để lấy thông tin bảo hành từ bảng products cho 'Mua ngay'
    $warranty_duration = null;
    if ($product_id) {
        $stmt_warranty = $conn->prepare("SELECT warranty FROM products WHERE id = ?");
        if ($stmt_warranty) { // Check if prepare was successful
            $stmt_warranty->bind_param("i", $product_id);
            $stmt_warranty->execute();
            $result_warranty = $stmt_warranty->get_result();
            if ($warranty_data = $result_warranty->fetch_assoc()) {
                $warranty_duration = $warranty_data['warranty'];
            }
            $stmt_warranty->close();
        } else {
            error_log("Failed to prepare statement for product warranty: " . $conn->error);
        }
    }

    if ($product_id && $quantity > 0 && $product_price >= 0) {
        $_SESSION['buy_now_item'] = [
            'product_id' => $product_id,
            'product_name' => $product_name,
            'price' => $product_price,
            'product_image' => $product_image,
            'quantity' => $quantity,
            'warranty_duration' => $warranty_duration // Lưu thời hạn bảo hành vào session
        ];
        // Xóa giỏ hàng thông thường khi "Mua ngay" để tránh xung đột
        unset($_SESSION['cart']); 
        // Chuyển hướng để chỉ xử lý từ session 'buy_now_item'
        header('Location: thanh-toan.php?source=buy_now');
        exit();
    } else {
        $error_message = "Không có sản phẩm hợp lệ để thanh toán ngay.";
    }
}

// --- Xác định các sản phẩm sẽ thanh toán ---
// Logic này chạy khi trang thanh-toan.php được tải (GET request, sau redirect hoặc truy cập trực tiếp)

// Trường hợp 1: Yêu cầu đến từ nút "Tiến hành thanh toán" trên trang giỏ hàng
if (isset($_GET['source']) && $_GET['source'] == 'cart') {
    // Rất quan trọng: Xóa buy_now_item ngay lập tức nếu đến từ giỏ hàng
    unset($_SESSION['buy_now_item']); 
    
    // Đảm bảo $_SESSION['cart'] là một mảng
    $cart_items_for_checkout = $_SESSION['cart'] ?? [];
    
    // Lọc bỏ các mục không hợp lệ trong giỏ hàng (thiếu thông tin) và lấy warranty từ DB
    $valid_cart_items = [];
    foreach ($cart_items_for_checkout as $key => $item) {
        if (isset($item['product_id'], $item['product_name'], $item['price'], $item['quantity'], $item['image'])) {
            // Lấy thông tin bảo hành từ DB cho mỗi sản phẩm trong giỏ hàng
            $product_id_cart = intval($item['product_id']);
            $warranty_duration_cart = null;
            $stmt_warranty = $conn->prepare("SELECT warranty FROM products WHERE id = ?");
            if ($stmt_warranty) {
                $stmt_warranty->bind_param("i", $product_id_cart);
                $stmt_warranty->execute();
                $result_warranty = $stmt_warranty->get_result();
                if ($warranty_data = $result_warranty->fetch_assoc()) {
                    $warranty_duration_cart = $warranty_data['warranty'];
                }
                $stmt_warranty->close();
            } else {
                error_log("Failed to prepare statement for product warranty (cart): " . $conn->error);
            }
            
            $item['warranty_duration'] = $warranty_duration_cart; // Thêm warranty_duration vào item
            $valid_cart_items[$key] = $item;
        } else {
            error_log("Cart item missing data: " . json_encode($item));
            // Tùy chọn: Xóa mục bị lỗi khỏi giỏ hàng
            // unset($_SESSION['cart'][$key]);
        }
    }
    $_SESSION['cart'] = $valid_cart_items; // Cập nhật lại session cart với các item hợp lệ và có warranty
    $cart_items_for_checkout = $valid_cart_items;

    if (empty($cart_items_for_checkout)) {
        $error_message = "Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm để thanh toán.";
        // Tùy chọn: chuyển hướng về trang giỏ hàng nếu trống
        // header('Location: ../cart.php');
        // exit();
    }
}
// Trường hợp 2: Yêu cầu đến từ redirect sau khi nhấn "Mua ngay"
elseif (isset($_GET['source']) && $_GET['source'] == 'buy_now') {
    // Nếu có 'buy_now_item' trong session, sử dụng nó
    if (isset($_SESSION['buy_now_item']) && !empty($_SESSION['buy_now_item'])) {
        // Kiểm tra tính hợp lệ của buy_now_item
        $buy_now_item = $_SESSION['buy_now_item'];
        if (isset($buy_now_item['product_id'], $buy_now_item['product_name'], $buy_now_item['price'], $buy_now_item['quantity'], $buy_now_item['product_image'])) {
            // buy_now_item đã có warranty_duration từ khi mua ngay
            $cart_items_for_checkout[] = $buy_now_item;
        } else {
            $error_message = "Thông tin sản phẩm 'Mua ngay' không đầy đủ hoặc không hợp lệ. Vui lòng thử lại.";
            error_log("Buy now item missing data: " . json_encode($buy_now_item));
            unset($_SESSION['buy_now_item']); // Xóa mục lỗi
        }
    } else {
        // Nếu không có buy_now_item trong session (vd: refresh trang sau khi đã dùng),
        // thì mặc định không có sản phẩm nào
        $error_message = "Không có sản phẩm 'Mua ngay' hợp lệ để thanh toán.";
    }
}
// Trường hợp 3: Truy cập trang thanh-toan.php mà không có tham số 'source' (ví dụ: refresh trang, gõ trực tiếp URL)
else {
    // Nếu có buy_now_item còn sót lại, ưu tiên nó (trường hợp người dùng refresh trang thanh toán sau khi Mua ngay)
    if (isset($_SESSION['buy_now_item']) && !empty($_SESSION['buy_now_item'])) {
        $buy_now_item = $_SESSION['buy_now_item'];
        if (isset($buy_now_item['product_id'], $buy_now_item['product_name'], $buy_now_item['price'], $buy_now_item['quantity'], $buy_now_item['product_image'])) {
            $cart_items_for_checkout[] = $buy_now_item;
        } else {
            $error_message = "Thông tin sản phẩm 'Mua ngay' không đầy đủ hoặc không hợp lệ. Vui lòng thử lại.";
            error_log("Buy now item (refresh) missing data: " . json_encode($buy_now_item));
            unset($_SESSION['buy_now_item']); // Xóa mục lỗi
        }
    }
    // Nếu không có buy_now_item, kiểm tra giỏ hàng thông thường
    elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $valid_cart_items = [];
        foreach ($_SESSION['cart'] as $key => $item) {
            if (isset($item['product_id'], $item['product_name'], $item['price'], $item['quantity'], $item['image'])) {
                // Lấy thông tin bảo hành từ DB cho mỗi sản phẩm trong giỏ hàng (lại một lần nữa nếu cần)
                $product_id_cart = intval($item['product_id']);
                $warranty_duration_cart = null;
                $stmt_warranty = $conn->prepare("SELECT warranty FROM products WHERE id = ?");
                if ($stmt_warranty) {
                    $stmt_warranty->bind_param("i", $product_id_cart);
                    $stmt_warranty->execute();
                    $result_warranty = $stmt_warranty->get_result();
                    if ($warranty_data = $result_warranty->fetch_assoc()) {
                        $warranty_duration_cart = $warranty_data['warranty'];
                    }
                    $stmt_warranty->close();
                } else {
                    error_log("Failed to prepare statement for product warranty (cart refresh): " . $conn->error);
                }
                $item['warranty_duration'] = $warranty_duration_cart; // Thêm warranty_duration vào item
                $valid_cart_items[$key] = $item;
            } else {
                error_log("Cart item (refresh) missing data: " . json_encode($item));
                // Tùy chọn: Xóa mục bị lỗi khỏi giỏ hàng
                // unset($_SESSION['cart'][$key]);
            }
        }
        $_SESSION['cart'] = $valid_cart_items; // Cập nhật lại session cart
        $cart_items_for_checkout = $valid_cart_items;

    }
    // Nếu cả hai đều trống
    if (empty($cart_items_for_checkout)) { // Kiểm tra lại sau khi đã cố gắng lấy từ cả 2 nguồn
        $error_message = "Không có sản phẩm nào để thanh toán. Vui lòng thêm sản phẩm vào giỏ hàng hoặc sử dụng chức năng 'Mua ngay'.";
    }
}


// --- Tính toán tổng tiền dựa trên $cart_items_for_checkout đã xác định ---
foreach ($cart_items_for_checkout as $item) {
    // Thêm kiểm tra isset() ở đây để tránh lỗi nếu có item nào đó bị thiếu key
    $item_price = isset($item['price']) ? floatval($item['price']) : 0;
    $item_quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
    $total_checkout_price += $item_price * $item_quantity;
}
$final_total = $total_checkout_price + $shipping_fee;

// --- Xử lý đặt hàng khi form được gửi (POST request) ---
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
    } elseif (empty($cart_items_for_checkout)) { // Kiểm tra lại giỏ hàng trước khi đặt
        $error_message = "Không có sản phẩm nào trong đơn hàng để thanh toán. Vui lòng thử lại.";
    } else {
        $conn->begin_transaction(); // Bắt đầu giao dịch

        try {
            $user_id_for_order = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $order_date_created = date('Y-m-d H:i:s'); // Lấy ngày giờ đặt hàng để tính bảo hành

            $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, email, address, city, district, payment_method, total_price, shipping_fee, final_total, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("Lỗi khi chuẩn bị truy vấn đơn hàng: " . $conn->error);
            }
            // Sửa lỗi: Chuỗi định nghĩa kiểu phải khớp với số lượng biến
            $stmt->bind_param("isssssssddds", $user_id_for_order, $full_name, $phone, $email, $address, $city, $district, $payment_method, $total_checkout_price, $shipping_fee, $final_total, $order_date_created);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi chèn đơn hàng: " . $stmt->error);
            }
            $order_id = $conn->insert_id;
            $stmt->close();

            // Prepare statement for order_items
            // Bổ sung serial_number và warranty_expire_date
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, serial_number, price, quantity, warranty_expire_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt_item === false) {
                throw new Exception("Lỗi khi chuẩn bị truy vấn chi tiết đơn hàng: " . $conn->error);
            }

            // Lấy user_id, nếu không có thì gán là 'GUEST' để dùng trong serial_number
            $user_id_for_sn = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'GUEST';

            foreach ($cart_items_for_checkout as $item) {
                $product_id_int = intval($item['product_id'] ?? 0);
                $item_name = htmlspecialchars($item['product_name'] ?? 'Unknown Product');
                $item_price_float = floatval($item['price'] ?? 0);
                $item_quantity_int = intval($item['quantity'] ?? 0);
                $warranty_duration_item = $item['warranty_duration'] ?? null; // Lấy thời hạn bảo hành của sản phẩm

                // Tính toán ngày hết hạn bảo hành cho từng sản phẩm
                $calculated_warranty_expire_date = null;
                if (!empty($warranty_duration_item)) {
                    $warranty_parts = explode(' ', strtolower(trim($warranty_duration_item)));
                    if (count($warranty_parts) >= 2) {
                        $value = (int)$warranty_parts[0];
                        $unit = strtolower($warranty_parts[1]);

                        $date_object = new DateTime($order_date_created); // Tính từ ngày đặt hàng của đơn hàng

                        switch ($unit) {
                            case 'tháng':
                            case 'thang':
                                $date_object->modify("+$value months");
                                break;
                            case 'năm':
                            case 'nam':
                                $date_object->modify("+$value years");
                                break;
                            // Thêm các trường hợp khác nếu có
                        }
                        $calculated_warranty_expire_date = $date_object->format('Y-m-d');
                    }
                }

                // Với mỗi đơn vị sản phẩm trong giỏ hàng, tạo một dòng order_item riêng
                // để có thể gán serial_number duy nhất cho từng sản phẩm.
                for ($i = 0; $i < $item_quantity_int; $i++) {
                    // Tăng bộ đếm cho từng đơn vị sản phẩm của cùng một loại trong đơn hàng này
                    // Đây là phần quan trọng để tạo ra sự khác biệt cho từng cái vợt
                    $unit_in_product_index = $i + 1; // Bắt đầu từ 1

                    // Tạo serial_number theo định dạng: USERID-ORDERID-PRODUCTID-UNITINDEX-UNIQID
                    // Ví dụ: 1-21-3-1-6845d55eb1c16 (User 1, Order 21, Product 3, đơn vị thứ 1, uniqid)
                    // Ví dụ: 1-21-3-2-6845d55eb1c16 (User 1, Order 21, Product 3, đơn vị thứ 2, uniqid)
                    $serial_number = $user_id_for_sn . '-' . $order_id . '-' . $product_id_int . '-' . $unit_in_product_index . '-' . uniqid(); 
                    
                    // Bind_param cho order_items
                    // iisdsis (int, int, string, string, double, int, string)
                    $single_unit_quantity = 1; // Mỗi dòng order_item đại diện cho 1 đơn vị sản phẩm
                    $stmt_item->bind_param("iisdsis", 
                        $order_id, 
                        $product_id_int, 
                        $item_name, 
                        $serial_number, 
                        $item_price_float, 
                        $single_unit_quantity, 
                        $calculated_warranty_expire_date
                    );
                    
                    if (!$stmt_item->execute()) {
                        throw new Exception("Lỗi khi chèn sản phẩm vào đơn hàng (Product ID: {$product_id_int}, SN: {$serial_number}): " . $stmt_item->error);
                    }
                }
            }
            $stmt_item->close();

            $conn->commit(); // Xác nhận giao dịch

            // Xóa sản phẩm khỏi session sau khi đặt hàng thành công
            unset($_SESSION['cart']);
            unset($_SESSION['buy_now_item']); // Quan trọng: luôn xóa cả buy_now_item sau khi đặt hàng

            // Chuyển hướng đến trang xác nhận đơn hàng
            header('Location: chi-tiet-don-hang.php?order_id=' . $order_id);
            exit();

        } catch (Exception $e) {
            $conn->rollback(); // Hủy giao dịch nếu có lỗi
            $error_message = "Có lỗi xảy ra khi tạo đơn hàng: " . $e->getMessage();
            error_log("Lỗi tạo đơn hàng: " . $e->getMessage()); // Ghi lỗi vào log hệ thống
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
    <?php include '../includes/header.php'; // Đảm bảo đường dẫn đúng ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Your existing CSS block should go here, or link to an external CSS file */
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
            font-size: 1.5rem;
            font-weight: 800;
            color: #1d3557;
            margin-bottom: 16px;
            position: relative;
            display: inline-block;
            text-align: center;
            width: 100%;
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
            color: #000;
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
            background: linear-gradient(90deg, #39b523 0%, #21c4dd 100%);
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
    </style>
</head>
<body>
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
                <?php
                // Display cart items for checkout
                if (!empty($cart_items_for_checkout)) {
                    foreach ($cart_items_for_checkout as $item):
                        // Kiểm tra sự tồn tại của các key trước khi hiển thị
                        $product_name = htmlspecialchars($item['product_name'] ?? 'Tên sản phẩm không xác định');
                        $quantity = htmlspecialchars($item['quantity'] ?? 0);
                        $price = htmlspecialchars($item['price'] ?? 0); // Lấy giá gốc
                        $subtotal_item = (isset($item['price']) && isset($item['quantity'])) ? ($item['price'] * $item['quantity']) : 0;
                ?>
                        <li>
                            <span><?= $product_name; ?> (x<?= $quantity; ?>)</span>
                            <span><?= number_format($subtotal_item, 0, ',', '.') ?>₫</span>
                        </li>
                <?php
                    endforeach;
                } else {
                    echo '<li><span style="color: #888;">Không có sản phẩm nào để hiển thị.</span></li>';
                }
                ?>
                <li><span>Tạm tính:</span><span><?= number_format($total_checkout_price, 0, ',', '.') ?>₫</span></li>
                <li><span>Phí vận chuyển:</span><span><?= number_format($shipping_fee, 0, ',', '.') ?>₫</span></li>
            </ul>
            <div class="order-total">Tổng cộng: <?= number_format($final_total, 0, ',', '.') ?>₫</div>
        </div>

        <button type="submit" class="place-order-button">Đặt hàng</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>