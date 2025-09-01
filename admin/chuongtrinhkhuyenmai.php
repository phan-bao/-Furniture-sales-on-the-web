<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/chuongtrinhkhuyenmai.css">
    <title>Responsive Dashboard Design #1 | AsmrProg</title>
</head>

<script>
    // Lấy URL hiện tại
    const currentPage = window.location.pathname.split('/').pop();

    // Lấy tất cả các mục trong sidebar
    const sidebarItems = document.querySelectorAll('aside .sidebar a');

    // Lặp qua tất cả các mục và thêm class 'active' vào mục có href khớp với URL
    sidebarItems.forEach(item => {
        if (item.getAttribute('href').includes(currentPage)) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
</script>

<body>
    <div class="container">
    <?php include('menu.php'); ?>
        <!-- Main Content -->
        <main>
            <h1>Khuyến Mãi</h1>

            <!-- Search Bar and Create Order Button -->
            <div class="search-create-container">
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Tìm kiếm khuyến mãi..." onkeyup="searchOrders()">
                    <span class="material-icons-sharp">search</span>
                </div>
                <a href="../admin/taochuongtrinhkhuyenmai.php" class="create-order-btn">Tạo khuyến mãi</a>
            </div>

            <div class="promotion-container">
                <a href="../admin/khuyenmai.php" class="promotion-link">
                    <h3>Mã Khuyến Mãi</h3>
                </a>
                <a href="../admin/chuongtrinhkhuyenmai.php" class="promotion-link active">
                    <h3>Chương Trình Khuyến Mãi</h3>
                </a>
            </div>

            <!-- Promotion Table -->
            <?php
            // Kết nối đến cơ sở dữ liệu
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

            // Truy vấn để lấy dữ liệu khuyến mãi
            $sql = "SELECT * FROM chuong_trinh_khuyen_mai"; // Hoặc bảng tương ứng mà bạn muốn lấy dữ liệu
            $result = $conn->query($sql);
            ?>

            <table>
                <thead>
                    <tr>
                        <th>Tên CTKM</th>
                        <th>Ngày Bắt Đầu</th>
                        <th>Ngày Kết Thúc</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Hiển thị dữ liệu của mỗi hàng
                        while ($row = $result->fetch_assoc()) {
                            $trangThaiClass = ($row['trang_thai'] === 'Đang áp dụng') ? 'active' : 'inactive'; // Lựa chọn class dựa trên trạng thái
                            echo "<tr>
                                    <td>{$row['ten_ctkm']}</td>
                                    <td>{$row['ngay_bat_dau']}</td>
                                    <td>{$row['ngay_ket_thuc']}</td>
                                    <td class='$trangThaiClass'>{$row['trang_thai']}</td> <!-- Thêm class cho ô trạng thái -->
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Không có dữ liệu</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <?php
            // Đóng kết nối
            $conn->close();
            ?>
        </main>
    </div>

</body>

</html>
