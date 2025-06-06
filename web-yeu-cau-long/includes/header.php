<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Chỉ bắt đầu session nếu chưa được bắt đầu
}
?>
<header class="header">
      <!-- Logo -->
      <a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/index.php" class="logo">
        <img src="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/assets/images/Badminton.png" alt="Logo Yêu Cầu Lông" />
        YeuCauLong
      </a>
      <!-- Navigation Left -->
      <nav class="nav-left">
        <ul class="nav-left-list">
            <a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/index.php" class="nav-left-link">Trang chủ</a>
          <li class="nav-left-item dropdown mega-dropdown">
            <a href="#" class="nav-left-link">
              Sản phẩm <i class="ri-arrow-down-s-line dropdown-arrow"></i>
            </a>
            <div class="dropdown-menu mega-menu">
              <div class="mega-menu-column">
                
                <div class="mega-menu-title"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/vot-cau-long.php">VỢT CẦU LÔNG</a></div>
                <a href="#" class="mega-menu-link">Vợt cầu lông Yonex</a>
                <a href="#" class="mega-menu-link">Vợt cầu lông Lining</a>
                <a href="#" class="mega-menu-link">Vợt cầu lông Victor</a>
                <a href="#" class="mega-menu-link">Vợt cầu lông Apacs</a>
                <a href="#" class="mega-menu-link">Vợt cầu lông Proace</a>
                <a href="#" class="mega-menu-link">Vợt cầu lông Fleet</a>
              </div>
              <div class="mega-menu-column">
                <div class="mega-menu-title"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/giay-cau-long.php">GIÀY CẦU LÔNG</div>
                <a href="#" class="mega-menu-link">Giày cầu lông Victor</a>
                <a href="#" class="mega-menu-link">Giày cầu lông Lining</a>
                <a href="#" class="mega-menu-link">Giày cầu lông Kawasaki</a>
                <a href="#" class="mega-menu-link">Giày cầu lông Mizuno</a>
                <a href="#" class="mega-menu-link">Giày cầu lông Kumpoo</a>
                <a href="#" class="mega-menu-link">Giày cầu lông Promax</a>
              </div>

              <div class="mega-menu-column">
                <div class="mega-menu-title"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/ao-cau-long.php">ÁO CẦU LÔNG</div>
                <a href="#" class="mega-menu-link">Áo cầu lông VNB</a>
                <a href="#" class="mega-menu-link">Áo cầu lông Kamito</a>
                <a href="#" class="mega-menu-link">Áo cầu lông Victor</a>
                <a href="#" class="mega-menu-link">Áo cầu lông Lining</a>
                <a href="#" class="mega-menu-link">Áo cầu lông DonexPro</a>
                <a href="#" class="mega-menu-link">Áo cầu lông Alien Armour</a>
              </div>
              <div class="mega-menu-column">
                <div class="mega-menu-title"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/quan-cau-long.php">QUẦN CẦU LÔNG</div>
                <a href="#" class="mega-menu-link">Quần cầu lông Victor</a>
                <a href="#" class="mega-menu-link">Quần cầu lông Lining</a>
                <a href="#" class="mega-menu-link">Quần cầu lông VNB</a>
                <a href="#" class="mega-menu-link">Quần cầu lông SFD</a>
                <a href="#" class="mega-menu-link">Quần cầu lông Donex Pro</a>
                <a href="#" class="mega-menu-link">Quần cầu lông Apacs</a>
              </div>
              <div class="mega-menu-column">
                <div class="mega-menu-title"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/vay-cau-long.php">VÁY CẦU LÔNG</div>
                <a href="#" class="mega-menu-link">Váy cầu lông Victec</a>
                <a href="#" class="mega-menu-link">Váy cầu lông Lining</a>
                <a href="#" class="mega-menu-link">Váy cầu lông Donex Pro</a>
                <a href="#" class="mega-menu-link">Váy cầu lông Victor</a>
                <a href="#" class="mega-menu-link">Váy cầu lông Kamito</a>
                <a href="#" class="mega-menu-link">Váy cầu lông Taro</a>
              </div>

              <div class="mega-menu-column">
                <div class="mega-menu-title"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/ong-cau-long.php">ỐNG CẦU LÔNG</div>
                <a href="#" class="mega-menu-link">Ống cầu lông VNB</a>
                <a href="#" class="mega-menu-link">Ống cầu lông Yonex</a>
                <a href="#" class="mega-menu-link">Ống cầu lông Lining</a>
                <a href="#" class="mega-menu-link">Ống cầu lông Victor</a>
                <a href="#" class="mega-menu-link">Ống cầu lông Apacs</a>
                <a href="#" class="mega-menu-link">Ống cầu lông Proace</a>
              </div>
              <div class="mega-menu-column">
                <div class="mega-menu-title"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/phu-kien.php">PHỤ KIỆN CẦU LÔNG</div>
                <a href="#" class="mega-menu-link">Túi cầu lông</a>
                <a href="#" class="mega-menu-link">Băng quấn cán</a>
                <a href="#" class="mega-menu-link">Quấn cán vợt</a>
                <a href="#" class="mega-menu-link">Dây cước</a>
                <a href="#" class="mega-menu-link">Ống cầu lông</a>
                <a href="#" class="mega-menu-link">Bình nước</a>
              </div>
            </div>
          </li>
          <li><a href="#" class="nav-left-link">Khuyến mãi</a></li>
          <li><a href="#" class="nav-left-link">Tin tức</a></li>
        </ul>
      </nav>

      <!-- Search Bar -->
      <div class="search-box">
          <form action="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/tim-kiem.php" method="GET">
              <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." required />
              <button type="submit"><i class="ri-search-line"></i></button>
          </form>
      </div>

      <!-- Navigation Right -->
