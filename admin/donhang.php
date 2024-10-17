<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/donhang.css">
    <title>Danh sách đơn hàng</title>
    <style>
        
    </style>    
</head>

<body>

<div class="container">
    
    <!-- Sidebar Section -->
    <aside>
        <div class="toggle">
            <div class="logo">
                <img src="images/logo.png">
                <h2>B<span class="danger">LISS</span></h2>
            </div>
            <div class="close" id="close-btn">
                <span class="material-icons-sharp">close</span>
            </div>
        </div>

        <div class="sidebar">
            <a href="../admin/dashboard.php">
                <span class="material-icons-sharp">insights</span>
                <h3>Tổng Quan</h3>
            </a>
            <a href="../admin/donhang.php" class="active"> 
                <span class="material-icons-sharp">dashboard</span>
                <h3>Đơn Hàng</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">person_outline</span>
                <h3>Users</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">receipt_long</span>
                <h3>History</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">mail_outline</span>
                <h3>Tickets</h3>
                <span class="message-count">27</span>
            </a>
            <a href="#">
                <span class="material-icons-sharp">inventory</span>
                <h3>Sale List</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">report_gmailerrorred</span>
                <h3>Reports</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">settings</span>
                <h3>Settings</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">add</span>
                <h3>New Login</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">logout</span>
                <h3>Logout</h3>
            </a>
        </div>
        
    </aside>
    <!-- End of Sidebar Section -->

    <!-- Main Content -->
    <main>
        <h1>Danh sách đơn hàng</h1>

        <!-- "Create Order" button -->
        <a href="create_order.php" class="create-order-btn">Tạo đơn hàng</a>

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

            // Truy vấn để lấy danh sách đơn hàng
            $sql = "SELECT don_hang.ma_don_hang, don_hang.ngay_dat, khach_hang.ten_khach_hang, 
                           don_hang.tinh_trang_thanh_toan, don_hang.tinh_trang_giao_hang, 
                           san_pham.gia 
                    FROM don_hang 
                    JOIN khach_hang ON don_hang.ma_khach_hang = khach_hang.ma_khach_hang 
                    JOIN san_pham ON don_hang.ma_san_pham = san_pham.ma_san_pham 
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
                    if ($row['tinh_trang_thanh_toan'] == 'Đã thanh toán') {
                        echo "<td class='status-paid'>Đã thanh toán</td>";
                    } else {
                        echo "<td class='status-unpaid'>Chưa thanh toán</td>";
                    }

                    if ($row['tinh_trang_giao_hang'] == 'Đang giao hàng') {
                        echo "<td class='delivery-pending'>Đang giao hàng</td>";
                    } else {
                        echo "<td class='delivery-done'>Đã giao</td>";
                    }

                    // Hiển thị tổng tiền với định dạng
                    echo "<td class='total-price'>" . number_format($row['gia'], 0, ',', '.') . "đ</td>"; // Giá tiền
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Không có đơn hàng nào</td></tr>";
            }

            // Đóng kết nối
            $conn->close();
            ?>
        </tbody>
    </table>
</div>


    </main>
    <!-- End of Main Content -->

    <!-- Right Section -->
    <div class="right-section">
        <!-- Additional content can go here -->
    </div>

</div>

</body>

</html>
