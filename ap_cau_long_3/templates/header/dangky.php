<?php
include '../../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($username && $password && $password_confirm && $password === $password_confirm) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hash, $email);
            if ($stmt->execute()) {
                header("Location: dangnhap.php");
                exit;
            } else {
                $error = "Lỗi đăng ký: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Vui lòng nhập đầy đủ và đúng thông tin!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="/ap_cau_long/css/header/dndk.css" />
</head>
<body>
    <form method="post" action="">
        <h2>Đăng ký tài khoản</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email">
        
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" required>
        
        <label for="password_confirm">Nhập lại mật khẩu:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
        
        <button type="submit">Đăng ký</button>
        <p class="login-link">Đã có tài khoản? <a href="dangnhap.php">Đăng nhập ngay</a></p>
    </form>
</body>
</html>