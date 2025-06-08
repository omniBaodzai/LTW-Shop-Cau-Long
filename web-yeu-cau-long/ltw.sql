-- Tạo schema (cơ sở dữ liệu)
CREATE DATABASE IF NOT EXISTS ltw_shop_cau_long;

-- Sử dụng schema vừa tạo
USE ltw_shop_cau_long;

-- Tạo bảng `users`
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- Auto-increment ID
    name VARCHAR(255) NOT NULL,              -- User's name
    email VARCHAR(255) NOT NULL UNIQUE,      -- Email (unique)
    phone VARCHAR(15) NOT NULL,              -- Phone number
    password VARCHAR(255) NOT NULL,          -- Hashed password
    address VARCHAR(255) DEFAULT NULL,       -- User's street address
    city VARCHAR(100) DEFAULT NULL,          -- User's city/province
    district VARCHAR(100) DEFAULT NULL,      -- User's district
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Creation timestamp
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY, -- ID sản phẩm (khóa chính)
    name VARCHAR(255) NOT NULL,        -- Tên sản phẩm
    image VARCHAR(255) NOT NULL,       -- Đường dẫn hình ảnh sản phẩm
    price DECIMAL(10, 2) NOT NULL,     -- Giá sản phẩm
    old_price DECIMAL(10, 2),          -- Giá gốc (nếu có)
    sku VARCHAR(50) NOT NULL,          -- Mã sản phẩm
    brand VARCHAR(100),                -- Thương hiệu sản phẩm
    category VARCHAR(100) NOT NULL,    -- Danh mục sản phẩm
    warranty VARCHAR(50),              -- Thời gian bảo hành
    stock INT NOT NULL DEFAULT 0,      -- Số lượng tồn kho
    description TEXT,                  -- Mô tả sản phẩm
    specs TEXT,                        -- Thông số kỹ thuật
    promotion VARCHAR(255) DEFAULT NULL -- Ưu đãi sản phẩm (có thể để trống)
);

INSERT INTO products 
(name, image, price, old_price, sku, brand, category, warranty, stock, description, specs, promotion) 
VALUES
(
    'Vợt cầu lông Yonex Astrox 100ZZ',
    'uploads/astrox100zz.jpg',
    3200000.00,
    3500000.00,
    'VT-YONEX-100ZZ',
    'Yonex',
    'Huong Dan Mua Hang',
    '12 tháng',
    10,
    'Vợt cao cấp dành cho VĐV chuyên nghiệp.',
    'Trọng lượng: 83g, Độ cứng: cứng, Cán: G5',
    'Tặng bao vợt + dây căng'
),
(
    'Vợt cầu lông Lining Turbo X90',
    'uploads/lining-x90.jpg',
    900000.00,
    1000000.00,
    'VT-LINING-X90',
    'Lining',
    'Huong Dan Mua Hang',
    '6 tháng',
    15,
    'Dành cho người chơi trung cấp',
    'Trọng lượng: 85g, Cán: S2',
    NULL
),
(
    'Vợt cầu lông Apacs Z-Ziggler',
    'uploads/apacs-zziggler.jpg',
    600000.00,
    NULL,
    'VT-APACS-ZZ',
    'Apacs',
    'Huong Dan Mua Hang',
    '6 tháng',
    20,
    'Giá rẻ, phù hợp cho người mới chơi',
    'Khung: Graphite + Nano, Trọng lượng: 87g',
    'Miễn phí vận chuyển toàn quốc'
);

