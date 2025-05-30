<?php
include_once __DIR__ . '/../../connect.php'; // Kết nối CSDL
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Kết quả tìm kiếm</title>
    <link rel="stylesheet" href="/ap_cau_long/css/sanpham/sp.css" />
    <link rel="stylesheet" href="/ap_cau_long/css/style.css" />
    
</head>
<body>

<?php include_once __DIR__ . '/../header.php'; ?>

<main>
    <h1 style="text-align: center;">Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($keyword); ?>"</h1>

    <?php
    if ($keyword === '') {
        echo '<p style="text-align: center;">Vui lòng nhập từ khóa để tìm kiếm.</p>';
    } else {
        // Escape chuỗi để tránh SQL Injection
        $keyword_escaped = $conn->real_escape_string($keyword);

        
        // Truy vấn chỉ tìm trên cột title
        $sql = "
            SELECT id, image, title, price, description, 'ao' AS type FROM ao WHERE title LIKE '%$keyword_escaped%'
            UNION
            SELECT id, image, title, price, description, 'giay' AS type FROM giay WHERE title LIKE '%$keyword_escaped%'
            UNION
            SELECT id, image, title, price, description, 'tui' AS type FROM tui WHERE title LIKE '%$keyword_escaped%'
            UNION
            SELECT id, image, title, price, description, 'vot' AS type FROM vot WHERE title LIKE '%$keyword_escaped%'
        ";

        $result = $conn->query($sql);

        if (!$result) {
            echo '<p style="text-align: center; color: red;">Lỗi truy vấn: ' . $conn->error . '</p>';
        } elseif ($result->num_rows === 0) {
            echo '<p style="text-align: center;">Không tìm thấy sản phẩm nào phù hợp.</p>';
        } else {
            echo '<div class="product-list">';
            while ($row = $result->fetch_assoc()):
            ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="price"><?php echo number_format($row['price'], 0, ',', '.') . ' đ'; ?></div>
                    <div class="description"><?php echo htmlspecialchars($row['description']); ?></div>
                    <a href="/ap_cau_long/templates/sanpham/chitiet.php?id=<?php echo $row['id']; ?>&type=<?php echo $row['type']; ?>" class="btn-buy">Mua ngay</a>
                </div>
            <?php
            endwhile;
            echo '</div>';
        }
    }
    ?>
</main>

<?php include_once __DIR__ . '/../footer.php'; ?>

<script src="/ap_cau_long/js/main.js"></script>
</body>
</html>
