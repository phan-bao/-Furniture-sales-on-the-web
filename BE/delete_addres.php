<?php
session_start();

// Bật hiển thị lỗi (chỉ cho môi trường phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['ma_khach_hang'])) {
    echo "error";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];

// Kiểm tra xem mã địa chỉ có được gửi hay không
if (isset($_POST['ma_dia_chi'])) {
    $ma_dia_chi = $_POST['ma_dia_chi'];

    // Kết nối CSDL
    $conn = new mysqli("localhost", "root", "", "furniture_store");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Chuẩn bị câu lệnh SQL để xóa địa chỉ
    $stmt = $conn->prepare("DELETE FROM dia_chi WHERE ma_dia_chi = ? AND ma_khach_hang = ?");
    $stmt->bind_param('ii', $ma_dia_chi, $ma_khach_hang);

    // Thực thi câu lệnh xóa
    if ($stmt->execute()) {
        echo "success";  // Trả về kết quả thành công
    } else {
        echo "error";  // Trả về kết quả lỗi
    }

    $stmt->close();
    $conn->close();
}
?>
