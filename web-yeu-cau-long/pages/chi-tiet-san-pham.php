<?php
session_start(); // Bắt đầu session để lấy thông tin người dùng
include '../connect.php'; // Kết nối cơ sở dữ liệu
include '../includes/header.php'; 

// Kiểm tra xem người dùng đã đăng nhập hay chưa
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Lấy tên người dùng từ bảng users
$user_name = '';
if ($user_id > 0) {
    $sql_user = "SELECT name FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $user_name = htmlspecialchars($user['name']);
    }
}

// Lấy id sản phẩm từ URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn thông tin sản phẩm từ cơ sở dữ liệu
$sql_product = "SELECT * FROM products WHERE id = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$result_product = $stmt_product->get_result();

if ($result_product->num_rows > 0) {
    $product = $result_product->fetch_assoc();
} else {
    echo "<p>Sản phẩm không tồn tại.</p>";
    exit();
}

$promotion = isset($product['promotion']) ? $product['promotion'] : '';

// Xử lý form gửi đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_submit'])) {
    $rating = intval($_POST['rating']);
    $content = htmlspecialchars($_POST['content']);

    // Check if user is logged in before allowing review submission
    if ($user_id == 0) {
        echo "<p>Vui lòng đăng nhập để gửi đánh giá.</p>";
    } elseif ($rating >= 1 && $rating <= 5 && !empty($user_name) && !empty($content)) {
        $sql_insert_review = "INSERT INTO reviews (product_id, user_name, rating, content) VALUES (?, ?, ?, ?)";
        $stmt_insert_review = $conn->prepare($sql_insert_review);
        $stmt_insert_review->bind_param("isis", $product_id, $user_name, $rating, $content);
        if ($stmt_insert_review->execute()) {
            echo "<p>Đánh giá của bạn đã được gửi!</p>";
        } else {
            echo "<p>Đã xảy ra lỗi khi gửi đánh giá. Vui lòng thử lại.</p>";
        }
    } else {
        echo "<p>Vui lòng nhập đầy đủ thông tin và chọn số sao hợp lệ.</p>";
    }
}

// Truy vấn đánh giá sản phẩm từ bảng reviews
$sql_reviews = "SELECT * FROM reviews WHERE product_id = ?";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();

// Calculate average rating and total reviews for display
$avg_rating = 0;
$total_reviews = 0;
$sql_avg_rating = "SELECT AVG(rating) AS avg_rating, COUNT(id) AS total_reviews FROM reviews WHERE product_id = ?";
$stmt_avg_rating = $conn->prepare($sql_avg_rating);
$stmt_avg_rating->bind_param("i", $product_id);
$stmt_avg_rating->execute();
$result_avg_rating = $stmt_avg_rating->get_result();
if ($result_avg_rating->num_rows > 0) {
    $row_avg_rating = $result_avg_rating->fetch_assoc();
    $avg_rating = round($row_avg_rating['avg_rating'], 1);
    $total_reviews = $row_avg_rating['total_reviews'];
}


?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link rel="stylesheet" href="../assets/css/style.css">

