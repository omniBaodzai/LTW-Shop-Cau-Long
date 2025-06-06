<?php
include '../connect.php'; // Kết nối cơ sở dữ liệu
include '../includes/header.php'; // Header

// Lấy giá trị lọc từ URL
$priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

// Điều kiện mặc định
$whereClause = "WHERE category = 'Quần Cầu Lông'";

// Xử lý lọc theo mức giá
if ($priceFilter === 'under500') {
    $whereClause .= " AND price < 500000";
} elseif ($priceFilter === '500to1m') {
    $whereClause .= " AND price >= 500000 AND price <= 1000000";
} elseif ($priceFilter === '1to2m') {
    $whereClause .= " AND price > 1000000 AND price <= 2000000";
} elseif ($priceFilter === '2to3m') {
    $whereClause .= " AND price > 2000000 AND price <= 3000000";
} elseif ($priceFilter === 'above3m') {
    $whereClause .= " AND price > 3000000";
}

// Truy vấn sản phẩm từ cơ sở dữ liệu
$sql = "SELECT id, image, name, price FROM products " . $whereClause; // Select 'id' as well
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'], // Make sure to get the ID for detail page link
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quần Cầu Lông</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
      /* Hover effect for filter links */
      .badminton-filter .filter-list a:hover {
          background-color:rgb(237, 241, 245);
          color:rgb(202, 89, 37);
          text-decoration: none;
      }

      /* Optional: Style for the currently active filter link */
      .badminton-filter .filter-list a.active {
          background-color:rgb(241, 235, 228);
          color: rgb(221, 158, 86);;
          font-weight: bold;
      }
    </style>
</head>
<body>
<main class="badminton-main">
    <div class="badminton-container">
        <nav class="badminton-breadcrumb">
            <a href="../index.php">Trang chủ</a>
            <span class="breadcrumb-sep">›</span>
            <a href="quan-cau-long.php">Quần Cầu Lông</a>
        </nav>
        <div class="badminton-body">
            <aside class="badminton-sidebar">
                <section class="badminton-filter">
                    <div class="filter-title">CHỌN MỨC GIÁ</div>
                    <ul class="filter-list">
                        <li><a href="?price=under500" class="<?= $priceFilter === 'under500' ? 'active' : '' ?>">Giá dưới 500.000đ</a></li>
                        <li><a href="?price=500to1m" class="<?= $priceFilter === '500to1m' ? 'active' : '' ?>">500.000đ - 1 triệu</a></li>
                        <li><a href="?price=1to2m" class="<?= $priceFilter === '1to2m' ? 'active' : '' ?>">1 - 2 triệu</a></li>
                        <li><a href="?price=2to3m" class="<?= $priceFilter === '2to3m' ? 'active' : '' ?>">2 - 3 triệu</a></li>
                        <li><a href="?price=above3m" class="<?= $priceFilter === 'above3m' ? 'active' : '' ?>">Giá trên 3 triệu</a></li>
                    </ul>
                </section>
            </aside>
            <section class="badminton-content">
                <div class="badminton-content-header">
                    <h1>QUẦN CẦU LÔNG</h1>
                </div>

                <div class="badminton-list"></div>

                <div class="badminton-pagination"></div>
            </section>
        </div>
    </div>
</main>

<script>
    // Dữ liệu sản phẩm từ PHP
    const products = <?php echo json_encode($products); ?>;

    // Số sản phẩm mỗi trang
    const perPage = 20;

    // Render sản phẩm cho trang hiện tại
    function renderProducts(page) {
        const list = document.querySelector(".badminton-list");
        list.innerHTML = ""; // Clear existing content

        const start = (page - 1) * perPage;
        const end = start + perPage;
        const items = products.slice(start, end);

        // Render sản phẩm
        items.forEach((p) => { // Removed 'index' from here as 'p.id' is directly available
            list.innerHTML += `
                <div class="badminton-item">
                    <div class="badminton-imgbox">
                        <a href="chi-tiet-san-pham.php?id=${p.id}"> <img src="${p.img}" alt="${p.name}" />
                            <span class="badminton-badge">Premium</span>
                        </a>
                    </div>
                    <div class="badminton-name" title="${p.name}">
                        <a href="chi-tiet-san-pham.php?id=${p.id}">${p.name}</a> </div>
                    <div class="badminton-price">${p.price}</div>
                </div>
            `;
        });

        // Nếu chưa đủ 20 sản phẩm, thêm div trống để giữ bố cục
        for (let i = items.length; i < perPage; i++) {
            list.innerHTML += `<div class="badminton-item empty"></div>`;
        }
    }

    // Render phân trang
    function renderPagination(page, totalPages) {
        const pag = document.querySelector(".badminton-pagination");
        let html = "";

        html += `<a href="#" class="page-btn first" ${page === 1 ? "disabled" : ""} title="Trang đầu"><i class="ri-arrow-left-double-line"></i></a>`;
        html += `<a href="#" class="page-btn prev" ${page === 1 ? "disabled" : ""} title="Trang trước"><i class="ri-arrow-left-s-line"></i></a>`;

        let start = Math.max(1, page - 2);
        let end = Math.min(totalPages, page + 2);

        if (start > 1) {
            html += `<a href="#" class="page-btn">1</a>`;
            if (start > 2) html += `<span class="page-ellipsis">...</span>`;
        }

        for (let i = start; i <= end; i++) {
            html += `<a href="#" class="page-btn${i === page ? " active" : ""}">${i}</a>`;
        }

        if (end < totalPages) {
            if (end < totalPages - 1) html += `<span class="page-ellipsis">...</span>`;
            html += `<a href="#" class="page-btn">${totalPages}</a>`;
        }

        html += `<a href="#" class="page-btn next" ${page === totalPages ? "disabled" : ""} title="Trang sau"><i class="ri-arrow-right-s-line"></i></a>`;
        html += `<a href="#" class="page-btn last" ${page === totalPages ? "disabled" : ""} title="Trang cuối"><i class="ri-arrow-right-double-line"></i></a>`;

        pag.innerHTML = html;

        // Gắn sự kiện click cho các nút
        pag.querySelectorAll(".page-btn").forEach((btn) => {
            if (btn.disabled || btn.classList.contains("active")) return;
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                let num = btn.textContent.trim();
                if (btn.classList.contains("first")) num = 1;
                else if (btn.classList.contains("last")) num = totalPages;
                else if (btn.classList.contains("prev")) num = Math.max(1, page - 1);
                else if (btn.classList.contains("next")) num = Math.min(totalPages, page + 1);
                else num = parseInt(num);

                // Preserve existing URL parameters (like price filter)
                const currentParams = new URLSearchParams(window.location.search);
                currentParams.set('page', num);
                const newUrl = window.location.pathname + '?' + currentParams.toString();
                history.replaceState(null, '', newUrl);

                renderProducts(num);
                renderPagination(num, totalPages);
            });
        });
    }

    // Khi click nút trang:
    function goToPage(page) {
        const totalPages = Math.ceil(products.length / perPage) || 1;
        
        // Preserve existing URL parameters (like price filter)
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.set('page', page);
        const newUrl = window.location.pathname + '?' + currentParams.toString();
        history.replaceState(null, '', newUrl);

        renderProducts(page);
        renderPagination(page, totalPages);
    }

    // Lấy trang từ URL
    function getPageFromURL() {
        const params = new URLSearchParams(window.location.search);
        const page = parseInt(params.get('page'));
        return (page && page > 0) ? page : 1;
    }

    // Hiển thị trang đầu tiên
    goToPage(getPageFromURL());
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
