<?php
include_once '../../connect.php';

if (!isset($conn) || $conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

$product_type = isset($_GET['type']) ? $_GET['type'] : 'vot';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

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
    case 'vot':
    default:
        $table = 'vot';
        $review_table = 'danhgia_vot';
        $review_fk = 'vot_id';
        break;
}

$sql_product = "SELECT id, image, title, price, description, detailed_description, offers_json, discount FROM $table WHERE id = ?";
$stmt_product = $conn->prepare($sql_product);
if ($stmt_product === false) {
    die("Lỗi chuẩn bị câu lệnh SQL sản phẩm: " . $conn->error);
}
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$result_product = $stmt_product->get_result();

$product = null;
if ($result_product->num_rows > 0) {
    $row = $result_product->fetch_assoc();

    $decoded_offers = json_decode($row['offers_json'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $decoded_offers = [];
        error_log("Lỗi giải mã JSON ưu đãi sản phẩm ID: " . $row['id'] . " - " . json_last_error_msg());
    }

    $original_price = $row['price'] + $row['discount'];

    // Truy vấn đánh giá
    $sql_reviews = "SELECT reviewer_name, rating, review_text, review_date FROM $review_table WHERE $review_fk = ? ORDER BY review_date DESC";
    $stmt_reviews = $conn->prepare($sql_reviews);
    if ($stmt_reviews === false) {
        die("Lỗi chuẩn bị câu lệnh SQL đánh giá: " . $conn->error);
    }
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

    $rating_avg = ($review_count > 0) ? round($total_rating / $review_count, 1) : 0.0;

    $product = [
        'id' => $row['id'],
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
} else {
    echo "<div style='text-align:center;padding:50px;font-size:20px;color:#e74c3c;'>Sản phẩm không tồn tại hoặc đã bị xóa.</div>";
    exit();
}

$stmt_product->close();
$conn->close();


// --- CÁC HÀM HỖ TRỢ HIỂN THỊ ---

// Hàm định dạng giá tiền Việt Nam Đồng
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' ₫';
}

// Hàm hiển thị sao đánh giá dựa trên điểm trung bình (ví dụ: 4.8 sao)
function displayStarsForAverage($rating_avg) {
    $full_stars = floor($rating_avg);
    $half_star = (($rating_avg - $full_stars) >= 0.5 && ($rating_avg - $full_stars) < 1) ? 1 : 0; // Đảm bảo chỉ có tối đa 1 nửa sao
    $empty_stars = 5 - $full_stars - $half_star;

    $stars_html = '';
    for ($i = 0; $i < $full_stars; $i++) {
        $stars_html .= '★'; // Sao đầy
    }
    if ($half_star) {
        $stars_html .= '½'; // Nửa sao (hoặc biểu tượng khác nếu dùng font-awesome, v.v.)
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars_html .= '☆'; // Sao rỗng
    }
    return $stars_html;
}

// Hàm hiển thị sao cho từng review cụ thể (điểm nguyên từ 1 đến 5)
function displayRating($rating) {
    $stars = '';
    for ($i = 0; $i < 5; $i++) {
        $stars .= ($i < $rating) ? '★' : '☆'; // Nếu điểm nhỏ hơn số sao, là sao đầy, ngược lại là sao rỗng
    }
    return $stars;
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
    </div> <!-- đóng product-image -->

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
            <button class="buy-now-button">MUA NGAY</button>
            <button class="add-to-cart-button">THÊM VÀO GIỎ HÀNG</button>
        </div>
    </div> <!-- đóng product-details -->
</div> <!-- đóng container đầu tiên -->

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
                </div> <!-- đóng review-item -->
            <?php endforeach; ?>
        <?php else: ?>
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        <?php endif; ?>
    </div> <!-- đóng reviews-section -->
</div> <!-- đóng container thứ hai -->



<script src="/ap_cau_long/js/main.js"></script>
<script src="/ap_cau_long/js/chitiet.js"></script>

</body>
</html>