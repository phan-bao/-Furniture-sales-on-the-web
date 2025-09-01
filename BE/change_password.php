<?php
session_start();
header('Content-Type: application/json');

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
    exit();
}

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "furniture_store";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối cơ sở dữ liệu.']);
    exit();
}

// Lấy thông tin từ POST
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Kiểm tra mật khẩu mới và xác nhận mật khẩu
if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.']);
    exit();
}

// Lấy thông tin tài khoản hiện tại
$username = $_SESSION['username'];
$query = "SELECT password FROM account WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản.']);
    exit();
}

$user = $result->fetch_assoc();
$hashed_password = $user['password']; // Mật khẩu đã được mã hóa trong cơ sở dữ liệu

// Kiểm tra mật khẩu cũ
if (!password_verify($current_password, $hashed_password)) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không chính xác.']);
    exit();
}

// Mã hóa mật khẩu mới
$new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Cập nhật mật khẩu trong cơ sở dữ liệu
$update_query = "UPDATE account SET password = ? WHERE username = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param('ss', $new_hashed_password, $username);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Mật khẩu đã được thay đổi thành công.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật mật khẩu.']);
}

// Đóng kết nối
$update_stmt->close();
$conn->close();
?>
