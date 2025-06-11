<?php
include '../includes/header.php';
include '../connect.php'; // Kết nối cơ sở dữ liệu

// Khởi tạo các biến với giá trị mặc định để tránh lỗi undefined variable
$user_name = '';
$user_email = '';
$user_phone = '';
$user_address = '';
$user_city = '';
$user_district = '';
$user_created_at = '';
$profile_loaded = false; // Biến cờ để kiểm tra xem thông tin người dùng đã được tải thành công chưa

// Kiểm tra xem người dùng đã đăng nhập hay chưa
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($user_id > 0) {
    // Truy vấn thông tin người dùng từ bảng `users`
    $stmt = $conn->prepare("SELECT name, email, phone, address, city, district, created_at FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_name = htmlspecialchars($user['name']);
            $user_email = htmlspecialchars($user['email']);
            $user_phone = htmlspecialchars($user['phone']);
            $user_address = htmlspecialchars($user['address']);
            $user_city = htmlspecialchars($user['city']);
            $user_district = htmlspecialchars($user['district']);
            $user_created_at = htmlspecialchars($user['created_at']);
            $profile_loaded = true;
        } else {
            echo "<p class='error-msg'>Không tìm thấy thông tin người dùng.</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='error-msg'>Lỗi chuẩn bị truy vấn thông tin người dùng: " . $conn->error . "</p>";
    }
} else {
    echo "<p class='error-msg'>Bạn chưa đăng nhập. Vui lòng đăng nhập để xem thông tin cá nhân.</p>";
    exit(); // Thoát nếu chưa đăng nhập
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $fullname = htmlspecialchars(trim($_POST['fullname']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));
        $city = htmlspecialchars(trim($_POST['city']));
        $district = htmlspecialchars(trim($_POST['district']));

        if (!empty($fullname) && !empty($phone) && !empty($address) && !empty($city) && !empty($district)) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, city = ?, district = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("sssssi", $fullname, $phone, $address, $city, $district, $user_id);

                if ($stmt->execute()) {
                    // Cập nhật lại các biến hiển thị sau khi update thành công
                    $user_name = $fullname;
                    $user_phone = $phone;
                    $user_address = $address;
                    $user_city = $city;
                    $user_district = $district;
                    echo "<p class='success-msg'>Thông tin đã được cập nhật thành công!</p>";
                } else {
                    echo "<p class='error-msg'>Có lỗi xảy ra khi cập nhật thông tin: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                 echo "<p class='error-msg'>Lỗi chuẩn bị truy vấn cập nhật hồ sơ: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='error-msg'>Vui lòng điền đầy đủ thông tin để cập nhật hồ sơ.</p>";
        }
    }

    // Xử lý đổi mật khẩu
    if ($_POST['action'] === 'change_password') {
        $old_password = htmlspecialchars(trim($_POST['old-password']));
        $new_password = htmlspecialchars(trim($_POST['new-password']));
        $confirm_password = htmlspecialchars(trim($_POST['confirm-password']));

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            echo "<p class='error-msg'>Vui lòng điền đầy đủ các trường mật khẩu.</p>";
        } elseif ($new_password !== $confirm_password) {
            echo "<p class='error-msg'>Mật khẩu mới và xác nhận mật khẩu không khớp.</p>";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user_db = $result->fetch_assoc();
                    if (password_verify($old_password, $user_db['password'])) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                        if ($stmt_update) {
                            $stmt_update->bind_param("si", $hashed_password, $user_id);

                            if ($stmt_update->execute()) {
                                echo "<p class='success-msg'>Mật khẩu đã được thay đổi thành công!</p>";
                            } else {
                                echo "<p class='error-msg'>Có lỗi xảy ra khi đổi mật khẩu: " . $stmt_update->error . "</p>";
                            }
                            $stmt_update->close();
                        } else {
                            echo "<p class='error-msg'>Lỗi chuẩn bị truy vấn cập nhật mật khẩu: " . $conn->error . "</p>";
                        }
                    } else {
                        echo "<p class='error-msg'>Mật khẩu hiện tại không đúng.</p>";
                    }
                } else {
                    echo "<p class='error-msg'>Không tìm thấy thông tin người dùng.</p>";
                }
                $stmt->close();
            } else {
                echo "<p class='error-msg'>Lỗi chuẩn bị truy vấn mật khẩu cũ: " . $conn->error . "</p>";
            }
        }
    }
}

