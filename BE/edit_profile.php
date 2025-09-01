<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "furniture_store";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối cơ sở dữ liệu thất bại']);
    exit();
}

$ten_khach_hang = $_POST['ten_khach_hang'];
$so_dien_thoai = $_POST['so_dien_thoai'];
$new_email = $_POST['new_email'];
$current_username = $_SESSION['username'];

$query = "UPDATE khach_hang k
          JOIN account a ON k.ma_khach_hang = a.ma_khach_hang
          SET k.ten_khach_hang = ?, k.so_dien_thoai = ?, k.mail = ?
          WHERE a.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $ten_khach_hang, $so_dien_thoai, $new_email, $current_username);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thông tin thất bại']);
}

$stmt->close();
$conn->close();
exit();
?>
