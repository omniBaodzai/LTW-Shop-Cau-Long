<?php
include '../connect.php'; // Kết nối cơ sở dữ liệu
include '../includes/header.php'; // Header luôn hiển thị
?>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">

<!-- Wrapper bao trọn nội dung + footer -->
<div class="page-wrapper">

    <!-- Nội dung chính -->
    <main class="order-main content-wrap">
        <div class="order-container">
            <?php
            if (!isset($_SESSION['user_id'])) {
                echo '
                <div style="
                    max-width: 500px;
                    margin: 50px auto;
                    padding: 20px;
                    background-color: #fff3f3;
                    border: 1px solid #ffcccc;
                    border-radius: 10px;
                    text-align: center;
                    font-family: Segoe UI, Tahoma, sans-serif;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                ">
                    <h2 style="color:rgb(223, 127, 116);"> Vui lòng đăng nhập</h2>
                    <p style="color: #444; font-size: 16px;">Bạn cần đăng nhập để tra cứu đơn hàng của mình.</p>
                    <p>Bạn sẽ được chuyển về <a href="../index.php" style="color:rgb(206, 145, 31); text-decoration: none;">trang chủ</a> sau 3 giây.</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = "../index.php";
                    }, 3000);
                </script>';

            } else {
                $user_id = $_SESSION['user_id'];
                $sqlOrders = "SELECT * FROM orders WHERE user_id = ?";
                $stmtOrders = $conn->prepare($sqlOrders);
                $stmtOrders->bind_param("i", $user_id);
                $stmtOrders->execute();
                $resultOrders = $stmtOrders->get_result();

                echo '<h1>Danh sách đơn hàng của bạn</h1>';

                if ($resultOrders->num_rows > 0) {
                    echo '<table class="order-table">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Phương thức thanh toán</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>';

                    while ($order = $resultOrders->fetch_assoc()) {
                        echo '<tr>
                            <td>' . htmlspecialchars($order['id']) . '</td>
                            <td>' . htmlspecialchars($order['order_date']) . '</td>
                            <td>' . number_format($order['final_total'], 0, ',', '.') . ' đ</td>
                            <td>' . htmlspecialchars($order['payment_method']) . '</td>
                            <td><a href="chi-tiet-don-hang.php?order_id=' . $order['id'] . '" class="btn-detail">Xem chi tiết</a></td>
                        </tr>';
                    }

                    echo '</tbody></table>';
                } else {
                    echo '<p style="text-align: center;">Bạn chưa có đơn hàng nào.</p>';
                }
            }
            ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</div> <!-- Kết thúc .page-wrapper -->

<style>
/* Bố cục để đẩy footer xuống đáy */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

.page-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.content-wrap {
    flex: 1;
}

/* Giao diện */
.order-container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-family: "Segoe UI", Tahoma, sans-serif;
    text-align: center;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: "Segoe UI", Tahoma, sans-serif;
}

.order-table th, .order-table td {
    padding: 10px;
    text-align: center;
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
}

.btn-detail:hover {
    background-color: #0056b3;
}

/* Footer */
.site-footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 15px 0;
}
</style>
