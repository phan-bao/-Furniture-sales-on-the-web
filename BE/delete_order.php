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

// Kiểm tra nếu mã đơn hàng được truyền vào URL
if (isset($_GET['ma_don_hang'])) {
    $ma_don_hang = $_GET['ma_don_hang'];

    // Câu lệnh DELETE sử dụng prepared statements để tránh SQL injection
    $sql = "DELETE FROM don_hang WHERE ma_don_hang = ?";

    // Chuẩn bị câu lệnh
    if ($stmt = $conn->prepare($sql)) {
        // Gắn tham số vào câu lệnh (ma_don_hang là số nguyên)
        $stmt->bind_param("i", $ma_don_hang);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            echo "Đơn hàng đã được xóa thành công!";
            header("Location: ../admin/donhang.php"); // Điều hướng về danh sách đơn hàng
            exit;
        } else {
            echo "Lỗi: Không thể xóa đơn hàng. " . $conn->error;
        }

        // Đóng statement
        $stmt->close();
    } else {
        echo "Lỗi chuẩn bị câu lệnh: " . $conn->error;
    }
} else {
    echo "Mã đơn hàng không hợp lệ.";
}

// Đóng kết nối
$conn->close();
?>
