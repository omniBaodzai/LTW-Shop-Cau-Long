<?php
include_once '../../connect.php';

if (!isset($conn) || $conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

// Lấy thông tin loại sản phẩm và ID từ URL
$product_type = $_GET['type'] ?? 'vot';
$product_id = intval($_GET['id'] ?? 1);

// Xác định bảng sản phẩm và bảng đánh giá dựa theo loại
switch ($product_type) {
    case 'ao':
        $table = 'ao';
        $review_table = 'danhgia_ao';
        $review_fk = 'ao_id';
        break;
    case 'giay':
        $table = 'giay';
        $review_table = 'danhgia_giay';
        $review_fk = 'giay_id';
        break;
    case 'tui':
        $table = 'tui';
        $review_table = 'danhgia_tui';
        $review_fk = 'tui_id';
        break;
    case 'noi_bat':
        $table = 'noi_bat';
        $review_table = 'danhgia_noi_bat';
        $review_fk = 'noi_bat_id';
        break;
    case 'vot':
    default:
        $table = 'vot';
        $review_table = 'danhgia_vot';
        $review_fk = 'vot_id';
        break;
}

// --- Xử lý đánh giá nếu có POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = trim($_POST['reviewer_name'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review_text'] ?? '');
    $review_date = date('Y-m-d');

    if ($reviewer_name && $rating >= 1 && $rating <= 5 && $review_text) {
        $insert_sql = "INSERT INTO $review_table ($review_fk, reviewer_name, rating, review_text, review_date) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        if ($stmt_insert) {
            $stmt_insert->bind_param("isiss", $product_id, $reviewer_name, $rating, $review_text, $review_date);
            if ($stmt_insert->execute()) {
                // POST/REDIRECT/GET: ngăn form bị gửi lại khi reload
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                echo "<div style='color:red;'>Lỗi khi thêm đánh giá: " . $stmt_insert->error . "</div>";
            }
            $stmt_insert->close();
        } else {
            echo "<div style='color:red;'>Lỗi khi chuẩn bị truy vấn đánh giá: " . $conn->error . "</div>";
        }
    } else {
        echo "<div style='color:red;'>Vui lòng nhập đầy đủ thông tin và đánh giá hợp lệ.</div>";
    }
}


// --- Lấy thông tin sản phẩm ---
$sql_product = "SELECT id, image, title, price, description, detailed_description, offers_json, discount 
                FROM $table WHERE id = ?";
$stmt_product = $conn->prepare($sql_product);
if (!$stmt_product) {
    die("Lỗi chuẩn bị câu lệnh SQL sản phẩm: " . $conn->error);
}
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$result_product = $stmt_product->get_result();

if ($result_product->num_rows === 0) {
    echo "<div style='text-align:center;padding:50px;font-size:20px;color:#e74c3c;'>Sản phẩm không tồn tại hoặc đã bị xóa.</div>";
    exit();
}

$row = $result_product->fetch_assoc();
$stmt_product->close();

$decoded_offers = json_decode($row['offers_json'], true);
if (!is_array($decoded_offers)) {
    $decoded_offers = [];
}

$original_price = $row['price'] + $row['discount'];

// --- Lấy danh sách đánh giá ---
$sql_reviews = "SELECT reviewer_name, rating, review_text, review_date 
                FROM $review_table WHERE $review_fk = ? ORDER BY review_date DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();

$reviews = [];
$total_rating = 0;
$review_count = 0;

while ($review_row = $result_reviews->fetch_assoc()) {
    $reviews[] = [
        'reviewer_name' => $review_row['reviewer_name'],
        'date' => date('d/m/Y', strtotime($review_row['review_date'])),
        'rating' => $review_row['rating'],
        'text' => $review_row['review_text']
    ];
    $total_rating += $review_row['rating'];
    $review_count++;
}
$stmt_reviews->close();

$rating_avg = $review_count ? round($total_rating / $review_count, 1) : 0.0;

// --- Gói dữ liệu sản phẩm ---
$product = [
    'id' => $row['id'],
    'type' => $product_type,
    'name' => $row['title'],
    'brand' => 'Victor',
    'status' => 'Còn hàng',
    'price' => $row['price'],
    'original_price' => $original_price,
    'discount' => $row['discount'],
    'rating_avg' => $rating_avg,
    'review_count' => $review_count,
    'main_image' => $row['image'],
    'thumbnails' => [$row['image']],
    'offers' => $decoded_offers,
    'description_short' => $row['description'],
    'description_detailed' => $row['detailed_description'],
    'reviews' => $reviews
];

