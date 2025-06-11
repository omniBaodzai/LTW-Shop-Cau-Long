<?php
include '../connect.php';
include '../includes/header.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

$whereClauses = [];
if ($keyword !== '') {
    $keyword_escaped = $conn->real_escape_string($keyword);
    $whereClauses[] = "name LIKE '%$keyword_escaped%'";
}

switch ($priceFilter) {
    case 'under500':
        $whereClauses[] = "price < 500000";
        break;
    case '500to1m':
        $whereClauses[] = "price >= 500000 AND price <= 1000000";
        break;
    case '1to2m':
        $whereClauses[] = "price > 1000000 AND price <= 2000000";
        break;
    case '2to3m':
        $whereClauses[] = "price > 2000000 AND price <= 3000000";
        break;
    case 'above3m':
        $whereClauses[] = "price > 3000000";
        break;
}

$whereSQL = '';
if (count($whereClauses) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

// First, count total products for pagination
$countSql = "SELECT COUNT(id) AS total FROM products $whereSQL";
$countResult = $conn->query($countSql);
$totalProducts = 0;
if ($countResult && $countResult->num_rows > 0) {
    $row = $countResult->fetch_assoc();
    $totalProducts = $row['total'];
}

$perPage = 20;
$totalPages = ceil($totalProducts / $perPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;

$offset = ($currentPage - 1) * $perPage;

// Then, fetch products for the current page
$sql = "SELECT id, image, name, price FROM products $whereSQL ORDER BY id DESC LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'img' => $row['image'],
            'name' => $row['name'],
            'price' => number_format($row['price'], 0, ',', '.') . ' đ'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kết quả tìm kiếm</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <style>
    .badminton-filter .filter-list a {
      display: block;
      padding: 8px 12px;
      color: #555;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.2s ease, color 0.2s ease;
      font-size: 15px;
    }
    .badminton-filter .filter-list a:hover {
      background-color: rgb(237, 241, 245);
      color: rgb(202, 89, 37);
    }
    .badminton-filter .filter-list a.active {
      background-color: rgb(241, 235, 228);
      color: rgb(221, 158, 86);
      font-weight: bold;
    }
    .badminton-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        flex-wrap: wrap; /* Allows pagination to wrap on smaller screens */
    }
    .badminton-pagination a,
    .badminton-pagination span {
        margin: 0 3px;
        padding: 5px 10px;
        background-color: #eee;
        cursor: pointer;
        border-radius: 3px;
        text-decoration: none;
        color: #333;
        display: inline-block;
        min-width: 30px; /* Ensure buttons have a minimum width */
        text-align: center;
        box-sizing: border-box; /* Include padding and border in the element's total width and height */
    }
    .badminton-pagination a.active {
        background-color: #ca5925;
        color: #fff;
        font-weight: bold;
    }
    .badminton-pagination a[disabled] {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .badminton-pagination .page-ellipsis {
        background-color: transparent;
        cursor: default;
    }
    .badminton-item {
      margin-bottom: 20px;
    }
    /* Styles to ensure the badminton-list always has a consistent grid layout */
    .badminton-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Adjust minmax as needed */
        gap: 20px; /* Space between items */
    }
    .badminton-item.empty {
        visibility: hidden; /* Hide empty items but preserve their space for grid consistency */
    }
  </style>
</head>
<body>

<main class="badminton-main">
  <div class="badminton-container">
    <nav class="badminton-breadcrumb">
      <a href="../index.php">Trang chủ</a>
      <span class="breadcrumb-sep">›</span>
      <span>Kết quả tìm kiếm</span>
    </nav>
    <div class="badminton-body">
      <aside class="badminton-sidebar">
        <section class="badminton-filter">
          <div class="filter-title">CHỌN MỨC GIÁ</div>
          <ul class="filter-list">
            <li><a href="?q=<?= urlencode($keyword) ?>&price=under500" class="<?= $priceFilter === 'under500' ? 'active' : '' ?>">Giá dưới 500.000đ</a></li>
            <li><a href="?q=<?= urlencode($keyword) ?>&price=500to1m" class="<?= $priceFilter === '500to1m' ? 'active' : '' ?>">500.000đ - 1 triệu</a></li>
            <li><a href="?q=<?= urlencode($keyword) ?>&price=1to2m" class="<?= $priceFilter === '1to2m' ? 'active' : '' ?>">1 - 2 triệu</a></li>
            <li><a href="?q=<?= urlencode($keyword) ?>&price=2to3m" class="<?= $priceFilter === '2to3m' ? 'active' : '' ?>">2 - 3 triệu</a></li>
            <li><a href="?q=<?= urlencode($keyword) ?>&price=above3m" class="<?= $priceFilter === 'above3m' ? 'active' : '' ?>">Giá trên 3 triệu</a></li>
            <li><a href="?q=<?= urlencode($keyword) ?>" class="<?= $priceFilter === '' ? 'active' : '' ?>">Tất cả giá</a></li>
          </ul>
        </section>
      </aside>
      <section class="badminton-content">
        <div class="badminton-content-header">
          <h1>Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword) ?>"</h1>
        </div>

        <div class="badminton-list">
            <?php if (empty($products)): ?>
                <p style="padding:16px; font-style: italic; width: 100%;">Không tìm thấy sản phẩm phù hợp.</p>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <div class="badminton-item">
                        <div class="badminton-imgbox">
                            <a href="../pages/chi-tiet-san-pham.php?id=<?= $p['id'] ?>">
                                <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>">
                                <span class="badminton-badge">Premium</span>
                            </a>
                        </div>
                        <div class="badminton-name">
                            <a href="../pages/chi-tiet-san-pham.php?id=<?= $p['id'] ?>"><?= $p['name'] ?></a>
                        </div>
                        <div class="badminton-price"><?= $p['price'] ?></div>
                    </div>
                <?php endforeach; ?>
                <?php
                // Add empty divs to maintain grid layout if fewer than perPage items
                $remainingItems = $perPage - count($products);
                for ($i = 0; $i < $remainingItems; $i++) {
                    echo '<div class="badminton-item empty"></div>';
                }
                ?>
            <?php endif; ?>
        </div>

        <div class="badminton-pagination">
            <?php if ($totalPages > 1): ?>
                <?php
                $currentParams = $_GET; // Get current URL parameters
                unset($currentParams['page']); // Remove page param to build new links

                $queryString = http_build_query($currentParams); // Rebuild query string without page
                ?>

                <a href="?<?= $queryString . '&page=1' ?>" class="page-btn first" <?= $currentPage === 1 ? 'disabled' : '' ?> title="Trang đầu"><i class="ri-arrow-left-double-line"></i></a>
                <a href="?<?= $queryString . '&page=' . max(1, $currentPage - 1) ?>" class="page-btn prev" <?= $currentPage === 1 ? 'disabled' : '' ?> title="Trang trước"><i class="ri-arrow-left-s-line"></i></a>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                if ($startPage > 1) {
                    echo '<a href="?' . $queryString . '&page=1" class="page-btn">1</a>';
                    if ($startPage > 2) echo '<span class="page-ellipsis">...</span>';
                }

                for ($i = $startPage; $i <= $endPage; $i++) {
                    $activeClass = ($i === $currentPage) ? 'active' : '';
                    echo '<a href="?' . $queryString . '&page=' . $i . '" class="page-btn ' . $activeClass . '">' . $i . '</a>';
                }

                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) echo '<span class="page-ellipsis">...</span>';
                    echo '<a href="?' . $queryString . '&page=' . $totalPages . '" class="page-btn">' . $totalPages . '</a>';
                }
                ?>

                <a href="?<?= $queryString . '&page=' . min($totalPages, $currentPage + 1) ?>" class="page-btn next" <?= $currentPage === $totalPages ? 'disabled' : '' ?> title="Trang sau"><i class="ri-arrow-right-s-line"></i></a>
                <a href="?<?= $queryString . '&page=' . $totalPages ?>" class="page-btn last" <?= $currentPage === $totalPages ? 'disabled' : '' ?> title="Trang cuối"><i class="ri-arrow-right-double-line"></i></a>
            <?php endif; ?>
        </div>
      </section>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>