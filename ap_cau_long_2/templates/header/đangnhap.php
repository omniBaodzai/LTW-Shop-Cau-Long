<?php
session_start();
// include 'db_connection.php'; // Kết nối DB
// include 'user_functions.php'; // Các hàm liên quan đến người dùng (ví dụ: login_user, register_user)

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_phone = $_POST['emailOrPhone'] ?? '';
    $password = $_POST['password'] ?? '';

    // --- BẮT ĐẦU PHẦN PHP CẦN BỔ SUNG ---
    // 1. Kiểm tra thông tin đăng nhập trong DB
    // if (authenticate_user($email_or_phone, $password)) {
    //     // Đăng nhập thành công, lưu thông tin vào session và chuyển hướng
    //     $_SESSION['user_id'] = get_user_id($email_or_phone);
    //     $_SESSION['username'] = get_username($email_or_phone);
    //     header('Location: profile.php');
    //     exit;
    // } else {
    //     $error_message = "Email/Số điện thoại hoặc mật khẩu không đúng.";
    // }
    if ($email_or_phone == "test@example.com" && $password == "password") { // Ví dụ đăng nhập tĩnh
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = "Test User";
        header('Location: profile.php');
        exit;
    } else {
        $error_message = "Email/Số điện thoại hoặc mật khẩu không đúng (chức năng động đang được phát triển).";
    }
    // --- KẾT THÚC PHẦN PHP CẦN BỔ SUNG ---
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        /* CSS tương tự như file HTML ban đầu */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-container { background-color: white; padding: 40px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        h1 { margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        .form-group input { width: calc(100% - 20px); padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1em; }
        .login-button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 1.1em; cursor: pointer; margin-top: 20px; }
        .social-login { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .social-login p { margin-bottom: 15px; color: #666; }
        .social-buttons button { width: 100%; padding: 10px; margin-bottom: 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
        .social-buttons .facebook { background-color: #3b5998; color: white; }
        .social-buttons .google { background-color: #dd4b39; color: white; }
        .register-link { margin-top: 20px; font-size: 0.9em; }
        .register-link a { color: #007bff; text-decoration: none; }
        .error-message { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Đăng nhập</h1>
        <?php if ($error_message) { echo '<p class="error-message">' . htmlspecialchars($error_message) . '</p>'; } ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="emailOrPhone">Email hoặc Số điện thoại:</label>
                <input type="text" id="emailOrPhone" name="emailOrPhone" required value="<?php echo isset($_POST['emailOrPhone']) ? htmlspecialchars($_POST['emailOrPhone']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Đăng nhập</button>
        </form>

        <div class="social-login">
            <p>Hoặc đăng nhập bằng</p>
            <div class="social-buttons">
                <button class="facebook">Đăng nhập với Facebook</button>
                <button class="google">Đăng nhập với Google</button>
            </div>
        </div>

        <div class="register-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </div>
</body>
</html>