<?php
session_start(); // Bắt đầu session để có thể sử dụng biến session (ví dụ: thông báo lỗi/thành công)

// --- BẮT ĐẦU PHẦN PHP CẦN BỔ SUNG ---
// Bao gồm file kết nối cơ sở dữ liệu và các hàm liên quan đến người dùng (nếu có)
// Ví dụ: include 'config/database.php';
// Ví dụ: include 'includes/user_functions.php';

$error_message = '';
$success_message = '';

// Kiểm tra nếu biểu mẫu đã được gửi đi (sử dụng phương thức POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ biểu mẫu và làm sạch chúng để tránh các vấn đề bảo mật (ví dụ: SQL Injection, XSS)
    $full_name = htmlspecialchars(trim($_POST['fullName'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $password = $_POST['password'] ?? ''; // Mật khẩu không nên được làm sạch bằng htmlspecialchars trước khi hash
    $confirm_password = $_POST['confirmPassword'] ?? '';

    // 1. Kiểm tra dữ liệu hợp lệ (Validation)
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Địa chỉ email không hợp lệ.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Mật khẩu xác nhận không khớp.";
    } elseif (strlen($password) < 6) {
        $error_message = "Mật khẩu phải có ít nhất 6 ký tự.";
    } else {
        // --- PHẦN PHP CẦN THIẾT CHO CHỨC NĂNG THỰC TẾ ---
        // 2. Kiểm tra xem email hoặc số điện thoại đã tồn tại trong cơ sở dữ liệu chưa
        // Đây là ví dụ giả lập, bạn cần thay thế bằng mã truy vấn cơ sở dữ liệu thực tế
        // function is_email_or_phone_registered($email, $phone, $db_connection) {
        //     $stmt = $db_connection->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR phone = ?");
        //     $stmt->bind_param("ss", $email, $phone);
        //     $stmt->execute();
        //     $stmt->bind_result($count);
        //     $stmt->fetch();
        //     $stmt->close();
        //     return $count > 0;
        // }

        // if (is_email_or_phone_registered($email, $phone, $your_db_connection_object)) {
        //     $error_message = "Email hoặc số điện thoại đã được đăng ký. Vui lòng sử dụng thông tin khác.";
        // } else {
            // 3. Hash mật khẩu (RẤT QUAN TRỌNG VÌ LÝ DO BẢO MẬT)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 4. Lưu thông tin người dùng vào cơ sở dữ liệu
            // Đây là ví dụ giả lập, bạn cần thay thế bằng mã truy vấn cơ sở dữ liệu thực tế
            // $stmt = $your_db_connection_object->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
            // $stmt->bind_param("ssss", $full_name, $email, $phone, $hashed_password);
            // if ($stmt->execute()) {
            //     $success_message = "Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.";
            //     // Có thể tự động đăng nhập người dùng hoặc chuyển hướng đến trang đăng nhập
            //     // header('Location: login.php?registered=true');
            //     // exit;
            // } else {
            //     $error_message = "Có lỗi xảy ra khi đăng ký tài khoản. Vui lòng thử lại.";
            // }
            // $stmt->close();

            // Ví dụ kết quả đăng ký thành công (thay thế bằng logic DB thực tế)
            $success_message = "Đăng ký tài khoản thành công! (Chức năng lưu DB đang được phát triển). Bạn có thể đăng nhập ngay bây giờ.";
        // }
    }
}
// --- KẾT THÚC PHẦN PHP CẦN BỔ SUNG ---
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <style>
        /* CSS tương tự như file HTML ban đầu */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .register-container { background-color: white; padding: 40px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        h1 { margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        .form-group input { width: calc(100% - 20px); padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1em; }
        .register-button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; font-size: 1.1em; cursor: pointer; margin-top: 20px; }
        .login-link { margin-top: 20px; font-size: 0.9em; }
        .login-link a { color: #007bff; text-decoration: none; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Đăng ký tài khoản mới</h1>

        <?php
        // Hiển thị thông báo lỗi hoặc thành công
        if ($error_message) {
            echo '<p class="message error">' . htmlspecialchars($error_message) . '</p>';
        } elseif ($success_message) {
            echo '<p class="message success">' . htmlspecialchars($success_message) . '</p>';
        }
        ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="fullName">Họ và tên:</label>
                <input type="text" id="fullName" name="fullName" required value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Xác nhận mật khẩu:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>
            <button type="submit" class="register-button">Đăng ký</button>
        </form>

        <div class="login-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </div>
    </div>
</body>
</html>