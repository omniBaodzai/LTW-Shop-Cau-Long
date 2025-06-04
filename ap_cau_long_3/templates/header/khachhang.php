<?php
session_start();
include_once __DIR__ . '/../../connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Thực hiện truy vấn lấy thông tin user
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if (!$user_result) {
    // Nếu truy vấn lỗi hoặc không trả về kết quả
    session_destroy();
    header("Location: dangnhap.php");
    exit;
}

$user = $user_result->fetch_assoc();

$stmt->close();

if (!$user) {
    // Nếu user không tồn tại
    session_destroy();
    header("Location: ../../dangnhap.php");
    exit;
}
$error_msg = '';
$info_msg = '';
// Tiếp tục các logic khác...

// Đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($current) || empty($new) || empty($confirm)) {
        $error_msg = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($new !== $confirm) {
        $error_msg = "Mật khẩu xác nhận không khớp.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!password_verify($current, $result['password'])) {
            $error_msg = "Mật khẩu hiện tại không đúng.";
        } else {
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hash, $user_id);
            if ($stmt->execute()) {
                $info_msg = "Đổi mật khẩu thành công!";
            } else {
                $error_msg = "Lỗi khi cập nhật mật khẩu.";
            }
            $stmt->close();
        }
    }
}

// Lấy lịch sử đơn hàng
$stmt = $conn->prepare("SELECT id, full_name, order_date, final_total FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản của bạn - Cầu Lông Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
    background: linear-gradient(to right, #f1f3f5, #fefefe);
    font-family: 'Segoe UI', sans-serif;
}
.card {
    border-radius: 1rem;
    background-color: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.section-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: #00796b;
    margin-bottom: 1rem;
}
.profile-box {
    padding: 2rem;
    border-radius: 1rem;
}
.logout-btn {
    text-decoration: none;
    color: #d32f2f;
    font-weight: bold;
}
.logout-btn:hover {
    text-decoration: underline;
}
.badge-bg {
    background-color: #fefefe;
    color: #0277bd;
}
.table thead {
    background-color: #00796b;
    color: white;
}
.profile-container {
    background: linear-gradient(to right, #f1f3f5, #fefefe);
    min-height: 100vh;
}
.nav-link {
    color: #333;
}
.nav-link.active {
    font-weight: bold;
    color: #00796b !important;
}

    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../header.php'; ?>
    <div class="container-fluid profile-container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card p-3">
                    <ul class="nav flex-column">
                        <a class="nav-link" href="#general-info">👤 Thông tin chung</a>

                        <li class="nav-item"><a class="nav-link" href="#change-password">🔒 Đổi mật khẩu</a></li>
                        <li class="nav-item"><a class="nav-link" href="#order-history">🧾 Lịch sử đơn hàng</a></li>
                        <a href="#" id="logout-link" class="nav-link text-danger">🚪 Đăng xuất</a>



                    </ul>
                </div>
            </div>

            <!-- Profile content -->
            <div class="col-md-9 profile-content">
                <!-- Thông tin cá nhân -->
                <div id="general-info" class="card mb-4 p-4">
                    <h2 class="section-title">👤 Thông tin cá nhân</h2>
                    <p><strong>Tên người dùng:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'Chưa có') ?></p>
                    <p><strong>Thành viên từ:</strong> <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                    <p><span class="badge badge-bg">Khách hàng thân thiết 🥇</span></p>
                </div>

                <!-- Đổi mật khẩu -->
                <div id="change-password" class="card mb-4 p-4">
                    <h2 class="section-title">🔒 Đổi mật khẩu</h2>
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger"><?= $error_msg ?></div>
                    <?php endif; ?>
                    <?php if ($info_msg): ?>
                        <div class="alert alert-success"><?= $info_msg ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">💾 Cập nhật mật khẩu</button>
                    </form>
                </div>

                <!-- Lịch sử đơn hàng -->
                <div id="order-history" class="card p-4">
                    <h2 class="section-title">🧾 Lịch sử đơn hàng</h2>
                    <?php if ($orders->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Người nhận</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td><?= htmlspecialchars($order['full_name']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                        <td><?= number_format($order['final_total'], 0, ',', '.') ?>đ</td>
                                        <td><a href="/ap_cau_long/templates/header/xacnhan.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>📦 Bạn chưa có đơn hàng nào. Hãy sản phẩm cầu lông bạn thích và bắt đầu trận đấu đầu tiên của bạn!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const navLinks = document.querySelectorAll(".nav-link");
        const sections = document.querySelectorAll(".profile-content > div.card");

        // Ẩn tất cả section
        function hideAllSections() {
            sections.forEach(section => {
                section.style.display = "none";
            });
        }

        // Hiển thị section theo id
        function showSectionById(id) {
            const target = document.querySelector(id);
            if (target) {
                target.style.display = "block";
            }
        }

        // Xử lý sự kiện click vào tab
        navLinks.forEach(link => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                const targetId = this.getAttribute("href");

                // Xoá active khỏi tất cả nav-link
                navLinks.forEach(l => l.classList.remove("active"));
                this.classList.add("active");

                // Ẩn và hiện section tương ứng
                hideAllSections();
                showSectionById(targetId);
            });
        });

        // Khởi tạo: ẩn tất cả và hiển thị tab đầu tiên
        hideAllSections();
        showSectionById("#general-info");
    });
</script>
<script>
document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();

    fetch('/ap_cau_long/templates/header/dangxuat.php')
    .then(response => {
        if (response.ok) {
            // Logout thành công, chuyển về trang chủ
            window.location.href = '/ap_cau_long/caulongvn.php';
        } else {
            alert('Đăng xuất thất bại.');
        }
    })
    .catch(error => {
        console.error('Lỗi khi đăng xuất:', error);
        alert('Lỗi hệ thống khi đăng xuất.');
    });
});

</script>




<?php include_once __DIR__ . '/../footer.php'; ?>

<script src="/ap_cau_long/js/main.js"></script>
</body>

</html>
