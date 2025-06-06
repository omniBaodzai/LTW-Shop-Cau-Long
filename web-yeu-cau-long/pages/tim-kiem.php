<?php
session_start(); // Bắt đầu session
include '../connect.php'; // Kết nối cơ sở dữ liệu
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kết quả tìm kiếm</title>
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="badminton-main">
    <div class="badminton-container">
        <!-- Breadcrumb -->
        <nav class="badminton-breadcrumb">
            <a href="../index.php">Trang chủ</a>
            <span class="breadcrumb-sep">›</span>
            <span>Kết quả tìm kiếm</span>
        </nav>
        <div class="badminton-body">
            <!-- Sidebar -->
            <aside class="badminton-sidebar">
                <section class="badminton-filter">
                    <div class="filter-title">CHỌN MỨC GIÁ</div>
                    <ul class="filter-list">
                        <li><label><input type="checkbox" /> Giá dưới 500.000đ</label></li>
                        <li><label><input type="checkbox" /> 500.000đ - 1 triệu</label></li>
                        <li><label><input type="checkbox" /> 1 - 2 triệu</label></li>
                        <li><label><input type="checkbox" /> 2 - 3 triệu</label></li>
                        <li><label><input type="checkbox" /> Giá trên 3 triệu</label></li>
                    </ul>
                </section>
            </aside>
            <!-- Main Content -->
            <section class="badminton-content">
                <div class="badminton-content-header">
                    <h1>Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($keyword); ?>"</h1>
                </div>

                <!-- danh sách sản phẩm -->
                <div class="badminton-list"></div>

                <!-- Phân trang -->
                <div class="badminton-pagination"></div>
            </section>
        </div>
    </div>
</main>

<script>
    // Escape chuỗi để tránh SQL Injection
    const keyword = "<?php echo htmlspecialchars($keyword); ?>";

    // Dữ liệu sản phẩm từ PHP
    const products = <?php
        if ($keyword !== '') {
            $keyword_escaped = $conn->real_escape_string($keyword);
            $sql = "SELECT id, image, name, price FROM products WHERE name LIKE '%$keyword_escaped%'";
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
            echo json_encode($products);
        } else {
            echo json_encode([]);
        }
    ?>;

    // Số sản phẩm mỗi trang
    const perPage = 20;

    // Render sản phẩm cho trang hiện tại
    function renderProducts(page) {
        const list = document.querySelector(".badminton-list");
        list.innerHTML = "";
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const items = products.slice(start, end);

        // Render sản phẩm
        items.forEach((p) => {
            list.innerHTML += `
                <div class="badminton-item">
                    <div class="badminton-imgbox">
                        <a href="chi-tiet-san-pham.php?id=${p.id}">
                            <img src="${p.img}" alt="${p.name}" />
                            <span class="badminton-badge">Premium</span>
                        </a>
                    </div>
                    <div class="badminton-name" title="${p.name}">
                        <a href="chi-tiet-san-pham.php?id=${p.id}">${p.name}</a>
                    </div>
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
                goToPage(num);
            });
        });
    }

    // Khi click nút trang:
    function goToPage(page) {
        const totalPages = Math.ceil(products.length / perPage) || 1;
        history.replaceState(null, '', '?page=' + page);
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