<main class="product-detail-main sporty">
  <div class="product-detail-container">
    <nav class="badminton-breadcrumb">
      <a href="../index.php">Trang chủ</a>
      <span class="breadcrumb-sep">›</span>
      <a href="vot-cau-long.php">Vợt Cầu Lông</a>
      <span class="breadcrumb-sep">›</span>
      <span><?php echo htmlspecialchars($product['name']); ?></span>
    </nav>
    <div class="product-detail-body">
      <div class="product-detail-gallery">
        <div class="product-detail-img-zoom sporty-zoom">
          <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainProductImg" />
        </div>
      </div>
      <div class="product-detail-info">
        <h1 class="product-detail-name sporty-gradient"><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="product-detail-meta">
          <span><i class="ri-barcode-box-line"></i> Mã SP: <b><?php echo htmlspecialchars($product['sku']); ?></b></span>
          <span><i class="ri-shield-check-line"></i> Bảo hành: <b><?php echo htmlspecialchars($product['warranty']); ?></b></span>
          <span><i class="ri-truck-line"></i> Vận chuyển: <b>Toàn quốc</b></span>
        </div>
        <div class="product-detail-promo sporty-promo">
          <div class="promo-title"><i class="ri-gift-2-fill"></i> Ưu đãi hôm nay</div>
          <ul>
            <?php 
            $promo_items = explode(',', $promotion);
            foreach ($promo_items as $item): 
            ?>
              <li><i class="ri-check-double-line"></i> <?php echo htmlspecialchars(trim($item)); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="product-detail-pricebox">
          <span class="product-detail-price"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</span>
          <?php if ($product['old_price']): ?>
            <span class="product-detail-oldprice"><?php echo number_format($product['old_price'], 0, ',', '.'); ?> đ</span>
            <span class="product-detail-sale"><?php echo round((($product['old_price'] - $product['price']) / $product['old_price']) * 100); ?>%</span>
          <?php endif; ?>
        </div>
        <div class="product-detail-qtybox">
          <label for="qty">Số lượng:</label>
          <div class="qty-control">
            <button type="button" class="qty-btn" onclick="changeQty(-1)">-</button>
            <input type="number" name="quantity" id="qty" value="1" min="1" max="99" required>
            <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
          </div>
        </div>
        <div class="product-detail-actions">
          <form method="POST" action="thanh-toan.php" onsubmit="syncQty(this)">
            <input type="hidden" name="action" value="buy_now">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
            <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
            <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['image']) ?>">
            <input type="hidden" name="quantity" id="buy-now-qty">
            <button type="submit" class="btn-buy sporty-btn">Mua ngay</button>
          </form>
            <button type="button" class="btn-cart sporty-btn-outline" onclick="addToCart(<?= $product['id'] ?>, document.getElementById('qty').value)">
                <i class="ri-shopping-cart-2-fill"></i> Thêm vào giỏ
            </button>
          
        </div>
        
        <!-- Modal thông báo -->
      <div id="cart-modal" class="cart-modal" style="display: none;">
          <div class="cart-modal-header">
              <span><i class="ri-check-line"></i> Thêm sản phẩm vào giỏ hàng thành công</span>
              <span class="close-btn" onclick="closeCartModal()">×</span>
          </div>
          <div class="cart-modal-content">
              <img id="cart-modal-product-image" src="" alt="Sản phẩm">
              <h4 id="cart-modal-product-name">Tên sản phẩm</h4>
              <div id="cart-modal-product-price" class="price">Giá sản phẩm</div>
              <div class="cart-info">
                  Giỏ hàng của bạn hiện có <span id="cart-modal-total-items"></span> sản phẩm
              </div>
          </div>
          <div class="cart-modal-buttons">
              <button onclick="closeCartModal()">Tiếp tục mua hàng</button>
              <button onclick="window.location.href='gio-hang.php'">Xem giỏ hàng</button>
          </div>
      </div>

        <div class="product-detail-share">
          <span>Chia sẻ:</span>
          <a href="#"><i class="ri-facebook-circle-fill"></i></a>
          <a href="#"><i class="ri-messenger-fill"></i></a>
          <a href="#"><i class="ri-zalo-fill"></i></a>
        </div>
        <div class="product-detail-extra sporty-extra">
          <div><i class="ri-award-fill"></i> Cam kết chính hãng 100%</div>
          <div><i class="ri-customer-service-2-fill"></i> Hỗ trợ tư vấn 24/7</div>
          <div><i class="ri-refresh-line"></i> Đổi trả trong 7 ngày</div>
        </div>
      </div>
    </div>
    <div class="product-detail-tabs">
      <button class="tab-btn active" onclick="showTab('desc')">Mô tả sản phẩm</button>
      <button class="tab-btn" onclick="showTab('specs')">Thông số kỹ thuật</button>
      <button class="tab-btn" onclick="showTab('review')">Đánh giá</button>
    </div>
    <div class="product-detail-tab-content">
      <div class="tab-pane active" id="desc">
        <h2>Giới thiệu sản phẩm</h2>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
      </div>
      <div class="tab-pane" id="specs">
        <h2>Thông số kỹ thuật</h2>
        <p><?php echo nl2br(htmlspecialchars($product['specs'])); ?></p>
      </div>
      <div class="tab-pane" id="review">
        <h2 class="review-title">Đánh giá sản phẩm</h2>
        <div class="review-summary">
          <span class="review-score"><?php echo $avg_rating; ?></span>
          <span class="review-stars">
            <?php 
            // Display filled stars based on average rating
            for ($i = 1; $i <= 5; $i++): 
              if ($i <= floor($avg_rating)) {
                echo '<i class="ri-star-fill"></i>';
              } elseif ($i - 0.5 <= $avg_rating) {
                echo '<i class="ri-star-half-fill"></i>';
              } else {
                echo '<i class="ri-star-line"></i>';
              }
            endfor; 
            ?>
          </span>
          <span class="review-count">(<?php echo $total_reviews; ?> đánh giá)</span>
        </div>
        <div class="product-detail-review-list">
          <?php 
          // Reset reviews result pointer if needed
          if ($result_reviews->num_rows > 0) {
              $result_reviews->data_seek(0); // Rewind to the beginning for displaying reviews
          }

          if ($result_reviews->num_rows > 0): ?>
            <?php while ($review = $result_reviews->fetch_assoc()): ?>
              <div class="review-item">
                <div class="review-user"><i class="ri-user-3-fill"></i> <?php echo htmlspecialchars($review['user_name']); ?></div>
                <div class="review-stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="ri-star<?php echo $i <= $review['rating'] ? '-fill' : '-line'; ?>"></i>
                  <?php endfor; ?>
                </div>
                <div class="review-content"><?php echo nl2br(htmlspecialchars($review['content'])); ?></div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
          <?php endif; ?>
        </div>
        <div class="product-detail-review-form">
          <h3 class="form-title">Gửi đánh giá của bạn</h3>
          <form method="POST">
            <p><b>Người dùng:</b> <?php echo $user_name ? $user_name : 'Bạn cần đăng nhập để đánh giá'; ?></p>
            <textarea name="content" placeholder="Nhận xét của bạn..." <?php echo $user_id == 0 ? 'disabled' : ''; ?> required></textarea>
            <div class="review-form-stars">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="ri-star-line" data-value="<?php echo $i; ?>"></i>
              <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="rating" required />
            <button type="submit" name="review_submit" class="btn-review-send" <?php echo $user_id == 0 ? 'disabled' : ''; ?>>Gửi đánh giá</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const stars = document.querySelectorAll('.review-form-stars i');
  const ratingInput = document.getElementById('rating');

  let selectedRating = 0; // This will store the rating value after a click

  // Initialize stars if there's a default rating, though typically reviews start at 0
  // You might remove this if you don't expect a pre-selected rating on page load.
  // if (ratingInput.value) {
  //   selectedRating = parseInt(ratingInput.value);
  //   stars.forEach((star, index) => {
  //     if (index < selectedRating) {
  //       star.classList.add('active');
  //     }
  //   });
  // }

  stars.forEach((star, index) => {
    // Handle click event to set the permanent rating
    star.addEventListener('click', () => {
      selectedRating = index + 1; // Update the selected rating
      ratingInput.value = selectedRating; // Set the hidden input value

      // Apply 'active' class to stars up to the selected rating
      stars.forEach((s, i) => {
        if (i < selectedRating) {
          s.classList.add('active');
          s.classList.remove('ri-star-line'); // Ensure correct icon
          s.classList.add('ri-star-fill');    // Ensure correct icon
        } else {
          s.classList.remove('active', 'ri-star-fill', 'ri-star-half-fill'); // Remove active and fill if not selected
          s.classList.add('ri-star-line'); // Set to outline
        }
      });
    });

    // Handle mouse enter for hover effect (temporary highlighting)
    star.addEventListener('mouseenter', () => {
      // Only apply hover effect if no rating has been selected yet, or to show potential new selection
      stars.forEach((s, i) => {
        if (i <= index) {
          s.classList.add('hover');
          s.classList.remove('ri-star-line'); // Ensure correct icon for hover
          s.classList.add('ri-star-fill');    // Ensure correct icon for hover
        } else {
          // If a star beyond the hover point is active, remove its active state temporarily for hover
          if (selectedRating === 0 || i >= selectedRating) { // Only change if not part of the active selection
             s.classList.remove('ri-star-fill', 'ri-star-half-fill');
             s.classList.add('ri-star-line');
          }
        }
      });
    });

    // Handle mouse leave to revert hover effect, but keep selected rating colored
    star.addEventListener('mouseleave', () => {
      stars.forEach((s, i) => {
        s.classList.remove('hover'); // Remove hover class

        // Revert to selected state if a rating is active, otherwise to outline
        if (i < selectedRating) {
          s.classList.add('active'); // Keep active class
          s.classList.remove('ri-star-line');
          s.classList.add('ri-star-fill');
        } else {
          s.classList.remove('active', 'ri-star-fill', 'ri-star-half-fill');
          s.classList.add('ri-star-line');
        }
      });
    });
  });
});
</script>
<script>
  
