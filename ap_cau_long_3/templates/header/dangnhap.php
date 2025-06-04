<?php
session_start();
include '../../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash);
        if ($stmt->fetch()) {
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: /ap_cau_long/caulongvn.php");
                exit;
            } else {
                $error = "Mật khẩu không đúng!";
            }
        } else {
            $error = "Không tìm thấy tài khoản!";
        }
        $stmt->close();
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/ap_cau_long/css/header/dndk.css" />
</head>
<body>
    <form method="post" action="">
        <h2>Đăng nhập</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Đăng nhập</button>
        <p class="login-link">Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a></p>
    </form>
</body>
</html>