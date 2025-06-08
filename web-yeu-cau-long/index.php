<?php 
include './includes/header.php'; 
include './connect.php'; // Kết nối cơ sở dữ liệu
?>
<link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link rel="stylesheet" href="./assets/css/style.css">
<script src="./assets/js/script.js"></script>
<section class="banner">
      <h1>CỬA HÀNG CẦU LÔNG SỐ 1 VIỆT NAM</h1>
      <p>Chuyên nghiệp từ cây vợt đến đôi giày<br />Đồng hành cùng đam mê</p>
      <div class="btn-group">
        <button class="btn shop"><a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/vot-cau-long.php">MUA NGAY</a></button>
        <button class="btn explore">KHÁM PHÁ THÊM</button>
      </div>
    </section>

    <!-- Nội dung chính -->
    <main>
      <!-- MAIN FEATURE SECTION -->
      <section class="main-features">
        <div class="main-features-container">
          <div class="feature-box">
            <div class="feature-icon"><i class="ri-truck-line"></i></div>
            <div class="feature-title">Vận chuyển <span>TOÀN QUỐC</span></div>
            <div class="feature-desc">Thanh toán khi nhận hàng</div>
          </div>
          <div class="feature-box">
            <div class="feature-icon"><i class="ri-shield-check-line"></i></div>
            <div class="feature-title">Bảo đảm <span>CHẤT LƯỢNG</span></div>
            <div class="feature-desc">Sản phẩm bảo đảm chất lượng.</div>
          </div>
          <div class="feature-box">
            <div class="feature-icon"><i class="ri-bank-card-line"></i></div>
            <div class="feature-title">Tiến hành <span>THANH TOÁN</span></div>
            <div class="feature-desc">Với nhiều <span>PHƯƠNG THỨC</span></div>
          </div>
          <div class="feature-box">
            <div class="feature-icon"><i class="ri-refresh-line"></i></div>
            <div class="feature-title">Đổi sản phẩm <span>MỚI</span></div>
            <div class="feature-desc">nếu sản phẩm lỗi</div>
          </div>
        </div>
      </section>

      <!-- NEW PRODUCT SECTION -->
<section class="product-section">
    <div class="product-section-header">
        <h2 class="section-title">Sản phẩm <span>mới</span></h2>
        <div class="category-tabs">
            <button class="tab active" category="all">Tất cả</button>
            <button class="tab" category="Vợt Cầu Lông">Vợt Cầu Lông</button>
            <button class="tab" category="Giày Cầu Lông">Giày Cầu Lông</button>
            <button class="tab" category="Áo Cầu Lông">Áo Cầu Lông</button>
            <button class="tab" category="Váy cầu lông">Váy cầu lông</button>
            <button class="tab" category="Quần Cầu Lông">Quần Cầu Lông</button>
        </div>
    </div>
    <div class="product-list">
        <!-- Sản phẩm sẽ được tải động tại đây -->
    </div>
