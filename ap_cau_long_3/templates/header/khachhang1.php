<?php
session_start(); // Bắt đầu session để kiểm tra trạng thái đăng nhập

// Bảo vệ trang: Nếu người dùng chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Chuyển hướng về trang đăng nhập
    exit(); // Dừng thực thi script sau khi chuyển hướng
}

// Lấy thông tin người dùng từ session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Khách hàng'; // Tên người dùng mặc định nếu không có trong session

// --- BẮT ĐẦU PHẦN PHP CẦN BỔ SUNG CHO DỮ LIỆU ĐỘNG ---
// Trong một ứng dụng thực tế, bạn sẽ kết nối cơ sở dữ liệu ở đây
// include 'config/database.php'; // File chứa thông tin kết nối DB

// Lấy thông tin cá nhân của người dùng từ DB (ví dụ giả lập)
// $user_info = get_user_details_from_db($user_id, $conn); // Hàm này sẽ truy vấn DB
$user_info = [
    'full_name' => $username, // Sử dụng tên từ session làm tên đầy đủ mặc định
    'email' => 'nguyenvana@example.com',
    'phone' => '0912 345 678',
    'address' => '123 Đường ABC, Phường XYZ, Quận 1, TP.HCM',
    'city' => 'TP.HCM',
    'district' => 'Quận 1',
];

// Lấy lịch sử đơn hàng từ DB (ví dụ giả lập)
// $orders = get_user_orders_from_db($user_id, $conn);
$orders = [
    ['order_id' => '#DH12345', 'order_date' => '20/05/2025', 'total_amount' => 4930000, 'status' => 'Đã giao'],
    ['order_id' => '#DH12346', 'order_date' => '15/05/2025', 'total_amount' => 1500000, 'status' => 'Đang xử lý'],
    ['order_id' => '#DH12347', 'order_date' => '10/05/2025', 'total_amount' => 800000, 'status' => 'Đã hủy'],
];

// Lấy sản phẩm yêu thích từ DB (ví dụ giả lập)
// $favorite_products = get_user_favorites_from_db($user_id, $conn);
$favorite_products = [
    ['id' => 'prod_qa1', 'name' => 'Quần áo cầu lông Victor V-200', 'price' => 650000, 'image' => 'path/to/sanpham3.jpg'],
    ['id' => 'prod_bl1', 'name' => 'Balo cầu lông Yonex BAG100', 'price' => 890000, 'image' => 'path/to/sanpham4.jpg'],
];

// Lấy đánh giá sản phẩm từ DB (ví dụ giả lập)
// $reviews = get_user_reviews_from_db($user_id, $conn);
$reviews = [
    ['product_name' => 'Vợt cầu lông Yonex Astrox 88D', 'rating' => 5, 'comment' => 'Vợt rất tốt, cảm giác đánh cầu chắc chắn và uy lực. Giao hàng nhanh chóng.', 'date' => '22/05/2025'],
    ['product_name' => 'Giày cầu lông Lining AYAP001', 'rating' => 4, 'comment' => 'Giày êm chân, bám sân tốt nhưng màu sắc hơi nhạt so với hình ảnh.', 'date' => '18/05/2025'],
];
// --- KẾT THÚC PHẦN PHP CẦN BỔ SUNG CHO DỮ LIỆU ĐỘNG ---

// Xử lý cập nhật thông tin cá nhân (ví dụ)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    // Lấy dữ liệu từ form và làm sạch
    $new_full_name = htmlspecialchars(trim($_POST['new_fullName'] ?? ''));
    $new_email = htmlspecialchars(trim($_POST['new_email'] ?? ''));
    $new_phone = htmlspecialchars(trim($_POST['new_phone'] ?? ''));
    $new_address = htmlspecialchars(trim($_POST['new_address'] ?? ''));
    $new_city = htmlspecialchars(trim($_POST['new_city'] ?? ''));
    $new_district = htmlspecialchars(trim($_POST['new_district'] ?? ''));

    // Cập nhật vào DB (chức năng giả lập)
    // update_user_profile_in_db($user_id, $new_full_name, $new_email, ...);
    $user_info['full_name'] = $new_full_name;
    $user_info['email'] = $new_email;
    $user_info['phone'] = $new_phone;
    $user_info['address'] = $new_address;
    $user_info['city'] = $new_city;
    $user_info['district'] = $new_district;
    $_SESSION['username'] = $new_full_name; // Cập nhật tên trong session nếu tên đầy đủ thay đổi
    $profile_update_message = "Thông tin cá nhân đã được cập nhật thành công!";
}

