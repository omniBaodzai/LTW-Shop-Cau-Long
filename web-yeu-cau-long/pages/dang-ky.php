
<?php include '../connect.php'; ?>
<?php
// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu

    // Kiểm tra email đã tồn tại
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email đã tồn tại. Vui lòng sử dụng email khác.";
    } else {
        // Thêm người dùng mới vào bảng `users`
        $insertQuery = "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssss", $name, $email, $phone, $password);

        if ($stmt->execute()) {
            // Chuyển hướng sang trang đăng nhập
            header("Location: dang-nhap.php");
            exit();
        } else {
            $error = "Có lỗi xảy ra. Vui lòng thử lại.";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
  main {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc88 60%, #e0e7ff88 100%),
      url("https://tennislovers.com/wp-content/uploads/badminton-racket-and-shuttlecock.jpg")
        center/cover no-repeat;
    background-blend-mode: multiply;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 0;
  }
</style>

<main>
  <div class="register-2col">
    <div class="register-left">
      <div class="welcome-content">
        <img
          src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
          alt="Welcome"
          class="welcome-img"
        />
        <h2>Chào mừng bạn đến với <span class="brand">YeuCauLong</span></h2>
        <p>
          Tạo tài khoản ngay hôm nay để không bỏ lỡ các chương trình khuyến mãi đặc biệt!!!
        </p>
        <a href="dang-nhap.php" class="login-cta">
          <i class="ri-login-box-line"></i> Đã có tài khoản? Đăng nhập ngay
        </a>
      </div>
    </div>
    <div class="register-right">
      <form class="glass-form" method="POST">
        <h2>Đăng ký tài khoản</h2>
        <?php if (isset($error)): ?>
          <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>
        <div class="form-group">
          <i class="ri-user-line"></i>
          <input type="text" name="name" placeholder="Họ và tên" required />
        </div>
        <div class="form-group">
          <i class="ri-mail-line"></i>
          <input type="email" name="email" placeholder="Email" required />
        </div>
        <div class="form-group">
          <i class="ri-phone-line"></i>
          <input type="tel" name="phone" placeholder="Số điện thoại" required />
        </div>
        <div class="form-group">
          <i class="ri-lock-line"></i>
          <input type="password" name="password" placeholder="Mật khẩu" required />
        </div>
        <div class="form-group">
          <i class="ri-lock-password-line"></i>
          <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required />
        </div>
        <div class="terms">
          <input type="checkbox" id="terms" required />
          <label for="terms"
            >Tôi đồng ý với <a href="#">Điều khoản & Chính sách</a></label
          >
        </div>
        <button type="submit" class="register-btn">Tạo tài khoản</button>
      </form>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>