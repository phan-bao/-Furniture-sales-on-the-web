-- =====================================
-- 1. Tạo Cơ Sở Dữ Liệu và Chuyển Đổi Sử Dụng
-- =====================================
DROP DATABASE IF EXISTS furniture_store;
CREATE DATABASE IF NOT EXISTS furniture_store;
USE furniture_store;

-- =====================================
-- 2. Tạo Các Bảng
-- =====================================

-- 2.1. Tạo bảng Khách Hàng
CREATE TABLE IF NOT EXISTS khach_hang (
    ma_khach_hang INT AUTO_INCREMENT PRIMARY KEY,
    ten_khach_hang VARCHAR(255) NOT NULL,
    mail VARCHAR(255) UNIQUE NOT NULL, -- Đổi mail thành email nếu thích
    so_dien_thoai VARCHAR(50) UNIQUE NOT NULL,
    facebook_id VARCHAR(50) NULL
) ENGINE=InnoDB;

-- 2.2. Tạo bảng Mã Khuyến Mãi
CREATE TABLE IF NOT EXISTS ma_khuyen_mai (
    ten_km VARCHAR(100) PRIMARY KEY,
    gia_tri_so_tien FLOAT,  
    ti_le_phan_tram FLOAT,   
    thoi_gian_bat_dau DATETIME NOT NULL,
    thoi_gian_ket_thuc DATETIME NOT NULL
) ENGINE=InnoDB;

-- 2.3. Tạo bảng Chương Trình Khuyến Mãi
CREATE TABLE IF NOT EXISTS chuong_trinh_khuyen_mai (
    ma_ctkm INT AUTO_INCREMENT PRIMARY KEY,       -- Mã chương trình khuyến mãi
    ten_ctkm VARCHAR(100) UNIQUE NOT NULL,        -- Tên chương trình khuyến mãi
    gia_tri_so_tien FLOAT,                        -- Giá trị giảm giá theo số tiền
    ti_le_phan_tram FLOAT,                        -- Tỷ lệ giảm giá theo phần trăm
    ngay_bat_dau DATETIME NOT NULL,               -- Ngày bắt đầu áp dụng
    ngay_ket_thuc DATETIME NOT NULL               -- Ngày kết thúc áp dụng
) ENGINE=InnoDB;

