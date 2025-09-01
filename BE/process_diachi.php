<?php
session_start();

// Bật hiển thị lỗi (chỉ cho môi trường phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['ma_khach_hang'])) {
    header("Location: login.php");
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "furniture_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Nếu người dùng nhấn nút "Lưu Địa Chỉ"
if (isset($_POST['them_dia_chi'])) {
    $so_nha = $_POST['so_nha'];
    $duong_pho = $_POST['duong_pho'];
    $quoc_gia = $_POST['quoc_gia'];
    $thanh_pho = $_POST['thanh_pho'];
    $huyen = $_POST['huyen'];
    $xa = $_POST['xa'];
    $so_dien_thoai_giao_hang = $_POST['so_dien_thoai_giao_hang'];
    $ten_dia_chi = $_POST['ten_dia_chi'] ?? 'Địa Chỉ Mới';

    $stmt = $conn->prepare("
        INSERT INTO dia_chi (ma_khach_hang, so_nha, duong_pho, quoc_gia, thanh_pho, huyen, xa, so_dien_thoai_giao_hang, ten_dia_chi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("issssssss", $ma_khach_hang, $so_nha, $duong_pho, $quoc_gia, $thanh_pho, $huyen, $xa, $so_dien_thoai_giao_hang, $ten_dia_chi);
    $stmt->execute();
    $stmt->close();

    // Sau khi lưu địa chỉ mới, quay về trang thanhtoan.php
    $conn->close();
    header("Location: ../Pages/account.php");
    exit();
}

// Nếu người dùng nhấn "Cập Nhật Địa Chỉ"
if (isset($_POST['update_dia_chi'])) {
    $ma_dia_chi = $_POST['ma_dia_chi'];
    $so_nha = $_POST['so_nha'];
    $duong_pho = $_POST['duong_pho'];
    $quoc_gia = $_POST['quoc_gia'];
    $thanh_pho = $_POST['thanh_pho'];
    $huyen = $_POST['huyen'];
    $xa = $_POST['xa'];
    $so_dien_thoai_giao_hang = $_POST['so_dien_thoai_giao_hang'];
    $ten_dia_chi = $_POST['ten_dia_chi'];

    $update_addr_query = "
        UPDATE dia_chi 
        SET so_nha = ?, duong_pho = ?, quoc_gia = ?, thanh_pho = ?, huyen = ?, xa = ?, so_dien_thoai_giao_hang = ?, ten_dia_chi = ?
        WHERE ma_dia_chi = ?
    ";
    $stmt = $conn->prepare($update_addr_query);
    if (!$stmt) {
        $_SESSION['message'] = "Lỗi chuẩn bị truy vấn UPDATE: " . $conn->error;
        $_SESSION['message_type'] = 'error';
    } else {
        $stmt->bind_param('ssssssssi', $so_nha, $duong_pho, $quoc_gia, $thanh_pho, $huyen, $xa, $so_dien_thoai_giao_hang, $ten_dia_chi, $ma_dia_chi);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Cập nhật địa chỉ thành công!";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Lỗi khi cập nhật địa chỉ: " . $stmt->error;
            $_SESSION['message_type'] = 'error';
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: ../Pages/account.php");
    exit();
}


    // Đóng kết nối
    $conn->close();

?>