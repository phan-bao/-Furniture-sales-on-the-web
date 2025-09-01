<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/taochuongtrinhkhuyenmai.css">
    <title>Tạo Chương Trình Khuyến Mãi</title>
</head>

<body>
    <div class="container">
        <?php include('menu.php'); ?>

        <!-- Main Content -->
        <main>
            <h1>Tạo Chương Trình Khuyến Mãi</h1>

            <!-- Form Tạo Chương Trình Khuyến Mãi -->
            <form action="taochuongtrinhkhuyenmai.php" method="POST" class="create-promotion-form">
                <div class="form-group">
                    <label for="ten_ctkm">Tên Chương Trình Khuyến Mãi</label>
                    <input type="text" id="ten_ctkm" name="ten_ctkm" required>
                </div>

                <div class="form-group">
                    <label for="ngay_bat_dau">Ngày Bắt Đầu</label>
                    <input type="datetime-local" id="ngay_bat_dau" name="ngay_bat_dau" required>
                </div>

                <div class="form-group">
                    <label for="ngay_ket_thuc">Ngày Kết Thúc</label>
                    <input type="datetime-local" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
                </div>

                <div class="form-group">
                    <label for="trang_thai">Trạng Thái</label>
                    <select id="trang_thai" name="trang_thai" required>
                        <option value="Đang áp dụng">Đang áp dụng</option>
                        <option value="Hết hạn">Hết hạn</option>
                        <option value="Tạm dừng">Tạm dừng</option>
                    </select>
                </div>

                <button type="submit" name="submit" class="create-promotion-btn">Tạo Chương Trình Khuyến Mãi</button>
            </form>

            <!-- Kết quả xử lý khi tạo chương trình khuyến mãi -->
            <?php
            // Kiểm tra nếu form đã được submit
            if (isset($_POST['submit'])) {
                // Kết nối đến cơ sở dữ liệu
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

                // Lấy dữ liệu từ form
                $ten_ctkm = $_POST['ten_ctkm'];
                $ngay_bat_dau = $_POST['ngay_bat_dau'];
                $ngay_ket_thuc = $_POST['ngay_ket_thuc'];
                $trang_thai = $_POST['trang_thai'];

                // Truy vấn để thêm chương trình khuyến mãi vào cơ sở dữ liệu
                $sql = "INSERT INTO chuong_trinh_khuyen_mai (ten_ctkm, ngay_bat_dau, ngay_ket_thuc, trang_thai) 
                        VALUES ('$ten_ctkm', '$ngay_bat_dau', '$ngay_ket_thuc', '$trang_thai')";

                if ($conn->query($sql) === TRUE) {
                    echo "<p>Chương trình khuyến mãi đã được tạo thành công!</p>";
                } else {
                    echo "Lỗi: " . $sql . "<br>" . $conn->error;
                }

                // Đóng kết nối
                $conn->close();
            }
            ?>
        </main>
    </div>
</body>

</html>
