<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CauLongVN - Trang chủ</title>
  <link rel="stylesheet" href="/ap_cau_long/css/style.css" />
</head>
<body>

<!-- Header với logo, tìm kiếm, giỏ hàng và menu -->
<header class="banner-main">
  <div class="logo-wrapper">
    <img src="anh/Logo.jpg" alt="Logo CauLongVN" />
    <div class="logo-text-below">CauLongVN</div>
  </div>

  <div class="logo-text-side">
    Chuyên dụng cụ cầu lông chính hãng
  </div>

  <!-- Menu chính -->
  <nav class="main-menu">
  <ul>
    <li><a href="#">Trang chủ</a></li>
    <li class="has-submenu">
      <a href="#">Sản phẩm ▾</a>
      <ul class="submenu">
        <li><a href="#">Giày</a></li>
        <li><a href="#">Áo</a></li>
        <li><a href="#">Vợt</a></li>
        <li><a href="#">Túi</a></li>
      </ul>
    </li>
    <li class="has-submenu">
      <a href="#">Hướng dẫn ▾</a>
      <ul class="submenu">
        <li><a href="templates/footer/huong-dan-mua-hang.php">Hướng dẫn mua hàng</a></li>
        <li><a href="templates/footer/huong-dan-chon-vot-giay.php">Chọn giày và vợt</a></li>
      </ul>
    </li>
  </ul>
</nav>


  <div class="search-cart">
    <input type="text" placeholder="Tìm kiếm sản phẩm..." />
    <button>Tìm</button>
    <button class="btn-cart">
      <img src="anh/giohang.jpg" alt="Giỏ hàng" class="icon-cart" />
      Giỏ hàng
    </button>

  </div>
</header>

<!-- Banner chính dạng slideshow -->
<section class="slideshow-container">
  <?php
  $products = [
    [
      'image' => 'anh/Lining_Axforce_Cannon_Pro.jpg',
      'title' => 'Lining Axforce Cannon Pro',
      'desc' => 'Vợt cầu lông cao cấp – sức mạnh và độ chính xác tuyệt đối cho người chơi chuyên nghiệp.'
    ],
    [
      'image' => 'anh/CauLongVN.jpg',
      'title' => 'CauLongVN',
      'desc' => 'Trang web chuyên cung cấp dụng cụ cầu lông chính hãng với đa dạng sản phẩm chất lượng cao, phục vụ mọi nhu cầu của người chơi từ nghiệp dư đến chuyên nghiệp.'
    ],
    [
      'image' => 'anh/Yonex-65Z3-C-90.jpg',
      'title' => 'Giày Cầu Lông Yonex 65Z3 C-90',
      'desc' => 'Giày cầu lông với công nghệ đệm êm ái, tăng sự ổn định và phản hồi nhanh trên sân.'
    ]
  ];

  foreach ($products as $index => $product) {
    echo '<div class="slide">';
    echo '<img src="'.$product['image'].'" alt="'.$product['title'].'">';
    echo '<h2>'.$product['title'].'</h2>';
    echo '<p>'.$product['desc'].'</p>';
    echo '</div>';
  }
  ?>
</section>

<script src="js/main.js"></script>

</body>
</html>