// Tab switching
function addToCart(productId, quantity) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "gio-hang.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);

            // Hiển thị thông báo
            document.getElementById('cart-modal-product-name').textContent = response.product_name;
            document.getElementById('cart-modal-product-price').textContent = response.product_price + ' đ';
            document.getElementById('cart-modal-product-image').src = response.product_image; // Hiển thị hình ảnh
            document.getElementById('cart-modal-total-items').textContent = response.total_items;
            document.getElementById('cart-modal').style.display = 'block';
        }
    };

    xhr.send(`action=add_to_cart&product_id=${productId}&quantity=${quantity}`);
}

function closeCartModal() {
    document.getElementById('cart-modal').style.display = 'none';
}
function showTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
  document.querySelector('.tab-btn[onclick*="' + tab + '"]').classList.add('active');
  document.getElementById(tab).classList.add('active');
}

// LightGallery initialization (kept as is)
document.addEventListener('DOMContentLoaded', function() {
  const gallery = document.querySelector('.product-detail-gallery');
  if (gallery) { // Ensure gallery exists before initializing
    lightGallery(gallery, {
      selector: 'img', // Change selector to 'img' if you only have one main image and no thumbnails in the gallery div
      plugins: [lgZoom, lgFullscreen, lgRotate, lgThumbnail, lgDownload],
      zoom: true,
      fullscreen: true,
      rotate: true,
      thumbnail: true,
      download: true,
      actualSize: true,
      hideBarsDelay: 2000,
      slideShowAutoplay: false,
      slideShowInterval: 4000,
      mode: 'lg-fade',
      speed: 400,
      controls: true,
      counter: true,
      swipeThreshold: 50,
      enableDrag: true,
      enableTouch: true,
      showMaximizeIcon: true,
      allowMediaOverlap: true,
      getCaptionFromTitleOrAlt: true
    });
  }
});
</script>
<script>
function changeQty(amount) {
    const qtyInput = document.getElementById('qty');
    let currentQty = parseInt(qtyInput.value, 10);
    const minQty = parseInt(qtyInput.min, 10);
    const maxQty = parseInt(qtyInput.max, 10);

    currentQty += amount;

    if (currentQty >= minQty && currentQty <= maxQty) {
        qtyInput.value = currentQty;
    }
}
</script>
<script>
function changeQty(amount) {
    const qtyInput = document.getElementById('qty');
    let currentQty = parseInt(qtyInput.value, 10);
    const minQty = parseInt(qtyInput.min, 10);
    const maxQty = parseInt(qtyInput.max, 10);

    currentQty += amount;

    if (currentQty >= minQty && currentQty <= maxQty) {
        qtyInput.value = currentQty;
    }
}

