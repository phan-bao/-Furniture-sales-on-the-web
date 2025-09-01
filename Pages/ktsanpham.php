<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm Tra Đơn Hàng</title>
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/footer.css">
    <link rel="stylesheet" href="../Css/ktsanpham.css">
</head>
<body>
<?php include '../Partials/header.php'; ?>
    <div class="container">
        <!-- Form kiểm tra đơn hàng -->
        <div class="order-check">
            <h2>🔍 KIỂM TRA ĐƠN HÀNG</h2>
            <form id="checkOrderForm" method="POST">
                
                <div>
                    <label>
                        <input type="radio" name="check_type" value="phone" checked> Số điện thoại
                    </label>
                    <label>
                        <input type="radio" name="check_type" value="email"> Email
                    </label>
                </div>
                <input type="text" name="identifier" id="identifier" placeholder="Nhập số điện thoại hoặc email" required>
                <input type="submit" value="Kiểm Tra">
            </form>
        </div>

        <!-- Phần hiển thị kết quả -->
        <div id="result" class="result-display">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Kết nối cơ sở dữ liệu
                $servername = "localhost";
                $username = "root"; // Thay bằng username của bạn
                $password = ""; // Thay bằng mật khẩu của bạn
                $dbname = "furniture_store";

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Kết nối thất bại: " . $conn->connect_error);
                }

                $check_type = $_POST['check_type'];
                $identifier = $_POST['identifier'];

                // Truy vấn dữ liệu từ bảng don_hang, khach_hang và phien_ban_san_pham
                if ($check_type === 'phone') {
                    $stmt = $conn->prepare("
                        SELECT dh.ma_don_hang, kh.ten_khach_hang, kh.so_dien_thoai, kh.mail AS email,
                               dh.ngay_dat AS ngay_mua, dh.tinh_trang_thanh_toan, dh.tinh_trang_giao_hang,
                               kh.thanh_pho, kh.huyen, kh.xa,
                               pbs.gia, pbs.so_luong_ton_kho AS so_luong_san_pham
                        FROM don_hang dh
                        JOIN khach_hang kh ON dh.ma_khach_hang = kh.ma_khach_hang
                        JOIN phien_ban_san_pham pbs ON dh.SKU_phien_ban = pbs.SKU_phien_ban
                        WHERE kh.so_dien_thoai = ?
                    ");
                } else {
                    $stmt = $conn->prepare("
                        SELECT dh.ma_don_hang, kh.ten_khach_hang, kh.so_dien_thoai, kh.mail AS email,
                               dh.ngay_dat AS ngay_mua, dh.tinh_trang_thanh_toan, dh.tinh_trang_giao_hang,
                               kh.thanh_pho, kh.huyen, kh.xa,
                               pbs.gia, pbs.so_luong_ton_kho AS so_luong_san_pham
                        FROM don_hang dh
                        JOIN khach_hang kh ON dh.ma_khach_hang = kh.ma_khach_hang
                        JOIN phien_ban_san_pham pbs ON dh.SKU_phien_ban = pbs.SKU_phien_ban
                        WHERE kh.mail = ?
                    ");
                }

                if (!$stmt) {
                    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
                }

                $stmt->bind_param("s", $identifier);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='result-display'>";
                        echo "<div class='result-left'>";
                        echo "<h3>Mã đơn hàng: " . $row['ma_don_hang'] . "</h3>";
                        echo "<p>Họ và tên khách hàng: " . $row['ten_khach_hang'] . "</p>";
                        echo "<p>Số điện thoại: " . $row['so_dien_thoai'] . "</p>";
                        echo "<p>Email: " . $row['email'] . "</p>";
                        echo "<p>Ngày mua: " . $row['ngay_mua'] . "</p>";
                        echo "<p class='status'>Trạng thái thanh toán: " . $row['tinh_trang_thanh_toan'] . "</p>";
                        echo "<p class='status'>Trạng thái giao hàng: " . $row['tinh_trang_giao_hang'] . "</p>";
                        echo "</div>";
                        echo "<div class='result-right'>";
                        echo "<h3>Giá trị đơn hàng</h3>";
                        echo "<p class='gia-tien'>" . number_format($row['gia'], 0, ',', '.') . " VNĐ</p>";
                        echo "<p>Số lượng sản phẩm: " . $row['so_luong_san_pham'] . "</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div><p>Không tìm thấy dữ liệu đơn hàng.</p></div>";
                }

                $stmt->close();
                $conn->close();
            }
            ?>
        </div>
    </div>

    <script>
        document.getElementById("checkOrderForm").addEventListener("submit", function(event) {
            // Đảm bảo form sẽ gửi yêu cầu POST chứ không dùng AJAX để xử lý PHP trên cùng trang
        });
    </script>
</body>
<?php include '../Partials/footer.php'; ?>
</html>
