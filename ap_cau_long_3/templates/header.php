<!-- templates/header.php -->
<!-- FontAwesome nên đặt trong <head> hoặc include chung, nhưng tạm để đây -->
<!-- templates/header.php -->
<!-- FontAwesome nên đặt trong <head> hoặc include chung, nhưng tạm để đây -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Link file CSS riêng của header -->
<link rel="stylesheet" href="/ap_cau_long/css/header/style.css">

<header class="banner-main">
  <!-- phần còn lại giữ nguyên -->

  <div class="logo-wrapper">
    <img src="/ap_cau_long/anh/Logo.jpg" alt="Logo CauLongVN" />
    <div class="logo-text-below">CauLongVN</div>
  </div>

  <div class="logo-text-side">
    Chuyên dụng cụ cầu lông chính hãng
  </div>

  <nav class="main-menu">
    <ul>
  <li><a href="/ap_cau_long/caulongvn.php">Trang chủ</a></li>
  <li class="has-submenu">
    <a href="#">Sản phẩm ▾</a>
    <ul class="submenu">
      <li><a href="/ap_cau_long/templates/sanpham/giay-cau-long.php">Giày</a></li>
      <li><a href="/ap_cau_long/templates/sanpham/ao-cau-long.php">Áo</a></li>
      <li><a href="/ap_cau_long/templates/sanpham/vot-cau-long.php">Vợt</a></li>
      <li><a href="/ap_cau_long/templates/sanpham/tui-cau-long.php">Túi</a></li>
    </ul>
  </li>
  <li class="has-submenu">
    <a href="#">Hướng dẫn ▾</a>
    <ul class="submenu">
      <li><a href="/ap_cau_long/templates/footer/huong-dan-mua-hang.php">Hướng dẫn mua hàng</a></li>
      <li><a href="/ap_cau_long/templates/footer/huong-dan-chon-vot-giay.php">Hướng dẫn chọn giày và vợt</a></li>
    </ul>
  </li>
</ul>

  </nav>

  <div class="search-cart">
    <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm..." />
    <button id="search-btn">Tìm</button>

    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Giả sử $_SESSION['username'] lưu tên người dùng sau khi đăng nhập thành công
?>

<ul>
<li class="has-submenu">
  <?php if (isset($_SESSION['username'])): ?>
    <a href="javascript:void(0);" class="btn-cart">
      <i class="fas fa-circle-user icon-cart"></i> <?= htmlspecialchars($_SESSION['username']) ?> ▾
    </a>
    <ul class="submenu">
      <li><a href="/ap_cau_long/templates/header/khachhang.php">Trang tài khoản</a></li>
      <li><a href="#" id="logout-link" class="text-danger">🚪 Đăng xuất</a></li>
    </ul>
  <?php else: ?>
    <a href="javascript:void(0);" class="btn-cart">
      <i class="fas fa-circle-user icon-cart"></i> Tài khoản ▾
    </a>
    <ul class="submenu">
      <li><a href="/ap_cau_long/templates/header/dangnhap.php">Đăng nhập</a></li>
      <li><a href="/ap_cau_long/templates/header/dangky.php">Đăng ký</a></li>
    </ul>
  <?php endif; ?>
</li>


</ul>


    <div class="cart-wrapper">
  <a href="/ap_cau_long/templates/header/giohang.php" class="btn-cart">
    <img src="/ap_cau_long/anh/giohang.jpg" alt="Giỏ hàng" class="icon-cart" />
    Giỏ hàng
  </a>
</div>


  </div>

  <script>
    // Khi bấm nút Tìm, chuyển trang tới trang tìm kiếm với tham số query từ input
    document.getElementById('search-btn').addEventListener('click', function() {
      const query = document.getElementById('search-input').value.trim();
      if(query) {
        window.location.href = '/ap_cau_long/templates/header/timkiem.php?q=' + encodeURIComponent(query);
      }
    });

    // Nếu muốn bấm Enter trong ô input cũng tìm kiếm
    document.getElementById('search-input').addEventListener('keypress', function(e) {
      if(e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('search-btn').click();
      }
    });
  </script>
</header>


