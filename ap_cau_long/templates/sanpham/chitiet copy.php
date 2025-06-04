<?php 
include_once '../../connect.php';

// Kiểm tra xem biến $conn đã được tạo và kết nối thành công chưa
if (!isset($conn) || $conn->connect_error) {
    die("Lỗi: Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra file connect.php và cấu hình XAMPP/WAMP.<br>" . $conn->connect_error);
}

// Lấy ID sản phẩm từ URL (ví dụ: product_detail.php?id=1)
// Mặc định là 1 nếu không có ID trên URL, hoặc nếu ID không hợp lệ.
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Chuẩn bị câu lệnh SQL để lấy thông tin sản phẩm từ bảng vot
$sql_product = "SELECT id, image, title, price, description, detailed_description, offers_json, discount FROM vot WHERE id = ?";
$stmt_product = $conn->prepare($sql_product);

if ($stmt_product === false) {
    // Xử lý lỗi chuẩn bị câu lệnh SQL
    die("Lỗi chuẩn bị câu lệnh SQL sản phẩm: " . $conn->error);
}

$stmt_product->bind_param("i", $product_id); // "i" cho kiểu integer (ID sản phẩm)
$stmt_product->execute();
$result_product = $stmt_product->get_result();

$product = null;
if ($result_product->num_rows > 0) {
    $row = $result_product->fetch_assoc();

    // Giải mã chuỗi JSON từ cột offers_json thành mảng PHP
    $decoded_offers = json_decode($row['offers_json'], true);
    // Kiểm tra xem việc giải mã có thành công không
    if (json_last_error() !== JSON_ERROR_NONE) {
        $decoded_offers = []; // Nếu có lỗi, đặt thành mảng rỗng để tránh lỗi
        // Ghi log lỗi để dễ debug trong môi trường phát triển
        error_log("Lỗi giải mã JSON từ cột offers_json cho sản phẩm ID: " . $row['id'] . " - " . json_last_error_msg());
    }

    // Tính toán giá gốc dựa trên giá hiện tại và giá trị giảm giá từ DB
    $original_price = $row['price'] + $row['discount'];

    // BƯỚC MỚI: TRUY VẤN ĐÁNH GIÁ TỪ BẢNG danhgia_vot
    // Sắp xếp theo ngày đánh giá mới nhất trước
    $sql_reviews = "SELECT reviewer_name, rating, review_text, review_date FROM danhgia_vot WHERE vot_id = ? ORDER BY review_date DESC";
    $stmt_reviews = $conn->prepare($sql_reviews);
    
    if ($stmt_reviews === false) {
        die("Lỗi chuẩn bị câu lệnh SQL đánh giá: " . $conn->error);
    }

    $stmt_reviews->bind_param("i", $product_id); // "i" cho kiểu integer (vot_id)
    $stmt_reviews->execute();
    $result_reviews = $stmt_reviews->get_result();

    $reviews = [];
    $total_rating = 0;
    $review_count = 0;

    while ($review_row = $result_reviews->fetch_assoc()) {
        $reviews[] = [
            'reviewer_name' => $review_row['reviewer_name'],
            'date' => date('d/m/Y', strtotime($review_row['review_date'])), // Định dạng ngày tháng cho dễ đọc
            'rating' => $review_row['rating'],
            'text' => $review_row['review_text']
        ];
        $total_rating += $review_row['rating'];
        $review_count++;
    }
    $stmt_reviews->close(); // Đóng statement đánh giá

    // Tính toán điểm đánh giá trung bình
    $rating_avg = ($review_count > 0) ? round($total_rating / $review_count, 1) : 0.0;


    // Chuẩn bị mảng $product để truyền vào phần hiển thị HTML
    $product = [
        'id' => $row['id'],
        'name' => $row['title'],
        'brand' => 'Victor', // Giá trị tĩnh (có thể thêm cột này vào DB nếu cần)
        'status' => 'Còn hàng', // Giá trị tĩnh (có thể thêm cột này vào DB nếu cần)
        'price' => $row['price'], // Giá hiện tại từ DB
        'original_price' => $original_price, // Giá gốc đã tính
        'discount' => $row['discount'], // Giá trị giảm giá từ DB
        'rating_avg' => $rating_avg, // Điểm đánh giá trung bình đã tính từ các review động
        'review_count' => $review_count, // Số lượng đánh giá đã tính từ các review động
        'main_image' => $row['image'],
        'thumbnails' => [
            $row['image'],
            // Bạn có thể thêm các URL ảnh thumbnail khác tại đây (có thể lấy từ DB nếu có bảng riêng cho ảnh)
            // Ví dụ: '../../anh/vot-cau-long-victor-thruster-ryuga-metallic-cps_thumb1.jpg',
            // '../../anh/vot-cau-long-victor-thruster-ryuga-metallic-cps_thumb2.jpg',
        ],
        'offers' => $decoded_offers, // Mảng ưu đãi đã giải mã từ DB
        'description_short' => $row['description'],
        'description_detailed' => $row['detailed_description'],
        'reviews' => $reviews // Mảng các đánh giá đã lấy từ bảng danhgia_vot
    ];
} else {
    // Xử lý trường hợp không tìm thấy sản phẩm
    echo "<div style='text-align: center; padding: 50px; font-size: 20px; color: #e74c3c;'>Sản phẩm không tồn tại hoặc đã bị xóa.</div>";
    exit(); // Dừng thực thi script nếu không tìm thấy sản phẩm
}

$stmt_product->close(); // Đóng statement sản phẩm
$conn->close(); // Đóng kết nối cơ sở dữ liệu

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
</head>
<body>

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
                <h3><span style="font-size: 1.3em;">✨</span> ƯU ĐÃI ĐẶC BIỆT KHI MUA VỢT</h3>
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
    </div>


<script src="/ap_cau_long/js/chitiet.js"></script>
</body>
</html>