INSERT INTO products (name, image, price, old_price, sku, brand, category, warranty, stock, description, specs, promotion) VALUES
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X', 'VNB', 'Vợt Cầu Lông', '6 tháng', 100, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông Yonex Astrox 88D', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 2500000.00, NULL, 'YXASTROX88D', 'Yonex', 'Vợt Cầu Lông', '6 tháng', 50, 'Vợt Cầu Lông Yonex Astrox 88D là siêu phẩm dành cho những người chơi thích lối đánh tấn công, đặc biệt mạnh mẽ trong các pha đập cầu và phòng thủ phản công. Công nghệ Rotational Generator System giúp cân bằng trọng lượng, tạo ra những cú đánh uy lực và liên tục.', 'Chất liệu: H.M. GRAPHITE + Tungsten + Namd; Trọng lượng: 3U (85-89g), 4U (80-84g); Điểm cân bằng: Nặng đầu; Độ cứng: Cứng; Lực căng tối đa: 12.5 kg', 'Giảm 5% khi mua kèm bao vợt Yonex'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C', 'VNB', 'Vợt Cầu Lông', '6 tháng', 120, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D', 'VNB', 'Vợt Cầu Lông', '6 tháng', 80, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X2', 'VNB', 'Vợt Cầu Lông', '6 tháng', 90, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH', 'VNB', 'Vợt Cầu Lông', '6 tháng', 70, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C2', 'VNB', 'Vợt Cầu Lông', '6 tháng', 110, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D2', 'VNB', 'Vợt Cầu Lông', '6 tháng', 95, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X3', 'VNB', 'Vợt Cầu Lông', '6 tháng', 105, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH2', 'VNB', 'Vợt Cầu Lông', '6 tháng', 75, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C3', 'VNB', 'Vợt Cầu Lông', '6 tháng', 115, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D3', 'VNB', 'Vợt Cầu Lông', '6 tháng', 85, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X4', 'VNB', 'Vợt Cầu Lông', '6 tháng', 100, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH3', 'VNB', 'Vợt Cầu Lông', '6 tháng', 70, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C4', 'VNB', 'Vợt Cầu Lông', '6 tháng', 120, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D4', 'VNB', 'Vợt Cầu Lông', '6 tháng', 80, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X5', 'VNB', 'Vợt Cầu Lông', '6 tháng', 90, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH4', 'VNB', 'Vợt Cầu Lông', '6 tháng', 70, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C5', 'VNB', 'Vợt Cầu Lông', '6 tháng', 110, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D5', 'VNB', 'Vợt Cầu Lông', '6 tháng', 95, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X6', 'VNB', 'Vợt Cầu Lông', '6 tháng', 105, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH5', 'VNB', 'Vợt Cầu Lông', '6 tháng', 75, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C6', 'VNB', 'Vợt Cầu Lông', '6 tháng', 115, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D6', 'VNB', 'Vợt Cầu Lông', '6 tháng', 85, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X7', 'VNB', 'Vợt Cầu Lông', '6 tháng', 100, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH6', 'VNB', 'Vợt Cầu Lông', '6 tháng', 70, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C7', 'VNB', 'Vợt Cầu Lông', '6 tháng', 120, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D7', 'VNB', 'Vợt Cầu Lông', '6 tháng', 80, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X8', 'VNB', 'Vợt Cầu Lông', '6 tháng', 90, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH7', 'VNB', 'Vợt Cầu Lông', '6 tháng', 70, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C8', 'VNB', 'Vợt Cầu Lông', '6 tháng', 110, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D8', 'VNB', 'Vợt Cầu Lông', '6 tháng', 95, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X9', 'VNB', 'Vợt Cầu Lông', '6 tháng', 105, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH8', 'VNB', 'Vợt Cầu Lông', '6 tháng', 75, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C9', 'VNB', 'Vợt Cầu Lông', '6 tháng', 115, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D9', 'VNB', 'Vợt Cầu Lông', '6 tháng', 85, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB'),
('Vợt Cầu Lông VNB V88 Xanh Chính Hãng', 'https://contents.mediadecathlon.com/p2390909/sq/k$25aaf71682821f2ec8cf537b2bfc25ea/b%E1%BB%99-v%E1%BB%A3t-c%E1%BA%A7u-l%C3%B4ng-br-190-cho-ng%C6%B0%E1%BB%9Di-l%E1%BB%9Bn-v%C3%A0ng-cam-kuikma-8736755.jpg?f=480x480&format=auto', 638000.00, NULL, 'VNBV88X10', 'VNB', 'Vợt Cầu Lông', '6 tháng', 100, 'Vợt Cầu Lông VNB V88 Xanh Chính Hãng là lựa chọn tuyệt vời cho người chơi phong trào và bán chuyên, mang lại sự cân bằng hoàn hảo giữa sức mạnh và khả năng kiểm soát. Thiết kế màu xanh nổi bật cùng chất liệu bền bỉ giúp người chơi tự tin trên sân.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Tặng kèm 1 quấn cán VNB'),
('Vợt Cầu Lông VNB V200i Hồng', 'https://cdn.shopvnb.com/img/300x300/uploads/san_pham/vot-cau-long-vnb-v200i-hong-3.webp', 529000.00, NULL, 'VNBV200IH9', 'VNB', 'Vợt Cầu Lông', '6 tháng', 70, 'Vợt Cầu Lông VNB V200i Hồng nổi bật với gam màu nữ tính và trọng lượng nhẹ, rất phù hợp cho phái nữ và những người chơi có lực cổ tay yếu. Vợt dễ dàng kiểm soát và hỗ trợ tốt cho các pha ve cầu, bỏ nhỏ.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V88 Cam Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v88-cam-chinh-hang-1.webp', 638000.00, NULL, 'VNBV88C10', 'VNB', 'Vợt Cầu Lông', '6 tháng', 120, 'Vợt Cầu Lông VNB V88 Cam Chính Hãng sở hữu màu sắc rực rỡ và cá tính, phù hợp với người chơi yêu thích sự nổi bật. Vợt có độ linh hoạt cao, dễ dàng kiểm soát, hỗ trợ tốt cho cả tấn công và phòng thủ.', 'Chất liệu: Carbon Fiber; Trọng lượng: 4U (80-84g); Điểm cân bằng: 295 ± 3mm; Độ cứng: Trung bình; Lực căng tối đa: 11.5 kg', 'Miễn phí căng dây lần đầu'),
('Vợt Cầu Lông VNB V200 Đỏ Chính Hãng', 'https://cdn.shopvnb.com/uploads/san_pham/vot-cau-long-vnb-v200-do-chinh-hang-1.webp', 529000.00, NULL, 'VNBV200D10', 'VNB', 'Vợt Cầu Lông', '6 tháng', 80, 'Vợt Cầu Lông VNB V200 Đỏ Chính Hãng là mẫu vợt phổ thông, dễ tiếp cận, lý tưởng cho người mới bắt đầu hoặc người chơi giải trí. Màu đỏ năng động cùng cảm giác đánh ổn định giúp bạn dễ dàng làm quen với bộ môn cầu lông.', 'Chất liệu: Hợp kim Carbon; Trọng lượng: 5U (75-79g); Điểm cân bằng: 290 ± 3mm; Độ cứng: Dẻo; Lực căng tối đa: 10.5 kg', 'Tặng 1 bộ dây căng VNB');
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,        -- ID đánh giá (khóa chính)
    product_id INT NOT NULL,                  -- ID sản phẩm (khóa ngoại)
    user_name VARCHAR(255) NOT NULL,          -- Tên người dùng
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5), -- Số sao (1-5)
    content TEXT NOT NULL,                    -- Nội dung đánh giá
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Thời gian tạo đánh giá
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE -- Liên kết với bảng products
);

INSERT INTO reviews (product_id, user_name, rating, content)
VALUES
(1, 'Nguyễn Văn A', 5, 'Vợt nhẹ, đánh rất sướng, shop giao hàng nhanh!'),
(1, 'Trần Thị B', 4, 'Màu đẹp, chất lượng tốt, sẽ ủng hộ tiếp!'),
(2, 'Lê Văn C', 3, 'Sản phẩm ổn, nhưng giao hàng hơi chậm.');

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  full_name VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(255),
  address TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  district VARCHAR(100) NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  total_price DECIMAL(15,2) NOT NULL,
  shipping_fee DECIMAL(15,2) NOT NULL,
  final_total DECIMAL(15,2) NOT NULL,
  order_date DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  serial_number VARCHAR(100),        -- Mã số seri để kiểm tra bảo hành
  price DECIMAL(15,2) NOT NULL,
  quantity INT NOT NULL,
  warranty_expire_date DATE,                 -- Ngày hết hạn bảo hành
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);