<nav class="nav-right">
    <ul class="nav-right-list">
        <!-- Dropdown - Tài khoản -->
        <li class="nav-right-item dropdown">
            <?php
            
            if (isset($_SESSION['user_name'])) {
                // Hiển thị tên người dùng và menu "Trang Cá Nhân" và "Đăng Xuất"
                echo '<a href="#" class="nav-right-link">';
                echo '<i class="ri-user-line"></i> ' . htmlspecialchars($_SESSION['user_name']);
                echo '<i class="ri-arrow-down-s-line dropdown-arrow"></i>';
                echo '</a>';
                echo '<ul class="dropdown-menu">';
                echo '<li><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/thanh-vien.php" class="dropdown-link">';
                echo '<i class="ri-user-line"></i> Trang Cá Nhân';
                echo '</a></li>';
                echo '<li><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/dang-xuat.php" class="dropdown-link">';
                echo '<i class="ri-logout-box-line"></i> Đăng Xuất';
                echo '</a></li>';
                echo '</ul>';
            } else {
                // Hiển thị menu "Đăng nhập" và "Đăng ký"
                echo '<a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/dang-ky.php" class="nav-right-link">';
                echo '<i class="ri-user-line"></i> Tài khoản';
                echo '<i class="ri-arrow-down-s-line dropdown-arrow"></i>';
                echo '</a>';
                echo '<ul class="dropdown-menu">';
                echo '<li><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/dang-ky.php" class="dropdown-link">';
                echo '<i class="ri-user-add-line"></i> Đăng ký';
                echo '</a></li>';
                echo '<li><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/dang-nhap.php" class="dropdown-link">';
                echo '<i class="ri-login-box-line"></i> Đăng nhập';
                echo '</a></li>';
                echo '</ul>';
            }
            ?>
        </li>

          <!-- Dropdown - Giỏ hàng -->

          <li class="nav-right-item dropdown">
              <a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/gio-hang.php" class="nav-right-link">
                  <i class="ri-shopping-cart-line"></i> Giỏ hàng
                  <?php
                  // Kiểm tra giỏ hàng trong session
                  if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                      echo '<span class="cart-count">' . count($_SESSION['cart']) . '</span>'; // Hiển thị số lượng sản phẩm
                  } else {
                      echo '<span class="cart-count">0</span>'; // Hiển thị 0 nếu không có sản phẩm
                  }
                  ?>
                  <i class="ri-arrow-down-s-line dropdown-arrow"></i>
              </a>
              <div class="dropdown-menu cart-dropdown">
                  <i class="ri-shopping-bag-3-line cart-icon"></i>
                  <?php
                  if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                      echo '<ul class="cart-items">';
                      foreach ($_SESSION['cart'] as $item) {
                          echo '<li>' . htmlspecialchars($item['name']) . ' x ' . $item['quantity'] . '</li>';
                      }
                      echo '</ul>';
                  } else {
                      echo '<p class="empty-cart-msg">Không có sản phẩm trong giỏ hàng</p>';
                  }
                  ?>
              </div>
          </li>

          <!-- Link - Tra cứu đơn hàng -->
          <li class="nav-right-item dropdown">
            <a href="#" class="nav-right-link">
              <i class="ri-file-list-3-line"></i>
              Tra cứu đơn hàng
              <i class="ri-arrow-down-s-line dropdown-arrow"></i>
            </a>
            <ul class="dropdown-menu order-dropdown">
              <li>
                
                <a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/tra-cuu-don-hang.php" class="dropdown-link">
                  <i class="ri-search-eye-line"></i> Kiểm tra đơn hàng
                </a>
              </li>
              <li>
                <a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/kiem-tra-bao-hanh.php" class="dropdown-link">
                  <i class="ri-tools-line"></i> Kiểm tra bảo hành
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </header>
    <style>
