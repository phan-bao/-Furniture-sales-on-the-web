<?php
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['ma_khach_hang'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập.']);
    exit();
}

// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "furniture_store");

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Kết nối cơ sở dữ liệu thất bại.']);
    exit();
}

// Lấy id_gio_hang từ yêu cầu POST
if (isset($_POST['id_gio_hang'])) {
    $id_gio_hang = $_POST['id_gio_hang'];

    // Lấy ma_khach_hang từ session để xác thực người dùng
    $ma_khach_hang = $_SESSION['ma_khach_hang'];

    // Xóa sản phẩm khỏi giỏ hàng
    $stmt = $conn->prepare("DELETE FROM giohang WHERE id_gio_hang = ? AND user_id = (SELECT user_id FROM account WHERE ma_khach_hang = ?)");
    $stmt->bind_param("is", $id_gio_hang, $ma_khach_hang); // Binding dữ liệu vào câu lệnh

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể xóa sản phẩm.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không có id giỏ hàng.']);
}

$conn->close();
?>
