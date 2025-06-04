<?php
include_once __DIR__ . '/../../connect.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Trang sản phẩm giày cầu lông</title>
    <link rel="stylesheet" href="/ap_cau_long/css/sanpham/sp.css" />
    <link rel="stylesheet" href="/ap_cau_long/css/style.css" />
</head>
<body>

<?php include_once __DIR__ . '/../header.php'; ?>

<main>
    <h1 style="text-align: center;">Danh sách sản phẩm giày cầu lông</h1>

    <?php
    $sql = "SELECT * FROM giay";
    $result = $conn->query($sql);

    if (!$result) {
        die("Lỗi truy vấn: " . $conn->error);
    }
    ?>

    <div class="product-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="price"><?php echo number_format($row['price'], 0, ',', '.') . ' đ'; ?></div>
                    <div class="description"><?php echo htmlspecialchars($row['description']); ?></div>
                    <!-- Link đến trang chitiet.php với id của sản phẩm giày -->
                    <a href="/ap_cau_long/templates/sanpham/chitiet.php?id=<?php echo $row['id']; ?>&type=giay" class="btn-buy">Mua ngay</a>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Không có sản phẩm giày cầu lông nào.</p>
        <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/../footer.php'; ?>

<script src="/ap_cau_long/js/main.js"></script>
</body>
</html>
