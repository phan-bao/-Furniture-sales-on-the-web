<?php
// Thông tin kết nối đến MySQL
$servername = "localhost";  // Tên máy chủ MySQL
$username = "root";         // Tên người dùng MySQL (mặc định là "root" cho XAMPP)
$password = "";             // Mật khẩu MySQL (thường để trống trên XAMPP)
$dbname = "furniture_store"; // Tên cơ sở dữ liệu bạn muốn kết nối

// Tạo kết nối đến MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
    echo "Kết nối thành công!";
}

// Đóng kết nối (tùy chọn, nên đóng kết nối sau khi thực hiện xong các thao tác)
$conn->close();
?>