$conn->close();

// --- Hỗ trợ hiển thị ---
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' ₫';
}

function displayStarsForAverage($rating_avg) {
    $full_stars = floor($rating_avg);
    $half_star = ($rating_avg - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;

    return str_repeat('★', $full_stars) . ($half_star ? '½' : '') . str_repeat('☆', $empty_stars);
}

function displayRating($rating) {
    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="/ap_cau_long/css/sanpham/chitiet.css">
    <link rel="stylesheet" href="/ap_cau_long/css/style.css" />
</head>
<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="product-image">
        <img id="mainProductImage" src="<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <div class="thumbnail-gallery">
            <?php if (!empty($product['thumbnails'])): ?>
                <?php foreach ($product['thumbnails'] as $index => $thumbnail_url): ?>
                    <img src="<?= htmlspecialchars($thumbnail_url) ?>" alt="Thumbnail <?= $index + 1 ?>" onclick="changeImage('<?= htmlspecialchars($thumbnail_url) ?>', this)">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-details">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <div class="brand-info">
            Mã: <?= htmlspecialchars($product['id']) ?> <br>
            Thương hiệu: <?= htmlspecialchars($product['brand']) ?> | Tình trạng: <?= htmlspecialchars($product['status']) ?>
        </div>
        <div class="price-info">
            <span class="price"><?= formatPrice($product['price']) ?></span>
            <?php if ($product['discount'] > 0): ?>
                <span class="original-price">Giá niêm yết: <?= formatPrice($product['original_price']) ?></span>
                <span class="discount-badge">-<?= formatPrice($product['discount']) ?></span>
            <?php endif; ?>
        </div>

        <div class="rating-summary">
            <span class="stars"><?= displayStarsForAverage($product['rating_avg']) ?></span>
            <span class="average-rating"><?= number_format($product['rating_avg'], 1) ?>/5</span>
            <span class="review-count">(<?= $product['review_count'] ?> đánh giá)</span>
        </div>

        <div class="offers">
            <h3><span style="font-size: 1.3em;">✨</span> ƯU ĐÃI ĐẶC BIỆT KHI MUA <?= htmlspecialchars($product['name']) ?></h3>
            <ul>
                <?php if (!empty($product['offers'])): ?>
                    <?php foreach ($product['offers'] as $offer): ?>
                        <li><?= htmlspecialchars($offer) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Hiện không có ưu đãi đặc biệt nào.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="quantity-selector">
            <button onclick="updateQuantity(-1)">-</button>
            <input type="text" id="productQuantity" value="1" readonly>
            <button onclick="updateQuantity(1)">+</button>
        </div>

        <div class="action-buttons">
            <form action="/ap_cau_long/templates/header/thanhtoan.php" method="POST" style="display:inline-block;">
                <input type="hidden" name="action" value="buy_now">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                <input type="hidden" name="product_type" value="<?= htmlspecialchars($product['type']) ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['price']) ?>">
                <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['main_image']) ?>">
                <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
                <button type="submit" class="buy-now-button">MUA NGAY</button>
            </form>

            <form action="/ap_cau_long/templates/header/giohang.php" method="POST" style="display:inline-block;">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                <input type="hidden" name="product_type" value="<?= htmlspecialchars($product['type']) ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['price']) ?>">
                <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['main_image']) ?>">
                <input type="hidden" name="quantity" id="addToCartQuantity" value="1">
                <button type="submit" class="add-to-cart-button">THÊM VÀO GIỎ HÀNG</button>
            </form>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 30px; display: block;">
    <h2 class="section-header">Mô tả sản phẩm</h2>
    <div class="description-content">
        <p><?= htmlspecialchars($product['description_short']) ?></p>
        <?= $product['description_detailed'] ?>
    </div>

    <h2 class="section-header">Đánh giá sản phẩm</h2>
    <div class="reviews-section">
        <?php if (!empty($product['reviews'])): ?>
            <?php foreach ($product['reviews'] as $review): ?>
                <div class="review-item">
                    <span class="reviewer-name"><?= htmlspecialchars($review['reviewer_name']) ?></span>
                    <span class="review-date"><?= htmlspecialchars($review['date']) ?></span>
                    <div class="rating"><?= displayRating($review['rating']) ?></div>
                    <p class="review-text"><?= htmlspecialchars($review['text']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        <?php endif; ?>
    </div>
<form class="review-form" method="POST" action="">
  <h2 class="section-header">Đánh giá & nhận xét sản phẩm của bạn</h2>
  
  <label for="reviewer_name">Tên của bạn</label>
  <input type="text" id="reviewer_name" name="reviewer_name" required placeholder="Nhập tên của bạn">

  <label>Đánh giá sao</label>
  <div class="rating">
    <input type="radio" id="star5" name="rating" value="5" required><label for="star5">&#9733;</label>
    <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
    <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
    <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
    <input type="radio" id="star1" name="rating" value="1"><label for="star1">&#9733;</label>
  </div>

  <label for="review_text">Nhận xét</label>
  <textarea id="review_text" name="review_text" rows="5" required placeholder="Viết nhận xét của bạn..."></textarea>

  <button type="submit" name="submit_review">Gửi đánh giá</button>
</form>

</div>



<script>
    function updateQuantity(change) {
        const quantityInput = document.getElementById('productQuantity');
        let currentQuantity = parseInt(quantityInput.value);
        currentQuantity += change;
        if (currentQuantity < 1) {
            currentQuantity = 1;
        }
        quantityInput.value = currentQuantity;

        document.getElementById('buyNowQuantity').value = currentQuantity;
        document.getElementById('addToCartQuantity').value = currentQuantity;
    }

    function changeImage(imageSrc, thumbnailElement) {
        document.getElementById('mainProductImage').src = imageSrc;
        const thumbnails = document.querySelectorAll('.thumbnail-gallery img');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        thumbnailElement.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const firstThumbnail = document.querySelector('.thumbnail-gallery img');
        if (firstThumbnail) {
            firstThumbnail.classList.add('active');
        }
    });
