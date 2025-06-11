<?php
include '../connect.php'; // Kết nối cơ sở dữ liệu (đảm bảo đường dẫn đúng)

if (!isset($_SESSION['user_id'])) {
    // Tùy chọn: Lưu thông báo để hiển thị sau khi chuyển hướng
    $_SESSION['message'] = 'Vui lòng đăng nhập để tiến hành thanh toán.';
    header('Location: ../pages/dang-nhap.php'); // Thay đổi thành đường dẫn tới trang đăng nhập của bạn
    exit();
}
$order_items_info = []; // Biến để lưu thông tin các sản phẩm trong đơn hàng
$order_info = null;     // Biến để lưu thông tin chung của đơn hàng
$error_message = '';    // Biến để lưu thông báo lỗi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['order_id']) && !empty($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']); // Chuyển đổi sang số nguyên

        // Bước 1: Lấy thông tin chung của đơn hàng
        $stmt_order = $conn->prepare("
            SELECT 
                id, order_date, full_name, phone, email, address, city, district 
            FROM orders 
            WHERE id = ?
        ");
        if ($stmt_order === false) {
            $error_message = "Lỗi chuẩn bị truy vấn đơn hàng: " . $conn->error;
        } else {
            $stmt_order->bind_param("i", $order_id);
            $stmt_order->execute();
            $result_order = $stmt_order->get_result();
            if ($result_order->num_rows > 0) {
                $order_info = $result_order->fetch_assoc();
            } else {
                $error_message = "Không tìm thấy đơn hàng với Mã đơn hàng này.";
            }
            $stmt_order->close();
        }

        // Bước 2: Nếu tìm thấy đơn hàng, lấy thông tin chi tiết các sản phẩm trong đơn
        if ($order_info) {
            $stmt_items = $conn->prepare("
                SELECT 
                    oi.product_name, 
                    oi.serial_number, 
                    oi.warranty_expire_date,
                    p.warranty AS product_warranty_duration
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            if ($stmt_items === false) {
                $error_message = "Lỗi chuẩn bị truy vấn sản phẩm đơn hàng: " . $conn->error;
            } else {
                $stmt_items->bind_param("i", $order_id);
                $stmt_items->execute();
                $result_items = $stmt_items->get_result();

                if ($result_items->num_rows > 0) {
                    while ($item = $result_items->fetch_assoc()) {
                        // Tính toán trạng thái bảo hành cho từng sản phẩm
                        $current_date = new DateTime();
                        $expire_date_obj = null;
                        $warranty_status = "Không xác định";

                        if (!empty($item['warranty_expire_date'])) {
                            try {
                                $expire_date_obj = new DateTime($item['warranty_expire_date']);
                                if ($current_date <= $expire_date_obj) {
                                    $warranty_status = "Còn bảo hành";
                                } else {
                                    $warranty_status = "Hết bảo hành";
                                }
                            } catch (Exception $e) {
                                $warranty_status = "Ngày hết hạn không hợp lệ";
                            }
                        } else {
                            // Nếu warranty_expire_date chưa được lưu trong order_items, tính lại dựa trên order_date và product.warranty
                            if (!empty($item['product_warranty_duration']) && $order_info['order_date']) {
                                try {
                                    $order_date_obj = new DateTime($order_info['order_date']);
                                    $warranty_duration_parts = explode(' ', strtolower($item['product_warranty_duration']));
                                    
                                    if (count($warranty_duration_parts) >= 2) {
                                        $value = (int)$warranty_duration_parts[0];
                                        $unit = strtolower($warranty_duration_parts[1]);

                                        switch ($unit) {
                                            case 'tháng':
                                                $order_date_obj->modify("+$value months");
                                                break;
                                            case 'năm':
                                                $order_date_obj->modify("+$value years");
                                                break;
                                            default:
                                                // Đơn vị không hợp lệ, không thể tính
                                                break;
                                        }
                                        $expire_date_obj = $order_date_obj; // Cập nhật ngày hết hạn tính toán
                                        if ($current_date <= $expire_date_obj) {
                                            $warranty_status = "Còn bảo hành";
                                        } else {
                                            $warranty_status = "Hết bảo hành";
                                        }
                                    } else {
                                         $warranty_status = "Định dạng thời hạn bảo hành không hợp lệ";
                                    }
                                } catch (Exception $e) {
                                    $warranty_status = "Lỗi tính toán bảo hành";
                                }
                            } else {
                                $warranty_status = "Không có thông tin bảo hành từ sản phẩm";
                            }
                        }
                        $item['calculated_expire_date'] = $expire_date_obj ? $expire_date_obj->format('d/m/Y') : "Không xác định";
                        $item['warranty_status'] = $warranty_status;
                        $order_items_info[] = $item; // Thêm sản phẩm vào danh sách
                    }
                } else {
                    $error_message = "Đơn hàng này không có sản phẩm nào.";
                }
                $stmt_items->close();
            }
        }
    } else {
        $error_message = "Vui lòng nhập Mã đơn hàng.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra bảo hành sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); /* Nền gradient nhẹ nhàng */
        color: #333;
        margin: 0;
        padding: 0;
        
    }

    .container {
        max-width: 850px; /* Tăng chiều rộng một chút */
        width: 90%; /* Đảm bảo responsive */
        margin: 40px auto;
        background: #fff;
        padding: 40px; /* Tăng padding */
        border-radius: 15px; /* Bo góc nhiều hơn */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08); /* Box shadow mạnh mẽ hơn */
        border: 1px solid #e0e0e0; /* Viền nhẹ */
        overflow: hidden; /* Đảm bảo nội dung không tràn ra ngoài bo góc */
    }

    h1 {
        text-align: center;
        font-size: 2.8rem; /* Tăng kích thước */
        color: #1d3557; /* Sử dụng màu từ gradient làm màu chính */
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
    }

    h1::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: 0;
        transform: translateX(-50%);
        width: 80px; /* Chiều rộng của đường gạch dưới */
        height: 4px;
        background: linear-gradient(90deg, #1d3557 0%, #e63946 100%); /* Gradient cho đường gạch dưới */
        border-radius: 2px;
    }

    .form-group {
        margin-bottom: 25px;
        text-align: center;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        color: #457b9d; /* Màu chữ label dễ nhìn */
        font-size: 1.1rem;
    }

    .form-group input[type="text"] {
        width: 70%;
        max-width: 400px; /* Giới hạn chiều rộng input */
        padding: 14px 18px; /* Tăng padding */
        border: 1px solid #b0c4de; /* Viền input nhẹ nhàng */
        border-radius: 8px; /* Bo góc input */
        font-size: 1.1rem;
        box-sizing: border-box;
        color: #000; /* Chữ nhập màu đen */
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-group input[type="text"]:focus {
        border-color: #457b9d; /* Màu viền khi focus */
        box-shadow: 0 0 0 3px rgba(69, 123, 157, 0.2); /* Hiệu ứng shadow khi focus */
        outline: none;
    }

    .form-group button {
        padding: 14px 30px; /* Tăng padding */
        background: linear-gradient(90deg, #1d3557 0%, #e63946 100%); /* Gradient cho nút */
        color: #fff;
        border: none;
        border-radius: 8px; /* Bo góc nút */
        font-size: 1.2rem;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 10px rgba(29, 53, 87, 0.3); /* Shadow cho nút */
    }

    .form-group button:hover {
        transform: translateY(-2px); /* Hiệu ứng nhấc lên */
        box-shadow: 0 6px 15px rgba(29, 53, 87, 0.4); /* Shadow mạnh hơn khi hover */
        background: linear-gradient(90deg, #e63946 0%, #1d3557 100%); /* Đảo ngược gradient hoặc đổi màu nhẹ */
    }

    .error-message {
        color: #e74c3c;
        text-align: center;
        margin-top: 25px;
        font-size: 1.2rem;
        font-weight: bold;
        padding: 10px 15px;
        background-color: #ffe6e6;
        border: 1px solid #e74c3c;
        border-radius: 8px;
    }

    .order-info, .warranty-result {
        margin-top: 35px; /* Tăng khoảng cách */
        padding-top: 25px;
        border-top: 1px dashed #e0e0e0; /* Đường viền dash để phân tách */
    }

    .order-info h2, .warranty-result h2 {
        font-size: 2rem; /* Tăng kích thước */
        color: #2c3e50;
        text-align: center;
        margin-bottom: 25px;
        position: relative;
    }

    .order-info h2::after, .warranty-result h2::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -5px; /* Điều chỉnh vị trí đường gạch dưới */
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: #457b9d; /* Màu đường gạch dưới cho H2 */
        border-radius: 1.5px;
    }

    .detail-box, .product-item {
        background-color: #fcfcfc; /* Nền trắng sáng hơn */
        border: 1px solid #e9ecef; /* Viền nhẹ */
        border-radius: 10px; /* Bo góc */
        padding: 25px; /* Tăng padding */
        margin-bottom: 20px; /* Tăng khoảng cách giữa các box */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); /* Shadow nhẹ */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .detail-box:hover, .product-item:hover {
        transform: translateY(-3px); /* Hiệu ứng nhấc lên khi hover */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); /* Shadow mạnh hơn khi hover */
    }

    .detail-box p, .product-item p {
        margin-bottom: 8px; /* Giảm khoảng cách giữa các dòng */
        font-size: 1.05rem; /* Tăng kích thước chữ */
        line-height: 1.8;
    }

    .detail-box .label, .product-item .label {
        font-weight: bold;
        color: #3d5a80; /* Màu label đậm hơn */
        display: inline-block;
        min-width: 200px; /* Tăng chiều rộng tối thiểu cho label */
        text-align: left;
    }

    .status-active {
        color: #27ae60; /* Màu xanh lá cho còn bảo hành */
        font-weight: bold;
        background-color: #e6ffee;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
    }

    .status-expired {
        color: #e74c3c; /* Màu đỏ cho hết bảo hành */
        font-weight: bold;
        background-color: #ffebeb;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
    }

    .product-item h3 { 
        color: #1d3557; /* Màu đậm từ gradient */
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1.8rem; /* Tăng kích thước */
        border-bottom: 2px solid #f0f0f0; /* Đường gạch dưới nhẹ */
        padding-bottom: 8px;
    }

    /* Footer adjustments (assuming you have a footer included) */
    footer {
        width: 100%;
        text-align: center;
        padding: 20px 0;
        background-color: #eceff1;
        color: #555;
        margin-top: 50px; /* Khoảng cách từ container đến footer */
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
</style>
</head>
<body>
    <div class="container">
        <h1>Kiểm tra thông tin bảo hành</h1>

        <form action="" method="post">
            <div class="form-group">
                <label for="order_id">Nhập Mã đơn hàng:</label>
                <input type="text" id="order_id" name="order_id" placeholder="Ví dụ: 12345" required>
            </div>
            <div class="form-group">
                <button type="submit">Kiểm tra</button>
            </div>
        </form>

        <?php if (!empty($error_message)) : ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <?php if ($order_info) : // Hiển thị thông tin chung của đơn hàng ?>
            <div class="order-info">
                <h2>Thông tin đơn hàng #<?= htmlspecialchars($order_info['id']) ?></h2>
                <div class="detail-box">
                    <p><span class="label">Ngày đặt hàng:</span> <?= htmlspecialchars(date('d/m/Y', strtotime($order_info['order_date']))) ?></p>
                    <p><span class="label">Họ và tên khách hàng:</span> <?= htmlspecialchars($order_info['full_name']) ?></p>
                    <p><span class="label">Điện thoại:</span> <?= htmlspecialchars($order_info['phone']) ?></p>
                    <p><span class="label">Email:</span> <?= htmlspecialchars($order_info['email']) ?></p>
                    <p><span class="label">Địa chỉ:</span> 
                        <?= htmlspecialchars($order_info['address']) ?>, 
                        <?= htmlspecialchars($order_info['district']) ?>, 
                        <?= htmlspecialchars($order_info['city']) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($order_items_info)) : // Hiển thị thông tin từng sản phẩm trong đơn hàng ?>
            <div class="warranty-result">
                <h2>Chi tiết sản phẩm và bảo hành</h2>
                <?php foreach ($order_items_info as $item) : ?>
                    <div class="product-item">
                        <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                        <p><span class="label">Serial Number:</span> <?= !empty($item['serial_number']) ? htmlspecialchars($item['serial_number']) : "Không có SN" ?></p>
                        <p><span class="label">Thời hạn bảo hành:</span> 
                            <?= !empty($item['product_warranty_duration']) ? htmlspecialchars($item['product_warranty_duration']) : "Không áp dụng" ?>
                        </p>
                        <p><span class="label">Ngày hết hạn bảo hành:</span> 
                            <?= htmlspecialchars($item['calculated_expire_date']) ?>
                        </p>
                        <p><span class="label">Trạng thái bảo hành:</span> 
                            <span class="<?= ($item['warranty_status'] == 'Còn bảo hành') ? 'status-active' : 'status-expired' ?>">
                                <?= htmlspecialchars($item['warranty_status']) ?>
                            </span>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($order_info && empty($order_items_info)) : ?>
            <p class="error-message">Đơn hàng này không có sản phẩm nào được ghi nhận.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>