.search-box {
    display: flex; /* Hiển thị các phần tử theo chiều ngang */
    align-items: center; /* Căn giữa các phần tử theo chiều dọc */
    justify-content: space-between; /* Căn đều khoảng cách giữa các phần tử */
    margin: 0 auto;
    max-width: 400px; /* Đặt chiều rộng tối đa cho thanh tìm kiếm */
    background-color: #f9f9f9;
    
    border-radius: 25px;
    padding: 5px 15px;
    display: flex;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.search-box form {
    display: flex;
    width: 100%; /* Đảm bảo form chiếm toàn bộ không gian của search-box */
}
.search-box input[type="text"] {
    flex: 1; /* Đảm bảo ô nhập chiếm toàn bộ không gian còn lại */
    border: none;
    outline: none;
    background: transparent;
    font-size: 16px;
    padding: 5px;
    color: #333;
}

.search-box input[type="text"]::placeholder {
    color: #aaa;
}

.search-box button {
    background: none;
    border: none;
    cursor: pointer;
    color: #007bff;
    font-size: 18px;
    padding: 5px;
    transition: color 0.3s ease;
}

.search-box button:hover {
    color: #0056b3;
}
.nav-right-item .cart-count {
    background-color:rgb(179, 207, 238); /* Màu nền cho số lượng sản phẩm */
    color:rgb(196, 64, 24); /* Màu chữ */
    font-size: 12px;
    font-weight: bold;
    border-radius: 50%; /* Bo tròn */
    padding: 2px 6px; /* Khoảng cách bên trong */
    margin-left: 5px; /* Khoảng cách giữa biểu tượng và số lượng */
}

.dropdown-menu.cart-dropdown {
    position: absolute;
    top: 100%; /* Hiển thị ngay bên dưới liên kết */
    left: 0;
    width: 250px; /* Chiều rộng của menu */
    background-color: #fff; /* Màu nền */
    border: 1px solid #ddd; /* Đường viền */
    border-radius: 8px; /* Bo góc */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Hiệu ứng đổ bóng */
    padding: 10px; /* Khoảng cách bên trong */
    z-index: 1000; /* Đảm bảo menu hiển thị trên các phần tử khác */
    display: none; /* Ẩn menu mặc định */
    top: calc(100% + 10px);
}

.nav-right-item.dropdown:hover .dropdown-menu.cart-dropdown {
    display: block; /* Hiển thị menu khi hover */
}

.cart-dropdown .cart-icon {
    font-size: 24px; /* Kích thước biểu tượng */
     /* Màu biểu tượng */
    margin-bottom: 10px; /* Khoảng cách dưới biểu tượng */
    display: block;
    text-align: center;
}

.cart-dropdown .cart-items {
    list-style: none; /* Xóa dấu đầu dòng */
    padding: 0;
    margin: 0;
}

.cart-dropdown .cart-items li {
    font-size: 14px;
    color: #333; /* Màu chữ */
    padding: 5px 0; /* Khoảng cách giữa các mục */
    border-bottom: 1px solid #ddd; /* Đường viền dưới mỗi mục */
}

.cart-dropdown .cart-items li:last-child {
    border-bottom: none; /* Xóa đường viền dưới mục cuối cùng */
}

.cart-dropdown .empty-cart-msg {
    font-size: 14px;
    color: #999; /* Màu chữ nhạt */
    text-align: center; /* Căn giữa thông báo */
    margin: 10px 0; /* Khoảng cách trên và dưới */
}
</style>