function syncQty(form) {
    const qtyInput = document.getElementById('qty');
    const hiddenQtyInput = form.querySelector('input[name="quantity"]');
    hiddenQtyInput.value = qtyInput.value;
}
</script>

<style>
.cart-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    padding: 0;
    width: 400px;
    z-index: 1000;
    font-family: 'Arial', sans-serif;
    animation: fadeIn 0.3s ease-in-out;
    overflow: hidden;
}

.cart-modal-header {
    background: linear-gradient(90deg, #1d3557 0%, #e63946 100%);/* Gradient từ xanh lá đến đỏ */
    color: white;
    padding: 10px 16px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px 8px 0 0; /* Bo góc trên */
}

.cart-modal-header i {
    margin-right: 8px;
}

.cart-modal-header .close-btn {
    cursor: pointer;
    font-size: 18px;
    color: white;
}

.cart-modal-content {
    padding: 20px;
    text-align: left;
}

.cart-modal-content img {
    width: 50px;
    float: left;
    margin-right: 16px;
}

.cart-modal-content h4 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.cart-modal-content .price {
    font-size: 18px;
    font-weight: bold;
    color: #000;
    margin: 8px 0;
}

.cart-modal-content .cart-info {
    margin-top: 10px;
    font-size: 14px;
    color: #555;
}

.cart-modal-buttons {
    display: flex;
    justify-content: space-around;
    padding: 16px;
    border-top: 1px solid #eee;
}

.cart-modal-buttons button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.cart-modal-buttons button:first-child {
    background:  linear-gradient(90deg, #43a047 0%, #e63946 100%); /* Gradient từ xanh lá đậm đến nhạt */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px; /* Bo góc */
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
}


.cart-modal-buttons button:first-child:hover {
    background: linear-gradient(90deg, #388e3c 0%, #43a047 100%); /* Gradient khi hover */
    transform: scale(1.05); /* Hiệu ứng phóng to */
}

.cart-modal-buttons button:last-child {
    background: linear-gradient(90deg, #e63946 0%,rgb(46, 63, 100) 100%); /* Gradient từ đỏ đậm đến nhạt */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px; /* Bo góc */
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.cart-modal-buttons button:last-child:hover {
    background: linear-gradient(90deg, #d84315 0%, #e63946 100%); /* Gradient khi hover */
    transform: scale(1.05); /* Hiệu ứng phóng to */
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.review-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.review-summary {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.review-score {
    font-size: 36px;
    font-weight: bold;
    color: #f5c518;
    margin-right: 10px;
}

.review-stars i {
    font-size: 24px;
    color: #f5c518;
    margin-right: 5px;
}

.review-count {
    font-size: 14px;
    color: #666;
}

.product-detail-review-list {
    margin-bottom: 20px;
}

.review-item {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.review-user {
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.review-stars i {
    font-size: 20px;
    color: #f5c518;
}

.review-content {
    margin-top: 5px;
    font-size: 14px;
    color: #333;
}

.product-detail-review-form {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.form-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.product-detail-review-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.review-form-stars i {
    font-size: 24px;
    color: #ddd; /* Default color for unselected stars */
    cursor: pointer;
    margin-right: 5px;
    transition: color 0.2s ease-in-out;
}

/* Updated CSS for active and hover states */
.review-form-stars i.active,
.review-form-stars i.hover {
    color: #f5c518; /* Yellow color for active/hover stars */
}

/* Styles for disabled form elements */
.product-detail-review-form textarea:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
}
.product-detail-review-form button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

.btn-review-send {
    background-color: #f44336;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.btn-review-send:hover {
    background-color: #d32f2f;
}
</style>
<?php include '../includes/footer.php'; ?>