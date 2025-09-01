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

// Lấy dữ liệu từ POST
$ma_dia_chi = $_POST['ma_dia_chi'] ?? null;
$so_nha = $_POST['so_nha'] ?? '';
$duong_pho = $_POST['duong_pho'] ?? '';
$quoc_gia = $_POST['quoc_gia'] ?? 'Vietnam';
$thanh_pho = $_POST['thanh_pho'] ?? '';
$huyen = $_POST['huyen'] ?? '';
$xa = $_POST['xa'] ?? '';
$so_dien_thoai_giao_hang = $_POST['so_dien_thoai_giao_hang'] ?? '';
$ten_dia_chi = $_POST['ten_dia_chi'] ?? '';

// Kiểm tra dữ liệu hợp lệ
if (!$ma_dia_chi || !$so_nha || !$duong_pho || !$thanh_pho || !$huyen || !$xa || !$so_dien_thoai_giao_hang) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
    exit();
}

// Cập nhật địa chỉ trong cơ sở dữ liệu
$query = "UPDATE dia_chi SET so_nha = ?, duong_pho = ?, quoc_gia = ?, thanh_pho = ?, huyen = ?, xa = ?, so_dien_thoai_giao_hang = ?, ten_dia_chi = ? WHERE ma_dia_chi = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssssssssi', $so_nha, $duong_pho, $quoc_gia, $thanh_pho, $huyen, $xa, $so_dien_thoai_giao_hang, $ten_dia_chi, $ma_dia_chi);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Địa chỉ đã được cập nhật thành công.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật địa chỉ.']);
}

$stmt->close();
$conn->close();
?>