// Xử lý đổi mật khẩu (ví dụ)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = $_POST['currentPassword'] ?? '';
    $new_password = $_POST['newPassword'] ?? '';
    $confirm_new_password = $_POST['confirmNewPassword'] ?? '';

    // Kiểm tra mật khẩu hiện tại và các điều kiện khác
    // Đây là nơi bạn sẽ verify mật khẩu cũ với hash trong DB
    // if (verify_password_from_db($user_id, $current_password, $conn)) {
    if ($current_password === "password123") { // Ví dụ giả lập: mật khẩu cũ là "password123"
        if ($new_password === $confirm_new_password) {
            if (strlen($new_password) >= 6) {
                // Hash mật khẩu mới và cập nhật vào DB
                // $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                // update_user_password_in_db($user_id, $hashed_new_password, $conn);
                $password_change_message = "Mật khẩu đã được thay đổi thành công!";
            } else {
                $password_change_error = "Mật khẩu mới phải có ít nhất 6 ký tự.";
            }
        } else {
            $password_change_error = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
        }
    } else {
        $password_change_error = "Mật khẩu hiện tại không đúng.";
    }
}

// Xử lý đăng xuất
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy(); // Hủy tất cả dữ liệu session
    header('Location: login.php'); // Chuyển hướng về trang đăng nhập
    exit();
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân của bạn - <?php echo htmlspecialchars($username); ?></title>
    <style>
        /* CSS tương tự như file HTML ban đầu */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .profile-container { max-width: 1000px; margin: 20px auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: flex; }
        .sidebar { width: 250px; padding-right: 20px; border-right: 1px solid #eee; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 10px; }
        .sidebar ul li a { display: block; padding: 10px 15px; text-decoration: none; color: #333; border-radius: 5px; transition: background-color 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #007bff; color: white; }
        .profile-content { flex-grow: 1; padding-left: 30px; }
        h1 { margin-top: 0; color: #333; }
        .section-title { font-size: 1.3em; margin-bottom: 20px; color: #007bff; border-bottom: 1px solid #eee; padding-bottom: 10px; }

        /* General Info */
        .info-group { margin-bottom: 15px; }
        .info-group label { font-weight: bold; color: #555; display: inline-block; width: 150px; }
        .info-group span { color: #333; }
        .edit-button { background-color: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .save-button { background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 15px; }


        /* Order History */
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th, .order-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .order-table th { background-color: #f2f2f2; }
        .order-status { font-weight: bold; }
        .status-pending { color: orange; }
        .status-completed { color: green; }
        .status-cancelled { color: red; }

        /* Favorite Products */
        .favorite-products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .favorite-product-card { border: 1px solid #eee; padding: 15px; text-align: center; }
        .favorite-product-card img { max-width: 100%; height: 150px; object-fit: cover; margin-bottom: 10px; }
        .favorite-product-card h3 { font-size: 1.1em; margin-bottom: 5px; }
        .favorite-product-card p { color: #555; }
        .remove-favorite-button { background-color: #dc3545; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 0.9em; margin-top: 10px; }

        /* Product Reviews */
        .review-item { border: 1px solid #eee; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .review-item h4 { margin-top: 0; margin-bottom: 5px; color: #007bff; }
        .review-item .rating { color: #ffc107; margin-bottom: 5px; }
        .review-item p { margin-bottom: 5px; }
        .review-item .date { font-size: 0.9em; color: #888; }
        .edit-review-button, .delete-review-button { background-color: #ffc107; color: #333; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 0.9em; margin-right: 10px; }
        .delete-review-button { background-color: #dc3545; color: white; }

        /* Change Password */
        .password-form .form-group { margin-bottom: 15px; }
        .password-form label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .password-form input { width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .password-form button { background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 15px; }

        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="sidebar">
            <ul>
                <li><a href="#general-info" class="active">Thông tin chung</a></li>
                <li><a href="#order-history">Lịch sử đơn hàng</a></li>
                <li><a href="#favorite-products">Sản phẩm yêu thích</a></li>
                <li><a href="#product-reviews">Đánh giá sản phẩm</a></li>
                <li><a href="#change-password">Đổi mật khẩu</a></li>
                <li><a href="profile.php?action=logout">Đăng xuất</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <div id="general-info">
                <h1>Chào mừng, <?php echo htmlspecialchars($username); ?>!</h1>
                <h2 class="section-title">Thông tin cá nhân</h2>

                <?php if (isset($profile_update_message)) { echo '<p class="message success">' . htmlspecialchars($profile_update_message) . '</p>'; } ?>

                <form action="profile.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label for="new_fullName">Họ và tên:</label>
                        <input type="text" id="new_fullName" name="new_fullName" value="<?php echo htmlspecialchars($user_info['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_email">Email:</label>
                        <input type="email" id="new_email" name="new_email" value="<?php echo htmlspecialchars($user_info['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_phone">Số điện thoại:</label>
                        <input type="tel" id="new_phone" name="new_phone" value="<?php echo htmlspecialchars($user_info['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_address">Địa chỉ:</label>
                        <textarea id="new_address" name="new_address" required><?php echo htmlspecialchars($user_info['address']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="new_city">Tỉnh/Thành phố:</label>
                        <input type="text" id="new_city" name="new_city" value="<?php echo htmlspecialchars($user_info['city']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_district">Quận/Huyện:</label>
                        <input type="text" id="new_district" name="new_district" value="<?php echo htmlspecialchars($user_info['district']); ?>" required>
                    </div>
                    <button type="submit" class="save-button">Lưu thay đổi</button>
                </form>
            </div>

            <div id="order-history" style="display: none;">
                <h1>Lịch sử đơn hàng</h1>
                <h2 class="section-title">Các đơn hàng của bạn</h2>
                <?php if (!empty($orders)) { ?>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                    <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</td>
                                    <td class="order-status <?php
                                        if ($order['status'] == 'Đã giao') echo 'status-completed';
                                        elseif ($order['status'] == 'Đang xử lý') echo 'status-pending';
                                        else echo 'status-cancelled';
                                    ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </td>
                                    <td><button class="edit-button">Xem chi tiết</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p>Bạn chưa có đơn hàng nào.</p>
                <?php } ?>
            </div>

            <div id="favorite-products" style="display: none;">
                <h1>Sản phẩm yêu thích</h1>
                <h2 class="section-title">Danh sách sản phẩm yêu thích của bạn</h2>
                <?php if (!empty($favorite_products)) { ?>
                    <div class="favorite-products-grid">
                        <?php foreach ($favorite_products as $product) { ?>
                            <div class="favorite-product-card">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                                <button class="remove-favorite-button">Xóa khỏi yêu thích</button>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p>Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>
                <?php } ?>
            </div>

            <div id="product-reviews" style="display: none;">
                <h1>Đánh giá sản phẩm</h1>
                <h2 class="section-title">Các đánh giá của bạn</h2>
                <?php if (!empty($reviews)) { ?>
                    <?php foreach ($reviews as $review) { ?>
                        <div class="review-item">
                            <h4><?php echo htmlspecialchars($review['product_name']); ?></h4>
                            <div class="rating">
                                <?php for ($i = 0; $i < $review['rating']; $i++) echo '★'; ?>
                                <?php for ($i = $review['rating']; $i < 5; $i++) echo '☆'; ?>
                            </div>
                            <p><?php echo htmlspecialchars($review['comment']); ?></p>
                            <span class="date">Ngày đánh giá: <?php echo htmlspecialchars($review['date']); ?></span>
                            <br>
                            <button class="edit-review-button">Chỉnh sửa</button>
                            <button class="delete-review-button">Xóa</button>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>Bạn chưa có đánh giá nào.</p>
                <?php } ?>
            </div>

            <div id="change-password" style="display: none;">
                <h1>Đổi mật khẩu</h1>
                <h2 class="section-title">Đổi mật khẩu của bạn</h2>
                <?php if (isset($password_change_error)) { echo '<p class="message error">' . htmlspecialchars($password_change_error) . '</p>'; } ?>
                <?php if (isset($password_change_message)) { echo '<p class="message success">' . htmlspecialchars($password_change_message) . '</p>'; } ?>
                <form class="password-form" action="profile.php" method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="form-group">
                        <label for="currentPassword">Mật khẩu hiện tại:</label>
                        <input type="password" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">Mật khẩu mới:</label>
                        <input type="password" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmNewPassword">Xác nhận mật khẩu mới:</label>
                        <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
                    </div>
                    <button type="submit">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // JavaScript để chuyển đổi giữa các tab sidebar (giữ nguyên từ HTML)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
            const profileSections = document.querySelectorAll('.profile-content > div');

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Đảm bảo không xử lý link Đăng xuất bằng JavaScript
                    if (this.getAttribute('href') === 'profile.php?action=logout') {
                        return true; // Cho phép trình duyệt thực hiện hành động href
                    }

                    e.preventDefault();

                    // Xóa class 'active' khỏi tất cả các liên kết
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    // Thêm class 'active' vào liên kết được nhấp
                    this.classList.add('active');

                    // Ẩn tất cả các phần
                    profileSections.forEach(section => section.style.display = 'none');

                    // Hiển thị phần mục tiêu
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.style.display = 'block';
                    }
                });
            });

            // Lấy hash từ URL (ví dụ: profile.php#order-history) và hiển thị tab tương ứng
            const hash = window.location.hash;
            if (hash) {
                const targetLink = document.querySelector(`.sidebar ul li a[href="${hash}"]`);
                if (targetLink) {
                    targetLink.click();
                }
            } else {
                // Mặc định hiển thị tab thông tin chung nếu không có hash
                const defaultLink = document.querySelector('.sidebar ul li a.active');
                if (defaultLink) {
                    defaultLink.click();
                } else if (sidebarLinks.length > 0) {
                    sidebarLinks[0].click();
                }
            }
        });
    </script>
</body>
</html>