$conn->close();
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
    /* Tổng quan */
    

    .member-main {
        flex-grow: 1; /* Để main chiếm hết chiều cao còn lại */
        display: flex;
        justify-content: center;
        align-items: flex-start; /* Căn trên cùng */
        padding: 20px;
    }

    /* Khung chính */
    .member-container {
        display: flex;
        gap: 25px; /* Khoảng cách giữa sidebar và content */
        width: 100%;
        max-width: 1200px; /* Tăng giới hạn chiều rộng tổng thể */
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 25px;
        flex-wrap: wrap; /* Cho phép wrap trên màn hình nhỏ */
    }

    /* Sidebar Tabs */
    .member-sidebar {
        flex: 0 0 280px; /* Chiều rộng cố định cho sidebar */
        background: linear-gradient(180deg, #f8faff 0%, #eef3f7 100%); /* Gradient cho sidebar */
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
    }

    .member-tabs {
        display: flex;
        flex-direction: column;
        gap: 10px; /* Khoảng cách giữa các nút tab */
    }

    .tab-btn {
        padding: 15px 20px;
        border: none;
        background-color: transparent; /* Nền trong suốt để gradient sidebar lộ ra */
        border-radius: 8px;
        cursor: pointer;
        text-align: left;
        font-size: 17px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        color: #555;
        border-left: 5px solid transparent; /* Viền trái cho hiệu ứng */
    }

    .tab-btn i {
        font-size: 22px;
        color: #457b9d; /* Màu icon mặc định */
    }

    .tab-btn:hover {
        background-color: #eef3f7; /* Nền hover */
        color: #1d3557; /* Màu chữ hover */
        border-left-color: #a8dadc; /* Màu viền trái hover */
    }

    .tab-btn.active {
        background: linear-gradient(90deg, #e0e7ff 0%, #f0f4f8 100%); /* Nền gradient cho tab active */
        color: #1d3557; /* Màu chữ active */
        font-weight: bold;
        border-left-color: #457b9d; /* Viền trái nổi bật */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .tab-btn.active i {
        color: #1d3557; /* Icon active */
    }

    /* Nội dung Tabs */
    .tab-content {
        flex: 1; /* Chiếm hết không gian còn lại */
        padding: 25px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        color: #333;
        /* Rất quan trọng: Đặt tab-content là flex container */
        display: flex;
        flex-direction: column; /* Xếp các tab-pane theo chiều dọc */
        justify-content: flex-start; /* Căn trên cùng các tab-pane */
        align-items: center; /* Căn giữa các tab-pane theo chiều ngang */
        min-height: 400px; /* Đảm bảo có đủ chiều cao để căn giữa hiệu quả */
    }

    .tab-pane {
        display: none;
        width: 100%; /* Đảm bảo tab-pane chiếm toàn bộ chiều rộng của tab-content */
        /* Khi tab-pane active, nó sẽ là flex container để căn giữa nội dung của riêng nó */
        flex-direction: column; /* Nội dung bên trong xếp chồng */
        align-items: center; /* Căn giữa nội dung bên trong theo chiều ngang */
        justify-content: flex-start; /* Căn trên cùng nội dung bên trong */
        /* remove min-height here, let the content define height */
    }

    .tab-pane.active {
        display: flex; /* Hiển thị tab active với flexbox */
    }

    /* Tiêu đề chung */
    h2 {
        font-size: 26px;
        margin-bottom: 25px;
        color: #1d3557;
        text-align: center;
        position: relative;
        padding-bottom: 10px;
    }

    h2::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: 0;
        transform: translateX(-50%);
        width: 70px;
        height: 3px;
        background: linear-gradient(90deg, #457b9d 0%, #a8dadc 100%);
        border-radius: 2px;
    }

    /* Avatar Section */
    .member-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background-color: #fcfdff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        width: 100%; /* Đảm bảo card chiếm đủ chiều rộng */
        max-width: 600px; /* Giới hạn chiều rộng cho card profile */
        margin: 0 auto; /* Căn giữa card profile */
    }

    .member-avatar-wrap {
        position: relative;
        width: 120px; /* Kích thước avatar lớn hơn */
        height: 120px;
        border-radius: 50%;
        background-color: #eee; /* Nền khi chưa có ảnh */
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 0 0 5px rgba(69, 123, 157, 0.2); /* Viền shadow quanh avatar */
    }

    .member-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff; /* Viền trắng bên trong */
    }

    .avatar-edit {
        position: absolute;
        bottom: 0;
        right: 0;
        background: linear-gradient(45deg, #a8dadc, #457b9d); /* Gradient cho nút edit */
        border-radius: 50%;
        padding: 8px;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease;
    }

    .avatar-edit:hover {
        transform: scale(1.1);
    }

    .avatar-edit label {
        color: #fff;
        font-size: 20px;
        margin: 0;
    }

    .member-info {
        text-align: center;
        width: 100%; /* Đảm bảo info chiếm đủ chiều rộng */
    }

    .member-name {
        font-size: 28px;
        color: #1d3557;
        margin-top: 15px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .member-detail-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        text-align: left; /* Căn trái các chi tiết */
        width: 100%;
        max-width: 400px; /* Giới hạn chiều rộng để dễ đọc */
        margin: 0 auto;
    }

    .member-detail {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 16px;
        color: #444;
        background-color: #f9fbfd;
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #eef3f7;
    }

    .member-detail i {
        color: #457b9d; /* Màu icon */
        font-size: 18px;
    }

    .member-detail span {
        flex-grow: 1; /* Cho phép span chiếm hết không gian còn lại */
        color: #333;
    }

    /* Form chỉnh sửa & đổi mật khẩu */
    .edit-card, .change-password-card {
        padding: 25px;
        background-color: #fcfdff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        width: 100%; /* Đảm bảo card chiếm đủ chiều rộng */
        max-width: 500px; /* Giới hạn chiều rộng cho form để nó không quá rộng */
        margin: 0 auto; /* Căn giữa card */
    }

    .edit-form, .change-password-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%; /* Đảm bảo form chiếm đủ chiều rộng của card */
    }

    .edit-group, .change-password-group {
        margin-bottom: 0; /* Đã có gap trên form */
    }

    .edit-group label, .change-password-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-weight: 600;
        color: #3d5a80;
        font-size: 16px;
    }

    .edit-group label i, .change-password-group label i {
        font-size: 18px;
        color: #457b9d;
    }

    .edit-group input, .change-password-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #cddde9;
        border-radius: 8px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s, box-shadow 0.3s;
        color: #000; /* Đảm bảo màu chữ nhập là đen */
    }

    .edit-group input:focus, .change-password-group input:focus {
        border-color: #457b9d;
        box-shadow: 0 0 0 3px rgba(69, 123, 157, 0.15);
        outline: none;
    }

    .edit-actions-row, .change-password-actions {
        display: flex;
        justify-content: center; /* Căn giữa các nút */
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap; /* Cho phép wrap nút trên màn hình nhỏ */
    }

    .edit-btn, .change-btn {
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .edit-btn.save, .change-btn.save {
        background: linear-gradient(90deg, #1d3557 0%, #457b9d 100%);
        color: #fff;
    }

    .edit-btn.save:hover, .change-btn.save:hover {
        background: linear-gradient(90deg, #457b9d 0%, #1d3557 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    .edit-btn.cancel, .change-btn.cancel {
        background-color: #e0e0e0;
        color: #555;
    }

    .edit-btn.cancel:hover, .change-btn.cancel:hover {
        background-color: #cccccc;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    /* Message styles */
    .success-msg {
        color: #28a745; /* Màu xanh lá cây đậm */
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
        width: 100%;
        max-width: 600px; /* Giới hạn chiều rộng thông báo */
        margin: 20px auto; /* Căn giữa thông báo */
    }

    .error-msg {
        color: #dc3545; /* Màu đỏ đậm */
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
        width: 100%;
        max-width: 600px; /* Giới hạn chiều rộng thông báo */
        margin: 20px auto; /* Căn giữa thông báo */
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .member-container {
            flex-direction: column; /* Chuyển sang xếp chồng trên màn hình nhỏ */
            gap: 20px;
            padding: 20px;
        }

        .member-sidebar {
            flex: 0 0 auto; /* Sidebar không cố định chiều rộng */
            width: 100%;
            padding: 15px;
        }

        .member-tabs {
            flex-direction: row; /* Nút tab xếp hàng ngang */
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .tab-btn {
            flex: 1 1 auto; /* Tự động điều chỉnh chiều rộng */
            text-align: center;
            justify-content: center;
            padding: 12px 15px;
        }
        
        .tab-content {
            padding: 20px;
            /* Điều chỉnh lại min-height hoặc căn giữa phù hợp trên mobile */
            min-height: auto; 
        }

        h2 {
            font-size: 24px;
        }

        .member-detail-list {
            max-width: 100%;
        }

        .member-card, .edit-card, .change-password-card {
            max-width: 100%; /* Trên màn hình nhỏ, các card chiếm toàn bộ chiều rộng */
        }
    }

    @media (max-width: 576px) {
        .member-container {
            padding: 15px;
            margin: 20px auto;
        }

        .tab-btn {
            font-size: 15px;
            padding: 10px 12px;
            gap: 8px;
        }

        .tab-btn i {
            font-size: 18px;
        }

        h1 {
            font-size: 2.2rem;
        }

        h2 {
            font-size: 22px;
        }

        .member-avatar-wrap {
            width: 100px;
            height: 100px;
        }

        .edit-btn, .change-btn {
            font-size: 16px;
            padding: 10px 20px;
        }

        .edit-actions-row, .change-password-actions {
            flex-direction: column; /* Stack buttons vertically */
            align-items: center;
        }
    }
</style>

<main class="member-main">
    <div class="member-container">
        <aside class="member-sidebar">
            <nav class="member-tabs">
                <button class="tab-btn active" onclick="showTab('profile')">
                    <i class="ri-user-line"></i> Thông tin thành viên
                </button>
                <button class="tab-btn" onclick="showTab('edit')">
                    <i class="ri-edit-line"></i> Chỉnh sửa thông tin
                </button>
                <button class="tab-btn" onclick="showTab('change-password')">
                    <i class="ri-lock-password-line"></i> Đổi mật khẩu
                </button>
            </nav>
        </aside>

        <section class="tab-content">
            <div class="tab-pane active" id="profile">
                <section class="member-card">
                    <h2>Thông tin thành viên</h2>
                    <div class="member-avatar-wrap">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Avatar" class="member-avatar" />
                        <div class="avatar-edit">
                            <label for="avatar-upload" title="Đổi ảnh đại diện">
                                <i class="ri-camera-line"></i>
                            </label>
                            <input type="file" id="avatar-upload" style="display: none" />
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name"><?= $user_name ?></h3> <div class="member-detail-list">
                            <div class="member-detail">
                                <i class="ri-mail-line"></i> Email:
                                <span><?= $user_email ?></span>
                            </div>
                            <div class="member-detail">
                                <i class="ri-phone-line"></i> SĐT: <span><?= $user_phone ?></span>
                            </div>
                            <div class="member-detail">
                                <i class="ri-calendar-line"></i> Ngày tham gia:
                                <span><?= date('d/m/Y', strtotime($user_created_at)) ?></span> </div>
                            <div class="member-detail">
                                <i class="ri-map-pin-line"></i> Địa chỉ:
                                <span><?= $user_address ?>, <?= $user_district ?>, <?= $user_city ?></span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="tab-pane" id="edit">
                <section class="edit-card">
                    <form class="edit-form" method="POST" autocomplete="off">
                        <h2 class="edit-title"><i class="ri-edit-line"></i> Chỉnh sửa thông tin</h2>
                        <div class="edit-group">
                            <label for="fullname">
                                <i class="ri-user-3-line"></i> Họ và tên
                            </label>
                            <input type="text" id="fullname" name="fullname" value="<?= $user_name ?>" required>
                        </div>
                        <div class="edit-group">
                            <label for="phone">
                                <i class="ri-phone-line"></i> Số điện thoại
                            </label>
                            <input type="tel" id="phone" name="phone" value="<?= $user_phone ?>" required>
                        </div>
                        <div class="edit-group">
                            <label for="address">
                                <i class="ri-map-pin-line"></i> Địa chỉ
                            </label>
                            <input type="text" id="address" name="address" value="<?= $user_address ?>" required>
                        </div>
                        <div class="edit-group">
                            <label for="city">
                                <i class="ri-building-line"></i> Thành phố
                            </label>
                            <input type="text" id="city" name="city" value="<?= $user_city ?>" required>
                        </div>
                        <div class="edit-group">
                            <label for="district">
                                <i class="ri-community-line"></i> Quận/Huyện
                            </label>
                            <input type="text" id="district" name="district" value="<?= $user_district ?>" required>
                        </div>
                        <div class="edit-actions-row">
                            <button type="submit" class="edit-btn save" name="action" value="update_profile">
                                <i class="ri-save-3-line"></i> Lưu thay đổi
                            </button>
                            <button type="button" class="edit-btn cancel" onclick="showTab('profile')"> <i class="ri-arrow-go-back-line"></i> Hủy
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <div class="tab-pane" id="change-password">
                <section class="change-password-card">
                    <form class="change-password-form" method="POST" autocomplete="off">
                        <h2 class="change-password-title">
                            <i class="ri-lock-password-line"></i> Đổi mật khẩu
                        </h2>
                        <div class="change-password-group">
                            <label for="old-password">
                                <i class="ri-lock-2-line"></i> Mật khẩu hiện tại
                            </label>
                            <input type="password" id="old-password" name="old-password" required>
                        </div>
                        <div class="change-password-group">
                            <label for="new-password">
                                <i class="ri-key-2-line"></i> Mật khẩu mới
                            </label>
                            <input type="password" id="new-password" name="new-password" required>
                        </div>
                        <div class="change-password-group">
                            <label for="confirm-password">
                                <i class="ri-key-line"></i> Xác nhận mật khẩu mới
                            </label>
                            <input type="password" id="confirm-password" name="confirm-password" required>
                        </div>
                        <div class="change-password-actions">
                            <button type="submit" class="change-btn save" name="action" value="change_password">
                                <i class="ri-save-3-line"></i> Lưu thay đổi
                            </button>
                            <button type="button" class="change-btn cancel" onclick="showTab('profile')"> <i class="ri-arrow-go-back-line"></i> Hủy
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </section>
    </div>
</main>

<script>
    function showTab(tabId) {
        // Ẩn tất cả các tab
        const tabs = document.querySelectorAll('.tab-pane');
        tabs.forEach(tab => tab.classList.remove('active'));

        // Hiển thị tab được chọn
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.classList.add('active');
        }

        // Loại bỏ class 'active' khỏi tất cả các nút
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(button => button.classList.remove('active'));

        // Thêm class 'active' vào nút tương ứng với tab được chọn
        const activeButton = document.querySelector(`[onclick="showTab('${tabId}')"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
    }

    // Hiển thị tab profile khi trang tải lần đầu (nếu không có lỗi từ POST request)
    document.addEventListener('DOMContentLoaded', function() {
        // Kiểm tra xem có thông báo lỗi/thành công nào được hiển thị không
        const successMsg = document.querySelector('.success-msg');
        const errorMsg = document.querySelector('.error-msg');

        if (successMsg || errorMsg) {
            // Nếu có thông báo, có thể muốn giữ tab hiện tại hoặc chuyển về tab profile
            // Hiện tại tôi sẽ mặc định chuyển về tab profile sau khi xử lý POST
            showTab('profile'); 
        } else {
            // Mặc định hiển thị tab profile khi không có POST data hoặc lỗi
            showTab('profile');
        }
    });

    // Xử lý nút "Hủy" trong form chỉnh sửa và đổi mật khẩu
    document.getElementById('btn-cancel-edit')?.addEventListener('click', function() {
        showTab('profile');
    });

    document.getElementById('btn-cancel-change-password')?.addEventListener('click', function() {
        showTab('profile');
    });

    // Thêm chức năng xem trước ảnh avatar (tùy chọn)
    document.getElementById('avatar-upload')?.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarImg = document.querySelector('.member-avatar');
                if (avatarImg) {
                    avatarImg.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });

</script>

<?php include '../includes/footer.php'; ?>