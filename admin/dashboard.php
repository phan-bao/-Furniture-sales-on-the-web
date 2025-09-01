<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Tên người dùng cơ sở dữ liệu
$password = ""; // Mật khẩu cơ sở dữ liệu
$dbname = "furniture_store"; // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn tổng doanh thu
$sql_doanh_thu = "SELECT SUM(ctdh.thanh_tien) AS tong_doanh_thu
                  FROM chi_tiet_don_hang ctdh
                  JOIN don_hang dh ON ctdh.ma_don_hang = dh.ma_don_hang
                  WHERE dh.tinh_trang_thanh_toan = 'Đã thanh toán'";
$result_doanh_thu = $conn->query($sql_doanh_thu);
$doanh_thu = $result_doanh_thu->fetch_assoc()['tong_doanh_thu'];

// Truy vấn tổng đơn hàng
$sql_don_hang = "SELECT COUNT(*) AS tong_don_hang
                 FROM don_hang
                 WHERE tinh_trang_thanh_toan = 'Đã thanh toán'";
$result_don_hang = $conn->query($sql_don_hang);
$don_hang = $result_don_hang->fetch_assoc()['tong_don_hang'];

// Truy vấn tổng số lượng sản phẩm
$sql_so_luong = "SELECT SUM(soluong) AS tong_so_luong
                 FROM chi_tiet_don_hang ctdh
                 JOIN don_hang dh ON ctdh.ma_don_hang = dh.ma_don_hang
                 WHERE dh.tinh_trang_thanh_toan = 'Đã thanh toán'";
$result_so_luong = $conn->query($sql_so_luong);
$so_luong = $result_so_luong->fetch_assoc()['tong_so_luong'];

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/dashboard.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="container">
        <?php include('menu.php'); ?>

        <main>
            <h1>Analytics</h1>
            <!-- Analyses -->
            <div class="analyse">
                <div class="sales">
                    <div class="status">
                        <div class="info">
                            <h3>Total Sales</h3>
                            <h1><?php echo number_format($doanh_thu, 0, ',', '.'); ?> VND</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p>+81%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="visits">
                    <div class="status">
                        <div class="info">
                            <h3>Total Orders</h3>
                            <h1><?php echo $don_hang; ?></h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p>-48%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="searches">
                    <div class="status">
                        <div class="info">
                            <h3>Total Products Sold</h3>
                            <h1><?php echo $so_luong; ?></h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p>+21%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Analyses -->
        </main>
        
    </div>
</body>
</html>
