<?php
include '../connect.php'; // Kết nối cơ sở dữ liệu

// Lấy order_id từ URL
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Đơn hàng không hợp lệ.");
}

$order_id = intval($_GET['order_id']);

// Truy vấn thông tin đơn hàng
$stmt_order = $conn->prepare("
    SELECT 
        full_name, phone, email, address, city, district, payment_method, 
        total_price, shipping_fee, final_total, order_date 
    FROM orders 
    WHERE id = ?
");
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 0) {
    die("Không tìm thấy đơn hàng.");
}

$order = $result_order->fetch_assoc();

// Truy vấn thông tin các sản phẩm trong đơn hàng
$stmt_items = $conn->prepare("
    SELECT 
        product_name, price, quantity 
    FROM order_items 
    WHERE order_id = ?
");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= htmlspecialchars($order_id) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php
include '../includes/header.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 12px 25px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            font-size: 2.4rem;
            color:rgb(41, 53, 121);
            margin-bottom: 25px;
            border-bottom: 3px solid rgb(41, 53, 121);
            padding-bottom: 10px;
        }
        h2 {
            margin-top: 40px;
            font-size: 1.4rem;
            color: #34495e;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            min-width: 140px;
        }
        ul {
            list-style-type: none;
            margin-top: 15px;
        }
        ul li {
            margin-bottom: 10px;
            font-size: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        thead {
            background-color: #ecf0f1;
        }
        th, td {
            padding: 15px;
            text-align: left;
            font-size: 1rem;
            border-bottom: 1px solid #ddd;
        }
        tfoot th {
            background-color: #fafafa;
            font-weight: bold;
            font-size: 1.1rem;
            color: #222;
            text-align: right;
            border-top: 2px solid #ccc;
        }
        tfoot tr:last-child th {
            color: rgb(177, 56, 0);
            font-size: 1.2rem;
        }
        .thanks {
            margin-top: 45px;
            text-align: center;
            font-weight: bold;
            font-size: 1.3rem;
            color: #2980b9;
        }
    </style>
</head>
<body>
    

    <div class="container">
        <h1>Chi tiết đơn hàng</h1>

        <p><span class="label">Mã đơn hàng:</span> <?= htmlspecialchars($order_id) ?></p>
        <p><span class="label">Ngày đặt:</span> <?= htmlspecialchars($order['order_date']) ?></p>

        <h2>Thông tin khách hàng</h2>
        <ul>
            <li><span class="label">Họ và tên:</span> <?= htmlspecialchars($order['full_name']) ?></li>
            <li><span class="label">Điện thoại:</span> <?= htmlspecialchars($order['phone']) ?></li>
            <li><span class="label">Email:</span> <?= htmlspecialchars($order['email']) ?></li>
            <li><span class="label">Địa chỉ:</span> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['district']) ?>, <?= htmlspecialchars($order['city']) ?></li>
            <li><span class="label">Phương thức thanh toán:</span> <?= htmlspecialchars($order['payment_method']) ?></li>
        </ul>

        <h2>Chi tiết đơn hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá (VNĐ)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (VNĐ)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $result_items->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right;">Tổng tiền hàng:</th>
                    <th><?= number_format($order['total_price'], 0, ',', '.') ?> VNĐ</th>
                </tr>
                <tr>
                    <th colspan="3" style="text-align:right;">Phí vận chuyển:</th>
                    <th><?= number_format($order['shipping_fee'], 0, ',', '.') ?> VNĐ</th>
                </tr>
                <tr>
                    <th colspan="3" style="text-align:right;">Tổng thanh toán:</th>
                    <th><?= number_format($order['final_total'], 0, ',', '.') ?> VNĐ</th>
                </tr>
            </tfoot>
        </table>

        <p class="thanks">Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi!</p>
    </div>

<?php include '../includes/footer.php'; ?>
</body>
</html>

<?php
$stmt_order->close();
$stmt_items->close();
$conn->close();
?>