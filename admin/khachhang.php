<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/khachhang.css">
    <title>Danh sách đơn hàng</title>
</head>

<body>

<div class="container">
    <?php include('menu.php'); ?>

    <!-- Main Content -->
    <main>
        <h1>Danh sách Khách Hàng</h1>

        <!-- Search Bar and Create Order Button -->
        <div class="search-create-container">
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Tìm kiếm khách hàng..." onkeyup="searchCustomers()">
                <span class="material-icons-sharp">search</span>
            </div>
            <a href="../admin/themkhachhang.php" class="create-order-btn">Thêm Khách Hàng</a>
        </div>

        <!-- Display customers and their orders in a table -->
        <div class="orders">
            <table>
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Số đơn hàng</th>
                        <th class="total-price">Tổng chi tiêu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Kết nối cơ sở dữ liệu
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

                    // Truy vấn để lấy thông tin khách hàng và tổng chi tiêu
                    $sql = "SELECT khach_hang.ten_khach_hang, khach_hang.mail, khach_hang.so_dien_thoai, 
       COUNT(don_hang.ma_don_hang) AS so_don_hang, 
       IFNULL(SUM(phien_ban_san_pham.gia), 0) AS tong_chi_tieu
FROM khach_hang
LEFT JOIN don_hang ON khach_hang.ma_khach_hang = don_hang.ma_khach_hang
LEFT JOIN chi_tiet_don_hang ON don_hang.ma_don_hang = chi_tiet_don_hang.ma_don_hang
LEFT JOIN phien_ban_san_pham ON chi_tiet_don_hang.SKU_phien_ban = phien_ban_san_pham.SKU_phien_ban
GROUP BY khach_hang.ma_khach_hang
ORDER BY tong_chi_tieu DESC;
";  // Sắp xếp khách hàng theo tổng chi tiêu từ cao đến thấp

                    // Sử dụng prepared statement
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Kiểm tra và hiển thị dữ liệu
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['ten_khach_hang']) . "</td>"; // Tên khách hàng
                                echo "<td>" . htmlspecialchars($row['mail']) . "</td>"; // Email
                                echo "<td>" . htmlspecialchars($row['so_dien_thoai']) . "</td>"; // Số điện thoại
                                echo "<td>" . $row['so_don_hang'] . "</td>"; // Số đơn hàng
                                echo "<td class='total-price'>" . number_format($row['tong_chi_tieu'], 0, ',', '.') . "đ</td>"; // Tổng chi tiêu
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Không có khách hàng nào</td></tr>";
                        }

                        // Đóng statement
                        $stmt->close();
                    } else {
                        echo "Lỗi trong việc thực thi câu lệnh SQL: " . $conn->error;
                    }

                    // Đóng kết nối
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function searchCustomers() {
            let input = document.getElementById('search-input').value.toLowerCase();
            let table = document.querySelector(".orders table tbody");
            let rows = table.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                let customerNameCell = rows[i].getElementsByTagName('td')[0];
                if (customerNameCell) {
                    let customerName = customerNameCell.textContent.toLowerCase();
                    rows[i].style.display = customerName.indexOf(input) > -1 ? '' : 'none';
                }
            }
        }
    </script>

</div>

</body>
</html>
