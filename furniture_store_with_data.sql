-- Tạo cơ sở dữ liệu furniture_store nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS furniture_store;

-- Sử dụng cơ sở dữ liệu furniture_store
USE furniture_store;

-- Tạo bảng Khách Hàng
CREATE TABLE khach_hang (
    ma_khach_hang INT AUTO_INCREMENT PRIMARY KEY,
    ten_khach_hang VARCHAR(255) NOT NULL,
    mail VARCHAR(255) UNIQUE NOT NULL,
    so_dien_thoai VARCHAR(20) UNIQUE NOT NULL,
    quoc_gia VARCHAR(255),
    thanh_pho VARCHAR(255),
    huyen VARCHAR(255),
    xa VARCHAR(255)
);

-- Tạo bảng Sản Phẩm
CREATE TABLE san_pham (
    ma_san_pham INT AUTO_INCREMENT PRIMARY KEY,
    ten_san_pham VARCHAR(255) NOT NULL,
    gia DECIMAL(10, 2) NOT NULL
);

-- Tạo bảng Đơn Hàng
CREATE TABLE don_hang (
    ma_don_hang INT AUTO_INCREMENT PRIMARY KEY,
    ma_khach_hang INT,
    ma_san_pham INT,
    ngay_dat DATE NOT NULL,
    tinh_trang_thanh_toan VARCHAR(50),
    tinh_trang_giao_hang VARCHAR(50),
    FOREIGN KEY (ma_khach_hang) REFERENCES khach_hang(ma_khach_hang),
    FOREIGN KEY (ma_san_pham) REFERENCES san_pham(ma_san_pham)
);

-- Chèn dữ liệu mẫu vào bảng Khách Hàng
INSERT INTO khach_hang (ten_khach_hang, mail, so_dien_thoai, quoc_gia, thanh_pho, huyen, xa) 
VALUES 
('Đoàn Minh Khải', 'nguyenvana@gmail.com', '0912345678', 'Vietnam', 'Ho Chi Minh', 'Quan 1', 'Phuong 1'),
('Phan Văn Bảo', 'tranthib@gmail.com', '0938765432', 'Vietnam', 'Hanoi', 'Hoan Kiem', 'Phuong 2');

-- Chèn dữ liệu mẫu vào bảng Sản Phẩm
INSERT INTO san_pham (ten_san_pham, gia) 
VALUES 
('Bàn Gỗ', 1500000),
('Ghế Sofa', 3500000);

-- Chèn dữ liệu mẫu vào bảng Đơn Hàng
INSERT INTO don_hang (ma_khach_hang, ma_san_pham, ngay_dat, tinh_trang_thanh_toan, tinh_trang_giao_hang) 
VALUES 
(1, 1, '2024-10-10', 'Đã thanh toán', 'Đang giao hàng'),
(2, 2, '2024-10-12', 'Chưa thanh toán', 'Chưa giao');