-- 2.4. Tạo bảng Sản Phẩm
CREATE TABLE IF NOT EXISTS san_pham (
    SKU_san_pham VARCHAR(50) PRIMARY KEY,            
    ten_san_pham VARCHAR(255) NOT NULL,     
    gia DECIMAL(10, 2) NOT NULL,            
    mo_ta TEXT,                             
    noi_dung TEXT,                          
    anh VARCHAR(255),                       
    ten_km VARCHAR(100),   
    ten_ctkm VARCHAR(100),                    
    tag VARCHAR(255),
    FOREIGN KEY (ten_km) REFERENCES ma_khuyen_mai(ten_km) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 2.5. Tạo bảng Phiên Bản Sản Phẩm
CREATE TABLE IF NOT EXISTS phien_ban_san_pham (
    ma_phien_ban INT AUTO_INCREMENT PRIMARY KEY,
    SKU_phien_ban VARCHAR(100) UNIQUE,
    SKU_san_pham VARCHAR(50),
    mau_sac VARCHAR(50),
    vat_lieu VARCHAR(100),
    kich_thuoc VARCHAR(50),
    hinh_anh VARCHAR(255),
    gia DECIMAL(10, 2),
    so_luong_ton_kho INT DEFAULT 0,
    FOREIGN KEY (SKU_san_pham) REFERENCES san_pham(SKU_san_pham) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2.6. Tạo bảng Địa Chỉ
CREATE TABLE IF NOT EXISTS dia_chi (
    ma_dia_chi INT AUTO_INCREMENT PRIMARY KEY,
    ma_khach_hang INT, 
    so_nha VARCHAR(100),
    duong_pho VARCHAR(255),
    quoc_gia VARCHAR(255),
    thanh_pho VARCHAR(255),
    huyen VARCHAR(255),
    xa VARCHAR(255),
    so_dien_thoai_giao_hang VARCHAR(50), -- Thêm trường này
    ten_dia_chi VARCHAR(255), -- Nhãn địa chỉ, ví dụ "Nhà", "Công ty"
    FOREIGN KEY (ma_khach_hang) REFERENCES khach_hang(ma_khach_hang) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2.7. Tạo bảng Đơn Hàng
CREATE TABLE IF NOT EXISTS don_hang (
    ma_don_hang INT AUTO_INCREMENT PRIMARY KEY,
    ma_khach_hang INT, 
    ma_dia_chi INT,
    ngay_dat DATE NOT NULL,
    tinh_trang_thanh_toan VARCHAR(50),
    tinh_trang_giao_hang VARCHAR(50),
    phuong_thuc_thanh_toan VARCHAR(100),
    FOREIGN KEY (ma_khach_hang) REFERENCES khach_hang(ma_khach_hang) ON DELETE CASCADE,
    FOREIGN KEY (ma_dia_chi) REFERENCES dia_chi(ma_dia_chi) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2.8. Tạo bảng Chi Tiết Đơn Hàng
CREATE TABLE IF NOT EXISTS chi_tiet_don_hang (
    ma_chi_tiet_don_hang INT AUTO_INCREMENT PRIMARY KEY,
    ma_don_hang INT NOT NULL,
    SKU_phien_ban VARCHAR(100) NOT NULL,
    soluong INT NOT NULL DEFAULT 1,
    gia DECIMAL(10,2) NOT NULL,
    thanh_tien DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (ma_don_hang) REFERENCES don_hang(ma_don_hang) ON DELETE CASCADE,
    FOREIGN KEY (SKU_phien_ban) REFERENCES phien_ban_san_pham(SKU_phien_ban) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2.9. Tạo bảng Đánh Giá Sản Phẩm
CREATE TABLE IF NOT EXISTS danh_gia_san_pham (
    ma_danh_gia INT AUTO_INCREMENT PRIMARY KEY,
    ma_khach_hang INT, 
    SKU_san_pham VARCHAR(50),
    danh_gia INT NOT NULL CHECK (danh_gia BETWEEN 1 AND 5),
    binh_luan TEXT,
    hinh_anh VARCHAR(1000),
    ngay_danh_gia DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_khach_hang) REFERENCES khach_hang(ma_khach_hang) ON DELETE CASCADE,
    FOREIGN KEY (SKU_san_pham) REFERENCES san_pham(SKU_san_pham) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2.10. Tạo bảng Mô Tả Sản Phẩm
CREATE TABLE IF NOT EXISTS mo_ta_san_pham (
    ma_mo_ta INT AUTO_INCREMENT PRIMARY KEY,
    SKU_san_pham VARCHAR(50),
    mo_ta TEXT NOT NULL,
    FOREIGN KEY (SKU_san_pham) REFERENCES san_pham(SKU_san_pham) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2.11. Tạo bảng Điều Khoản Bảo Hành
CREATE TABLE IF NOT EXISTS dieu_khoan_bao_hanh (
    ma_dieu_khoan INT AUTO_INCREMENT PRIMARY KEY,
    dieu_khoan TEXT NOT NULL,
    thoi_gian_bao_hanh INT NOT NULL
) ENGINE=InnoDB;

-- 2.12. Tạo bảng Contacts
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    viewed TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- 2.13. Tạo bảng Account
CREATE TABLE IF NOT EXISTS account (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    ma_khach_hang INT,
    FOREIGN KEY (ma_khach_hang) REFERENCES khach_hang(ma_khach_hang) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 2.14. Tạo bảng Giỏ Hàng
CREATE TABLE IF NOT EXISTS giohang (
    id_gio_hang INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- ID người dùng (có thể lấy từ session)
    SKU_phien_ban VARCHAR(100) NOT NULL,  -- SKU của phiên bản sản phẩm
    quantity INT DEFAULT 1,  -- Số lượng sản phẩm trong giỏ
    FOREIGN KEY (user_id) REFERENCES account(user_id) ON DELETE CASCADE,  -- Khóa ngoại liên kết với bảng account
    FOREIGN KEY (SKU_phien_ban) REFERENCES phien_ban_san_pham(SKU_phien_ban) ON DELETE CASCADE  -- Đảm bảo SKU_phien_ban là khóa chính hoặc có chỉ mục
) ENGINE=InnoDB;


-- Tạo bảng blogs với cột content
CREATE TABLE blogs
(
    id INT
    AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR
    (255),
    date DATETIME,
    description TEXT,
    content TEXT,
    image_path VARCHAR
    (255),
    status ENUM
    ('Hiển thị', 'Ẩn') DEFAULT 'Ẩn',
    author VARCHAR
    (100),
    category VARCHAR
    (100)
);
-- =====================================
-- 3. Chèn Dữ Liệu Vào Các Bảng
-- =====================================

-- 3.1. Chèn Dữ Liệu vào Bảng `khach_hang`
INSERT IGNORE INTO khach_hang (ten_khach_hang, mail, so_dien_thoai)
VALUES
    ('Lê Văn C', 'levanc@gmail.com', '0923456789'),
    ('Hoàng Thị D', 'hoangthid@gmail.com', '0912123456'),
    ('Phạm Văn E', 'phamvane@gmail.com', '0945678901'),
    ('Nguyễn Thị F', 'nguyenthif@gmail.com', '0978654321'),
    ('Trần Văn G', 'tranvang@gmail.com', '0934567890'),
    ('Đặng Thị H', 'dangthih@gmail.com', '0967890123');

-- 3.2. Chèn Dữ Liệu vào Bảng `ma_khuyen_mai`
INSERT IGNORE INTO ma_khuyen_mai (ten_km, gia_tri_so_tien, ti_le_phan_tram, thoi_gian_bat_dau, thoi_gian_ket_thuc) 
VALUES 
    ('KM001', NULL, 10, '2023-10-01 00:00:00', '2023-10-31 23:59:59'),
    ('KM002', 100000, NULL, '2023-11-01 00:00:00', '2023-11-30 23:59:59'),
    ('KM003', NULL, 20, '2023-12-01 00:00:00', '2023-12-31 23:59:59');

-- 3.3. Chèn Dữ Liệu vào Bảng `chuong_trinh_khuyen_mai`
INSERT IGNORE INTO chuong_trinh_khuyen_mai (ten_ctkm, gia_tri_so_tien, ti_le_phan_tram, ngay_bat_dau, ngay_ket_thuc) 
VALUES 
    ('Tết Nguyên Đán', NULL, 15, '2023-12-01 00:00:00', '2023-12-31 23:59:59'),
    ('Giảm Giá Black Friday', 200000, NULL, '2023-11-15 00:00:00', '2023-11-30 23:59:59'),
    ('Giảm Giá Đặc Biệt', NULL, 25, '2024-01-01 00:00:00', '2024-01-31 23:59:59');

-- 3.4. Chèn Dữ Liệu vào Bảng `san_pham`
INSERT IGNORE INTO san_pham (SKU_san_pham, ten_san_pham, gia, mo_ta, noi_dung, anh, ten_km, ten_ctkm, tag) 
VALUES 
    ('DEK_001', 'Bàn Làm Việc', 1200000, 'Bàn gỗ tự nhiên', 'Chi tiết bàn làm việc', 'DEK001.jpg', 'KM001', 'CTKM001', 'Bàn'),
    ('CHA_001', 'Ghế Văn Phòng', 800000, 'Ghế tựa lưng thoải mái', 'Chi tiết ghế văn phòng', 'CHA001.jpg', 'KM002', 'CTKM002', 'Ghế'),
    ('TAB_002', 'Bàn Cafe', 500000, 'Bàn nhỏ cho không gian cafe', 'Chi tiết bàn cafe', 'TAB002.jpg', NULL, 'CTKM003', 'Bàn'),
    ('BED_003', 'Giường Ngủ', 3000000, 'Giường gỗ tự nhiên chắc chắn', 'Chi tiết giường ngủ', 'BED003.jpg', 'KM003', 'CTKM004', 'Giường'),
    ('SHE_004', 'Kệ Sách', 1500000, 'Kệ sách nhiều ngăn', 'Chi tiết kệ sách', 'SHE004.jpg', NULL, 'CTKM005', 'Kệ'),
    ('WAR_141', 'Tủ Quần Áo', 2500000, 'Tủ quần áo gỗ tự nhiên, nhiều ngăn chứa', 'Chi tiết tủ quần áo', 'WAR141.jpg', NULL, 'CTKM006', 'Tủ'),
    ('WAR_104', 'Tủ Quần Áo', 2200000, 'Tủ quần áo gỗ tự nhiên, nhiều ngăn chứa', 'Chi tiết tủ quần áo', 'WAR104.jpg', NULL, 'CTKM007', 'Tủ'),
    ('BED_174', 'Giường Ngủ 3 Hộc Kéo', 3500000, 'Giường gỗ tự nhiên với 3 hộc kéo tiện lợi', 'Chi tiết giường ngủ 3 hộc kéo', 'BED174.jpg', NULL, 'CTKM008', 'Giường');

-- 3.5. Chèn Dữ Liệu vào Bảng `phien_ban_san_pham`
INSERT IGNORE INTO phien_ban_san_pham (SKU_phien_ban, SKU_san_pham, mau_sac, vat_lieu, kich_thuoc, hinh_anh, gia, so_luong_ton_kho) 
VALUES 
    ('DEK_001_WHI', 'DEK_001', 'Trắng', 'Gỗ MDF', '120x60x75 cm', 'DEK001.jpg', 1200000, 10),
    ('DEK_001_BLK', 'DEK_001', 'Đen', 'Gỗ MDF', '120x60x75 cm', 'DEK001.jpg', 1300000, 8),
    ('CHA_001_BLK', 'CHA_001', 'Đen', 'Lưới', '50x50x100 cm', 'CHA001.jpg', 800000, 15),
    ('CHA_001_WHI', 'CHA_001', 'Trắng', 'Lưới', '50x50x100 cm', 'CHA001.jpg', 850000, 12),
    ('TAB_002_OAK', 'TAB_002', 'Nâu', 'Gỗ tự nhiên', '60x60x45 cm', 'TAB002.jpg', 500000, 20),
    ('TAB_002_WHI', 'TAB_002', 'Trắng', 'Gỗ công nghiệp', '60x60x45 cm', 'TAB002.jpg', 550000, 18),
    ('BED_003_KNG', 'BED_003', 'Vàng', 'Gỗ thông', '200x180x50 cm', 'BED003.jpg', 3000000, 5),
    ('BED_003_BLU', 'BED_003', 'Xanh', 'Gỗ thông', '200x180x50 cm', 'BED003.jpg', 3200000, 4),
    ('SHE_004_BRO', 'SHE_004', 'Nâu', 'Gỗ ép', '80x30x200 cm', 'SHE004.jpg', 1500000, 8),
    ('SHE_004_WHI', 'SHE_004', 'Trắng', 'Gỗ ép', '80x30x200 cm', 'SHE004.jpg', 1550000, 6),
    ('WAR_141_WHI', 'WAR_141', 'Trắng', 'Gỗ MDF', '150x60x200 cm', 'WAR141.jpg', 2500000, 10),
    ('WAR_141_BLU', 'WAR_141', 'Xanh', 'Gỗ MDF', '150x60x200 cm', 'WAR141.jpg', 2700000, 8),
    ('WAR_104_BRO', 'WAR_104', 'Nâu', 'Gỗ tự nhiên', '160x70x220 cm', 'WAR104.jpg', 2500000, 8),
    ('WAR_104_BLK', 'WAR_104', 'Đen', 'Gỗ tự nhiên', '160x70x220 cm', 'WAR104.jpg', 2600000, 6),
    ('BED_174_WHI', 'BED_174', 'Trắng', 'Gỗ MDF', '200x160x50 cm', 'BED174.jpg', 3500000, 10),
    ('BED_174_BLK', 'BED_174', 'Đen', 'Gỗ tự nhiên', '200x160x50 cm', 'BED174.jpg', 3900000, 6);

-- 3.6. Chèn Dữ Liệu vào Bảng `dia_chi`
INSERT INTO dia_chi (ma_khach_hang, so_nha, duong_pho, quoc_gia, thanh_pho, huyen, xa, so_dien_thoai_giao_hang, ten_dia_chi) 
VALUES
    (1, '12', 'Dương Quảng Hàm', 'Vietnam', 'Da Nang', 'Hai Chau', 'Phuong 3', '0123456789', 'Nhà'),
    (2, '24', 'Nguyễn Trãi', 'Vietnam', 'Hai Phong', 'Le Chan', 'Phuong 5', '0987654321', 'Công ty'),
    (3, '56', 'Lê Lợi', 'Vietnam', 'Can Tho', 'Ninh Kieu', 'Phuong 7', '0912345678', 'Nhà'),
    (4, '89', 'Trần Bá Giao', 'Vietnam', 'Ho Chi Minh', 'Quan 3', 'Phuong 9', '0945678123', 'Công ty'),
    (5, '3', 'Phan Chu Trinh', 'Vietnam', 'Hue', 'Phu Hoi', 'Phuong 2', '0934567890', 'Nhà'),
    (1, '45', 'Lý Thường Kiệt', 'Vietnam', 'Da Nang', 'Cam Le', 'Phuong 1', '0956781234', 'Công ty');

-- 3.7. Chèn Dữ Liệu vào Bảng `don_hang`
INSERT IGNORE INTO don_hang (ma_khach_hang, ma_dia_chi, ngay_dat, tinh_trang_thanh_toan, tinh_trang_giao_hang, phuong_thuc_thanh_toan) 
VALUES 
    (1, 1, '2023-10-01', 'Đã thanh toán', 'Đang giao hàng', 'COD'),
    (2, 2, '2023-10-02', 'Chưa thanh toán', 'Chưa giao hàng', 'Chuyển khoản ngân hàng'),
    (3, 3, '2023-10-03', 'Đã thanh toán', 'Đã giao hàng', 'Momo/ZaloPay'),
    (4, 4, '2023-10-04', 'Đã thanh toán', 'Đang giao hàng', 'COD'),
    (5, 5, '2023-10-05', 'Chưa thanh toán', 'Chưa giao hàng', 'Chuyển khoản ngân hàng');

-- 3.8. Chèn Dữ Liệu vào Bảng `chi_tiet_don_hang`
INSERT INTO chi_tiet_don_hang (ma_don_hang, SKU_phien_ban, soluong, gia, thanh_tien) 
VALUES 
    (1, 'DEK_001_WHI', 2, 1200000, 2400000),
    (2, 'CHA_001_BLK', 1, 800000, 800000),
    (3, 'TAB_002_OAK', 3, 500000, 1500000),
    (4, 'BED_003_KNG', 1, 2000000, 2000000),
    (5, 'SHE_004_BRO', 4, 300000, 1200000);

-- 3.9. Chèn Dữ Liệu vào Bảng `danh_gia_san_pham`
INSERT IGNORE INTO danh_gia_san_pham (ma_khach_hang, SKU_san_pham, danh_gia, binh_luan, hinh_anh) 
VALUES 
    (1, 'DEK_001', 5, 'Bàn rất đẹp và chắc chắn.', 'image1.jpg,image2.jpg'),
    (2, 'CHA_001', 4, 'Ghế thoải mái nhưng hơi khó lắp.', 'image3.jpg'),
    (3, 'TAB_002', 3, 'Bàn ổn, nhưng màu sắc khác hình.', 'image4.jpg,image5.jpg'),
    (4, 'BED_003', 5, 'Giường rất bền và đẹp.', NULL),
    (5, 'SHE_004', 4, 'Kệ phù hợp với gia đình.', 'image6.jpg');

-- 3.10. Chèn Dữ Liệu vào Bảng `mo_ta_san_pham`
INSERT IGNORE INTO mo_ta_san_pham (SKU_san_pham, mo_ta) 
VALUES 
    ('DEK_001', 'Bàn làm việc hiện đại, làm từ gỗ tự nhiên, phù hợp văn phòng.'),
    ('CHA_001', 'Ghế văn phòng xoay cao cấp, thiết kế lưng lưới thoáng khí.'),
    ('TAB_002', 'Bàn cafe nhỏ gọn, hiện đại, phù hợp không gian quán cafe.'),
    ('BED_003', 'Giường ngủ chắc chắn, làm từ gỗ tự nhiên cao cấp.'),
    ('SHE_004', 'Kệ sách nhiều ngăn, thiết kế tiện lợi cho gia đình hoặc văn phòng.');

-- 3.11. Chèn Dữ Liệu vào Bảng `dieu_khoan_bao_hanh`
INSERT IGNORE INTO dieu_khoan_bao_hanh (dieu_khoan, thoi_gian_bao_hanh) 
VALUES 
    ('Sản phẩm được bảo hành trong vòng 12 tháng kể từ ngày mua hàng.', 12),
    ('Điều kiện bảo hành: lỗi kỹ thuật từ nhà sản xuất, không áp dụng cho lỗi do người sử dụng.', 12),
    ('Vui lòng giữ hóa đơn mua hàng để được áp dụng bảo hành.', 12);

-- 3.12. Chèn Dữ Liệu vào Bảng `contacts`
INSERT INTO contacts (name, email, phone, message, viewed) 
VALUES
    ('John Doe', 'john@example.com', '123456789', 'I am interested in your furniture.', 0),
    ('Jane Smith', 'jane@example.com', '987654321', 'Can you tell me more about your products?', 1),
    ('Robert Brown', 'robert@example.com', '555666777', 'Do you offer delivery service?', 0);

-- 3.13. Chèn Dữ Liệu vào Bảng `account`
-- Lưu ý: Mật khẩu nên được mã hóa trước khi thêm vào
INSERT INTO account (username, password, ma_khach_hang) 
VALUES
    ('admin', '$2y$10$e0NRF3pIaAdvefr9pF/ZaeF9y5uD51rFPx1GmM5s/8xkK8bJ6cK.G', 1),
    ('user1', '$2y$10$r6SmpyW2U56v.EOFQK/kguEG.TFJAdUEUuiv6hJH6YxMdB6qU9h9y', 2),
    ('user2', '$2y$10$R.tQMK8D2Yxg2Y5f5mCLO.fhNfFiYP72Hy1c0D7ANkkxJcDfn8G5.', 3);

-- 3.14. Chèn Dữ Liệu vào Bảng `giohang`
INSERT INTO giohang (user_id, SKU_phien_ban, quantity) 
VALUES 
    (1, 'DEK_001_WHI', 2),
    (2, 'CHA_001_BLK', 1),
    (3, 'TAB_002_OAK', 3),
    (1, 'BED_003_KNG', 1),
    (2, 'SHE_004_BRO', 4);
-- 1. Tạo bảng `roles` nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Chèn các vai trò vào bảng `roles` nếu chưa tồn tại
INSERT IGNORE INTO roles (role_name) VALUES 
    ('admin'),
    ('employee');

-- 3. Tạo bảng `users` nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Chèn dữ liệu mẫu vào bảng `users`
-- Lưu ý: Mật khẩu đã được mã hóa bằng PHP's password_hash()

INSERT IGNORE INTO users (username, password, role_id)
SELECT 'admin', 
       'admin123',  -- Mật khẩu thuần túy cho 'admin'
       (SELECT id FROM roles WHERE role_name = 'admin')
UNION ALL
SELECT 'employee1', 
       'employee123',  -- Mật khẩu thuần túy cho 'employee1'
       (SELECT id FROM roles WHERE role_name = 'employee')
UNION ALL
SELECT 'employee2', 
       'employee123',  -- Mật khẩu thuần túy cho 'employee2'
       (SELECT id FROM roles WHERE role_name = 'employee');




-- Chèn dữ liệu vào bảng blogs
    INSERT INTO blogs
        (
        title,
        date,
        description,
        content,
        image_path,
        author,
        category
        )
    VALUES
        (
            '10 Ý TƯỞNG VỀ THIẾT KẾ NỘI THẤT PHÒNG KHÁCH CỦA BLISS',
            '2025-05-20',
            'Những màu hot nhất năm 2025 sẽ giúp lý tưởng cho gia đình hoặc không gian tiếp khách trang trọngg.',
            'Những Mẫu Sofa Hiện Đại Phù Hợp Với Mọi Không Gian
Sofa Góc L-Shape Tối Giản:
Thiết kế linh hoạt, phù hợp với không gian nhỏ hoặc phòng khách mở.
Màu sắc trung tính như xám, trắng hoặc be giúp tạo cảm giác sang trọng và thoáng đãng.
Sofa Da Cao Cấp:
Chất liệu da mềm mại, sang trọng, dễ lau chùi, lý tưởng cho gia đình hoặc không gian tiếp khách trang trọng.
Màu đen hoặc nâu sẫm tạo điểm nhấn đẳng cấp.
Sofa Bọc Nhung Sang Trọng:
Chất liệu nhung với các gam màu như xanh lục bảo, đỏ burgundy mang lại cảm giác quý phái.
Kết hợp chân kim loại vàng hoặc đồng để tăng tính hiện đại.
Sofa Đa Năng:
Thiết kế tích hợp hộc chứa đồ hoặc có thể biến thành giường ngủ.
Phù hợp cho căn hộ nhỏ hoặc phòng khách kiêm phòng ngủ.
Sofa Modula Tháo Rời:
Có thể điều chỉnh linh hoạt để thay đổi bố cục phòng khách.
Phù hợp cho những gia đình yêu thích sự đổi mới và sáng tạo.
Sofa Đơn Điệu Với Gối Trang Trí:
Thiết kế tối giản nhưng nổi bật nhờ gối tựa nhiều họa tiết hoặc màu sắc nổi bật.
Tăng sự ấm cúng và cá tính cho không gian sống.
Sofa Chữ U Rộng Rãi:
Lựa chọn hoàn hảo cho phòng khách lớn hoặc gia đình đông người.
Tạo cảm giác gần gũi, kết nối mọi người trong các buổi họp mặt.
Sofa Gỗ Kết Hợp Nệm Hiện Đại:
Sự kết hợp giữa khung gỗ tự nhiên và nệm bọc vải/da mang lại vẻ đẹp tinh tế và thân thiện môi trường.
Dễ dàng phối với các món nội thất khác.
Sofa Thấp Kiểu Nhật:
Thiết kế đơn giản với chiều cao thấp, phù hợp cho những ai yêu thích phong cách Zen.
Kết hợp với bàn trà nhỏ và thảm trải sàn nhẹ nhàng.
Sofa Không Tay Vịn:
Phù hợp với không gian nhỏ, thiết kế không tay vịn tạo cảm giác thoáng và mở rộng diện tích.
Sử dụng màu sắc tươi sáng như xanh lá, vàng để làm nổi bật căn phòng.',
            '../images/blog/img_67659d4f82e2e6.29515171.jpg',
            'Phan Văn Bảo',
            'Tin tức'
    ),
        (
            '10 Ý TƯỞNG THIẾT KẾ PHONG KHÁCH ĐỘC ĐÁO LÀM SANH TRỌNG PHÒNG KHÁCH HIỆN NAY 2025.',
            '2025-06-05',
            'Nội thất BLISS biến phòng khách của bạn thành nơi hoàn hảo cho cả gia đình! .',
            '10 ý tưởng thiết kế phòng khách độc đáo:
Phong cách tối giản (Minimalist Style):
Tông màu trung tính như trắng, xám và beige.
Đồ nội thất đơn giản, không cầu kỳ, tạo cảm giác thoáng đãng.
Điểm nhấn bằng các vật liệu tự nhiên như gỗ, đá.
Phong cách hiện đại (Modern Style):
Sử dụng các màu sắc táo bạo như đen, vàng, hoặc xanh dương làm điểm nhấn.
Kết hợp kính và kim loại để tạo sự bóng bẩy.
Sử dụng đèn LED ẩn trong tường hoặc trần.
Phong cách cổ điển (Classic Style):
Nội thất bọc vải hoa văn, ghế sofa kiểu chesterfield.
Trang trí bằng đèn chùm pha lê và khung tranh cổ.
Sàn lát gỗ tự nhiên hoặc trải thảm họa tiết.
Phong cách Bohemian:
Tích hợp thảm họa tiết sặc sỡ, gối tựa đa sắc.
Kết hợp đồ trang trí thủ công như mây tre, đan lát.
Thêm cây xanh và đèn lồng treo.
Phong cách Bắc Âu (Scandinavian):
Sử dụng ánh sáng tự nhiên tối đa, cửa kính lớn.
Nội thất gỗ sáng màu, thiết kế tối giản.
Điểm nhấn là các đồ trang trí lông thú, len.
Phong cách công nghiệp (Industrial):
Tường gạch trần hoặc bê tông thô.
Đồ nội thất kim loại, da và gỗ tái chế.
Đèn treo dây kim loại hoặc ánh sáng vàng ấm.
Phong cách nhiệt đới (Tropical):
Sử dụng cây cảnh lớn như cây chuối cảnh, dừa cạn.
Nội thất gỗ sẫm màu kết hợp với vải lanh.
Màu sắc rực rỡ như xanh lá, vàng, hoặc cam làm điểm nhấn.
Phong cách Nhật Bản (Japandi):
Kết hợp phong cách Nhật và Scandinavian.
Nội thất gỗ tự nhiên, tông màu nhẹ nhàng.
Trang trí bằng các chi tiết tinh tế như đèn lồng giấy.
Phòng khách mở (Open Concept):
Tích hợp không gian phòng khách với bếp hoặc phòng ăn.
Nội thất đa năng để tối ưu hóa không gian.
Dùng thảm hoặc vách ngăn nhẹ để phân chia khu vực.
Phong cách xanh bền vững (Eco-Friendly):
Sử dụng nội thất từ vật liệu tái chế hoặc thân thiện môi trường.
Trang trí bằng cây xanh, đèn năng lượng mặt trời.
Tận dụng ánh sáng tự nhiên và hệ thống thông gió tốt.',
            '../images/blog/img_67659c7e6c7cc0.11911724.jpg',
            'Đoàn minh khải',
            'Thiết kế'
    );

-- Cập nhật bảng mã khuyến mãi để thêm cột trang_thai
ALTER TABLE ma_khuyen_mai
ADD COLUMN trang_thai ENUM('Đang áp dụng', 'Hết hạn', 'Tạm dừng') DEFAULT 'Đang áp dụng';
-- Cập nhật bảng mã khuyến mãi để thêm cột trang_thai
ALTER TABLE chuong_trinh_khuyen_mai
ADD COLUMN trang_thai ENUM('Đang áp dụng', 'Hết hạn', 'Tạm dừng') DEFAULT 'Đang áp dụng';
