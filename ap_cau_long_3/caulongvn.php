<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CauLongVN - Trang chủ</title>
  <link rel="stylesheet" href="/ap_cau_long/css/style.css" />
</head>
<body>

  <?php include 'templates/header.php'; ?>
  <?php include 'templates/banner.php'; ?>

  <?php
include 'connect.php';

$sql = "SELECT * FROM noi_bat";
$result = $conn->query($sql);
?>

<section class="featured-products">
  <h2>Sản phẩm nổi bật</h2>
  <div class="product-grid">
    <?php
    if ($result && $result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
    ?>
      <div class="product">
        <a href="/ap_cau_long/templates/sanpham/chitiet.php?id=<?php echo $row['id']; ?>&type=noi_bat" style="text-decoration: none;">
          <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
          <h3><?php echo htmlspecialchars($row['title']); ?></h3>
        </a>

        <p><?php echo htmlspecialchars($row['description']); ?></p>
      </div>
    <?php
      endwhile;
    else:
      echo "<p>Không có sản phẩm nổi bật nào.</p>";
    endif;

    $conn->close();
    ?>
  </div>
</section>


  <?php include 'templates/footer.php'; ?>

  <script src="/ap_cau_long/js/main.js"></script>
</body>
</html>
