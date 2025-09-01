<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/donhang.css">
    <title>Danh sách đơn hàng</title>
  
</head>

<body>

<div class="container">
<?php include('menu.php'); ?>
    <!-- Sidebar Section -->
    <!-- Include Sidebar -->
    
        <!-- End of Sidebar Section -->
    <!-- Main Content -->
    <main>
        <h1>Danh sách đơn hàng</h1>

<!-- Search Bar and Create Order Button -->
<div class="search-create-container">
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Tìm kiếm đơn hàng..." onkeyup="searchOrders()">
        <span class="material-icons-sharp">search</span>
    </div>
    <a href="../admin/taodonhang.php" class="create-order-btn">Tạo đơn hàng</a>
</div>



       <!-- Display orders in a table -->
       <div class="orders">
            <table>
                <thead>
                    <tr>
                        <th>Đơn hàng</th> <!-- Mã đơn hàng -->
                        <th>Ngày đặt</th> <!-- Ngày đặt -->
                        <th>Khách hàng</th> <!-- Tên khách hàng -->
                        <th>Thanh toán</th> <!-- Tình trạng thanh toán -->
                        <th>Giao hàng</th> <!-- Tình trạng giao hàng -->
                        <th class="total-price">Tổng tiền</th> <!-- Giá tiền -->
                    </tr>
                </thead>
                <tbody>
                <?php
// Kết nối đến MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn để lấy thông tin đơn hàng với dữ liệu từ bảng chi tiết đơn hàng
$sql = "SELECT don_hang.ma_don_hang, don_hang.ngay_dat, khach_hang.ten_khach_hang, 
               don_hang.tinh_trang_thanh_toan, don_hang.tinh_trang_giao_hang, 
               SUM(ctdh.thanh_tien) AS tong_tien
        FROM don_hang 
        JOIN khach_hang ON don_hang.ma_khach_hang = khach_hang.ma_khach_hang 
        JOIN chi_tiet_don_hang ctdh ON don_hang.ma_don_hang = ctdh.ma_don_hang
        GROUP BY don_hang.ma_don_hang, don_hang.ngay_dat, khach_hang.ten_khach_hang, 
                 don_hang.tinh_trang_thanh_toan, don_hang.tinh_trang_giao_hang
        ORDER BY don_hang.ngay_dat DESC";

$result = $conn->query($sql);

// Hiển thị dữ liệu từ truy vấn
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['ma_don_hang'] . "</td>"; // Mã đơn hàng
        echo "<td>" . $row['ngay_dat'] . "</td>"; // Ngày đặt
        echo "<td>" . $row['ten_khach_hang'] . "</td>"; // Tên khách hàng

        // Hiển thị trạng thái thanh toán và giao hàng
        echo "<td class='" . ($row['tinh_trang_thanh_toan'] == 'Đã thanh toán' ? 'status-paid' : 'status-unpaid') . "'>" . $row['tinh_trang_thanh_toan'] . "</td>";
        echo "<td class='" . ($row['tinh_trang_giao_hang'] == 'Đang giao hàng' ? 'delivery-pending' : 'delivery-done') . "'>" . $row['tinh_trang_giao_hang'] . "</td>";

        // Hiển thị tổng tiền
        echo "<td class='total-price'>" . number_format($row['tong_tien'], 0, ',', '.') . "đ</td>"; // Tổng tiền
        
        // Thêm nút "Sửa" và "Xóa"
        echo "<td>
                <a href='../BE/edit_order.php?ma_don_hang={$row['ma_don_hang']}' class='edit-btn'>
                    <span class='material-icons-sharp'>edit</span>
                </a>
                <a href='../BE/delete_order.php?ma_don_hang={$row['ma_don_hang']}' class='delete-btn' onclick='return confirm(\"Bạn có chắc chắn muốn xóa đơn hàng này?\")'>
                    <span class='material-icons-sharp'>delete</span>
                </a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Không có đơn hàng nào</td></tr>";
}

// Đóng kết nối
$conn->close();
?>

</tbody>

            </table>
        </div>
    </main>
    <!-- End of Main Content -->

    <script>
        function searchOrders() {
            // Lấy giá trị từ ô tìm kiếm
            let input = document.getElementById('search-input').value.toLowerCase();
            let table = document.querySelector(".orders table tbody");
            let rows = table.getElementsByTagName('tr');
            
            // Duyệt qua các hàng trong bảng và ẩn những hàng không khớp với từ khóa
            for (let i = 0; i < rows.length; i++) {
                // Lấy giá trị của cột mã đơn hàng (giả sử mã đơn hàng nằm ở cột đầu tiên - td[0])
                let orderIdCell = rows[i].getElementsByTagName('td')[0]; 
                
                if (orderIdCell) {
                    let orderId = orderIdCell.textContent.toLowerCase();

                    // Kiểm tra nếu mã đơn hàng khớp với từ khóa tìm kiếm
                    rows[i].style.display = orderId.indexOf(input) > -1 ? '' : 'none';
                }
            }
        }
    </script>
</body>

</html>
