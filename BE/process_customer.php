<?php
session_start();
header('Content-Type: application/json'); // Trả về JSON

include 'db_connect.php';

// Lấy dữ liệu từ form
$ho = $_POST['ho'];
$ten = $_POST['ten'];
$email = $_POST['email'];
$sdt = $_POST['sdt'];
$quoc_gia = $_POST['quoc_gia'];
$thanh_pho = $_POST['thanh_pho'];
$huyen = $_POST['huyen'];
$xa = $_POST['xa'];

// Ghép tên họ và tên lại
$ten_khach_hang = $ho . ' ' . $ten;

// Chuẩn bị câu lệnh SQL để thêm khách hàng mới
$sql = "INSERT INTO khach_hang (ten_khach_hang, mail, so_dien_thoai, quoc_gia, thanh_pho, huyen, xa)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $ten_khach_hang, $email, $sdt, $quoc_gia, $thanh_pho, $huyen, $xa);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Thêm khách hàng thành công!', 'type' => 'success']);
    } else {
        echo json_encode(['message' => 'Lỗi: ' . $stmt->error, 'type' => 'error']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode(['message' => 'Lỗi: ' . $e->getMessage(), 'type' => 'error']);
}

$conn->close();
exit();
