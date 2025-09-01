<?php
// order_success.php

session_start();

// Kiểm tra xem người dùng đã đặt hàng chưa
if (!isset($_GET['order_id'])) {
    die('Không tìm thấy thông tin đơn hàng.');
}

$ma_don_hang = (int)$_GET['order_id'];

// Kết nối CSDL sử dụng MySQLi
$servername = "localhost";
$username = "root"; // Thay bằng username DB của bạn
$password = "";     // Thay bằng password DB của bạn
$dbname = "furniture_store";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy thông tin đơn hàng từ bảng don_hang
$stmt = $conn->prepare("
    SELECT dh.ma_don_hang, dh.ngay_dat, dh.tinh_trang_thanh_toan, dh.tinh_trang_giao_hang, dh.phuong_thuc_thanh_toan,
           kh.ten_khach_hang, kh.mail, kh.so_dien_thoai,
           dc.so_nha, dc.duong_pho, dc.quoc_gia, dc.thanh_pho, dc.huyen, dc.xa, dc.ten_dia_chi,
           dh.danh_sach_san_pham, dh.tong_tien
    FROM don_hang dh
    JOIN khach_hang kh ON dh.ma_khach_hang = kh.ma_khach_hang
    JOIN dia_chi dc ON dh.ma_dia_chi = dc.ma_dia_chi
    WHERE dh.ma_don_hang = ?
");
$stmt->bind_param("i", $ma_don_hang);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die('Đơn hàng không tồn tại.');
}

// Giải mã danh sách sản phẩm từ JSON
$order_items = json_decode($order['danh_sach_san_pham'], true);

// Kết nối lại để lấy tên sản phẩm
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác Nhận Đơn Hàng</title>
    <style>
        /* CSS cơ bản cho trang */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .order-success-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .order-success-container h1 {
            color: #28a745;
            text-align: center;
        }
        .order-success-container h2 {
            margin-top: 20px;
            color: #333333;
        }
        .order-success-container p {
            color: #555555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #dddddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="order-success-container">
        <h1>Đơn Hàng Của Bạn Đã Được Đặt Thành Công!</h1>
        <p><strong>Mã Đơn Hàng:</strong> <?php echo htmlspecialchars($order['ma_don_hang']); ?></p>
        <p><strong>Ngày Đặt Hàng:</strong> <?php echo htmlspecialchars($order['ngay_dat']); ?></p>

        <h2>Thông Tin Khách Hàng</h2>
        <p><strong>Tên:</strong> <?php echo htmlspecialchars($order['ten_khach_hang']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['mail']); ?></p>
        <p><strong>Số Điện Thoại:</strong> <?php echo htmlspecialchars($order['so_dien_thoai']); ?></p>

        <h2>Địa Chỉ Giao Hàng</h2>
        <p><strong><?php echo htmlspecialchars($order['ten_dia_chi']); ?>:</strong></p>
        <p>
            <?php
                // Hiển thị địa chỉ đầy đủ
                echo htmlspecialchars($order['so_nha'] . ' ' . $order['duong_pho'] . ', ' . 
                                      $order['xa'] . ', ' . $order['huyen'] . ', ' . 
                                      $order['thanh_pho'] . ', ' . $order['quoc_gia']);
            ?>
        </p>

        <h2>Thông Tin Đơn Hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>Sản Phẩm</th>
                    <th>SKU Phiên Bản</th>
                    <th>Số Lượng</th>
                    <th>Giá (VNĐ)</th>
                    <th>Tổng Tiền (VNĐ)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td>
                        <?php
                            // Lấy tên sản phẩm từ bảng san_pham
                            $sku = htmlspecialchars($item['SKU_phien_ban']);
                            // Kết nối CSDL để lấy tên sản phẩm
                            $conn = new mysqli($servername, $username, $password, $dbname);
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }
                            $stmt = $conn->prepare("SELECT ten_san_pham FROM san_pham WHERE SKU_san_pham = ?");
                            $stmt->bind_param("s", $sku);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $san_pham = $result->fetch_assoc();
                            echo htmlspecialchars($san_pham['ten_san_pham']);
                            $stmt->close();
                            $conn->close();
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['SKU_phien_ban']); ?></td>
                    <td><?php echo htmlspecialchars($item['so_luong']); ?></td>
                    <td><?php echo number_format($item['gia'], 0, ',', '.') . ' VNĐ'; ?></td>
                    <td><?php echo number_format($item['thanh_tien'], 0, ',', '.') . ' VNĐ'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Tổng Tiền:</strong> <?php echo number_format($order['tong_tien'], 0, ',', '.') . ' VNĐ'; ?></p>
        <p><strong>Phương Thức Thanh Toán:</strong> <?php echo htmlspecialchars($order['phuong_thuc_thanh_toan']); ?></p>
        <p><strong>Trạng Thái Thanh Toán:</strong> <?php echo htmlspecialchars($order['tinh_trang_thanh_toan']); ?></p>
        <p><strong>Trạng Thái Giao Hàng:</strong> <?php echo htmlspecialchars($order['tinh_trang_giao_hang']); ?></p>

        <a href="index.php" class="button">Trở Về Trang Chủ</a>
    </div>
</body>
</html>