</section>
      <!-- PRODUCT CATEGORIES SECTION -->

      <section class="category-section">
          <div class="category-section-header">
              <h2 class="section-title">Sản phẩm <span>cầu lông</span></h2>
          </div>
          <div class="category-grid">
              <div class="category-card">
                  <a href="./pages/vot-cau-long.php">
                      <img
                          src="./assets/images/hinh-anh-vot-cau-long1.png"
                          alt="Vợt Cầu Lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">VỢT CẦU LÔNG</div>
                  </a>
              </div>
              <div class="category-card">
                  <a href="./pages/giay-cau-long.php">
                      <img
                          src="./assets/images/giày-cầu-lông-bs-560-lite-cho-nữ-xanh-da-trời-perfly-8651367.avif"
                          alt="Giày Cầu Lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">GIÀY CẦU LÔNG</div>
                  </a>
              </div>
              <div class="category-card">
                  <a href="./pages/ao-cau-long.php">
                      <img
                          src="https://contents.mediadecathlon.com/p2586195/k$e8427e41387943b0ed95a5b3deac2f04/%C3%A1o-thun-c%E1%BA%A7u-l%C3%B4ng-nam-lite-560-xanh-navy-cam-perfly-8806696.jpg?f=1920x0&format=auto"
                          alt="Áo Cầu Lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">ÁO CẦU LÔNG</div>
                  </a>
              </div>
              <div class="category-card">
                  <a href="./pages/quan-cau-long.php">
                      <img
                          src="./assets/images/quần-short-cầu-lông-nam-thoáng-khí-560-xanh-dương-perfly-8647962.avif"
                          alt="Quần Cầu Lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">QUẦN CẦU LÔNG</div>
                  </a>
              </div>
              <div class="category-card">
                  <a href="./pages/vay-cau-long.php">
                      <img
                          src="./assets/images/váy-cầu-lông-nữ-thoáng-mát-lite-560-hồng-perfly-8854171.avif"
                          alt="Váy cầu lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">VÁY CẦU LÔNG</div>
                  </a>
              </div>
              <div class="category-card">
                  <a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/tim-kiem.php?q=Túi+cầu+lông">
                      <img
                          src="./assets/images/túi-thể-thao-35l-essential-đen-xanh-dương-kipsta-8580096.avif"
                          alt="Túi Vợt Cầu Lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">TÚI VỢT CẦU LÔNG</div>
                  </a>
              </div>
              <div class="category-card">
                  <a href="http://localhost/LTW-Shop-Cau-Long/web-yeu-cau-long/pages/tim-kiem.php?q=Balo+cầu+lông">
                      <img
                          src="./assets/images/balo-cau-long-yonex-ba03212ex-xanh-do-gia-cong_1695584495.png"
                          alt="Balo Cầu Lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">BALO CẦU LÔNG</div>
                  </a>
              </div>
              <div class="category-card">
                  <a href="./pages/ong-cau-long.php">
                      <img
                          src="./assets/images/ong_cau_long_pronex_02ca9b63fb99425293ab1a114d5d8362_fff64ac318d94b439c8e7a25dd013651_master.webp"
                          alt="Phụ Kiện Cầu Lông"
                          class="category-image"
                      />
                      <div class="category-overlay"></div>
                      <div class="category-title">ỐNG CẦU LÔNG</div>
                  </a>
              </div>
          </div>
      </section>
            
      
    </main>

<?php include './includes/footer.php'; ?>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".category-tabs .tab");
    const productList = document.querySelector(".product-list");

    // Hàm tải sản phẩm theo danh mục
    const loadProducts = (category) => {
        productList.innerHTML = "<p>Đang tải sản phẩm...</p>"; // Hiển thị thông báo tải

        fetch(`./pages/get-products.php?category=${category}`)
            .then((response) => response.json())
            .then((data) => {
                productList.innerHTML = ""; // Xóa thông báo tải
                if (data.length > 0) {
                    let html = "";
                    data.forEach((product) => {
                        html += `
                            <div class="product-card" data-id="${product.id}" style="cursor:pointer;">
                                <img src="${product.image}" alt="${product.name}" class="product-image" />
                                <h3 class="product-name">${product.name}</h3>
                                <p class="product-price">${product.price.toLocaleString()} đ</p>
                            </div>
                        `;
                    });
                    productList.innerHTML = html;

                    // Gắn sự kiện click chuyển trang chi tiết sản phẩm
                    document.querySelectorAll(".product-card").forEach(card => {
                        card.addEventListener("click", () => {
                            const productId = card.getAttribute("data-id");
                            window.location.href = `pages/chi-tiet-san-pham.php?id=${productId}`;
                        });
                    });
                } else {
                    productList.innerHTML = "<p>Không có sản phẩm nào.</p>";
                }
            })
            .catch((error) => {
                productList.innerHTML = "<p>Đã xảy ra lỗi khi tải sản phẩm.</p>";
                console.error(error);
            });
    };

    // Gắn sự kiện click cho các tab
    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            // Xóa class active khỏi tất cả các tab
            tabs.forEach((t) => t.classList.remove("active"));
            // Thêm class active vào tab được chọn
            tab.classList.add("active");

            // Lấy danh mục từ thuộc tính data-category
            const category = tab.getAttribute("category");
            loadProducts(category);
        });
    });

    // Tải sản phẩm ban đầu (Tất cả)
    loadProducts("all");
});

</script>
</body>
</html> 