</script>
<script src="/ap_cau_long/js/main.js"></script>
<script src="/ap_cau_long/js/chitiet.js"></script>
<style>
.review-form {
    width: 100%;
    max-width: 100%;
    padding: 30px;
    margin: 40px 0;
    background-color: #fdfdfd;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2c3e50;
    box-sizing: border-box;
}

/* Tiêu đề */
.review-form h2.section-header {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 25px;
    color: #2c3e50;
    text-align: left;
}

/* Label */
.review-form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 15px;
}

/* Input và textarea */
.review-form input[type="text"],
.review-form textarea {
    width: 100%;
    padding: 12px 14px;
    margin-bottom: 20px;
    font-size: 15px;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #fff;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    box-sizing: border-box;
}

.review-form input[type="text"]:focus,
.review-form textarea:focus {
    border-color: #5a87d9;
    box-shadow: 0 0 6px rgba(90, 135, 217, 0.25);
    outline: none;
}

/* Đánh giá sao */
.review-form .rating {
    display: flex;
    flex-direction: row; /* Đặt sao từ trái qua phải */
    justify-content: flex-start;
    gap: 10px;
    font-size: 32px; /* Sao to hơn */
    margin-bottom: 20px;
    direction: ltr;
}


.review-form .rating input[type="radio"] {
    display: none;
}

.review-form .rating label {
    color: #ccc;
    cursor: pointer;
    transition: transform 0.2s ease, color 0.2s ease;
}


.review-form .rating input[type="radio"] {
    display: none;
}

.review-form .rating label {
    color: #ccc;
    cursor: pointer;
    transition: transform 0.2s ease, color 0.2s ease;
}

.review-form .rating label:hover,
.review-form .rating label:hover ~ label {
    color: #f1c40f;
    transform: scale(1.1);
}

/* Highlight khi chọn */
.review-form .rating input[type="radio"]:checked ~ label {
    color: #ccc; /* Reset trước */
}

.review-form input[type="radio"]:checked ~ label {
    color: #f1c40f;
}

/* Button submit */
.review-form button[type="submit"] {
    background-color: #2c3e50;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    padding: 14px 0;
    width: 100%;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    box-shadow: 0 2px 6px rgba(44, 62, 80, 0.25);
}

.review-form button[type="submit"]:hover {
    background-color: #1a252f;
}

/* Responsive (chỉ điều chỉnh padding nếu cần) */
@media (max-width: 768px) {
    .review-form {
        padding: 20px;
    }

    .review-form .rating {
        font-size: 22px;
        gap: 6px;
    }
}


</style>
</body>
</html>