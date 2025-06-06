<?php
include '../connect.php'; // Kết nối cơ sở dữ liệu
include '../includes/header.php'; // Header

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo '<p style="text-align: center; color: red;">Vui lòng đăng nhập để tra cứu đơn hàng.</p>';
    exit;
}

$user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session

// Truy vấn danh sách đơn hàng của người dùng
$sqlOrders = "SELECT * FROM orders WHERE user_id = ?";
$stmtOrders = $conn->prepare($sqlOrders);
$stmtOrders->bind_param("i", $user_id);
$stmtOrders->execute();
$resultOrders = $stmtOrders->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu đơn hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
</head>
<body>
<main class="order-main">
    <div class="order-container">
        <h1>Danh sách đơn hàng của bạn</h1>
        <?php if ($resultOrders->num_rows > 0): ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Phương thức thanh toán</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $resultOrders->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                            <td><?= number_format($order['final_total'], 0, ',', '.') ?> đ</td>
                            <td><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td>
                                <a href="chi-tiet-don-hang.php?order_id=<?= $order['id'] ?>" class="btn-detail">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">Bạn chưa có đơn hàng nào.</p>
        <?php endif; ?>
    </div>
</main>
<style>
.order-container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-family: "Segoe UI", Tahoma, sans-serif; /* Thêm font chữ */
    text-align: center; /* Căn giữa nội dung theo chiều ngang */
    display: flex; /* Sử dụng flexbox */
    flex-direction: column; /* Căn nội dung theo chiều dọc */
    justify-content: center; /* Căn giữa nội dung theo chiều dọc */
    align-items: center; /* Căn giữa nội dung theo chiều ngang */
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: "Segoe UI", Tahoma, sans-serif; /* Thêm font chữ */
}

.order-table th, .order-table td {
    padding: 10px;
    text-align: center; /* Căn giữa nội dung trong bảng */
    border: 1px solid #ddd;
}

.order-table th {
    background-color: #f9f9f9;
    font-weight: bold;
}

.btn-detail {
    display: inline-block;
    padding: 7px 10px;
    background: linear-gradient(90deg, #43a047 0%, #e63946 100%);
    color: #fff;
    text-decoration: none;
    border-radius: 15px;
    transition: background-color 0.3s ease;
    font-size: 0.9em;
    font-weight: 500;
    font-family: "Segoe UI", Tahoma, sans-serif; /* Thêm font chữ */
}

.btn-detail:hover {
    background-color: #0056b3;
}
</style>
<?php include '../includes/footer.php'; ?>
</body>
</html>