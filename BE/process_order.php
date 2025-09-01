<?php
session_start();

// Bật hiển thị lỗi để debug (chỉ sử dụng trong môi trường phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra login
if (!isset($_SESSION['ma_khach_hang'])) {
    header("Location: login.php");
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "furniture_store");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy thông tin giỏ hàng từ cơ sở dữ liệu
$stmt_cart = $conn->prepare("
    SELECT 
        g.id_gio_hang, g.SKU_phien_ban, g.quantity, 
        pb.gia, 
        sp.ten_san_pham
    FROM giohang g
    JOIN phien_ban_san_pham pb ON g.SKU_phien_ban = pb.SKU_phien_ban
    JOIN san_pham sp ON pb.SKU_san_pham = sp.SKU_san_pham
    WHERE g.user_id = (SELECT user_id FROM account WHERE ma_khach_hang = ?)
");
$stmt_cart->bind_param("i", $ma_khach_hang);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

$cart_items = [];
$total = 0;

if ($result_cart->num_rows > 0) {
    while ($row = $result_cart->fetch_assoc()) {
        $row['total_price'] = $row['gia'] * $row['quantity'];
        $total += $row['total_price'];
        $cart_items[] = $row;
    }
} else {
    echo "Giỏ hàng trống. Vui lòng thêm sản phẩm.";
    exit();
}
$stmt_cart->close();

// Lấy dữ liệu từ form
$ma_dia_chi = $_POST['ma_dia_chi'] ?? null;
$phuong_thuc_thanh_toan = $_POST['phuong_thuc_thanh_toan'] ?? null;

// Kiểm tra dữ liệu
if (!$ma_dia_chi || !$phuong_thuc_thanh_toan) {
    echo "Dữ liệu không hợp lệ.";
    exit();
}

// Bắt đầu giao dịch
$conn->begin_transaction();

try {
    // Thêm đơn hàng vào bảng `don_hang`
    $stmt_order = $conn->prepare("
        INSERT INTO don_hang (ma_khach_hang, ma_dia_chi, ngay_dat, tinh_trang_thanh_toan, tinh_trang_giao_hang, phuong_thuc_thanh_toan)
        VALUES (?, ?, NOW(), ?, ?, ?)
    ");
    $tinh_trang_thanh_toan = ($phuong_thuc_thanh_toan === "Thanh toán trực tuyến") ? "Đã thanh toán" : "Chưa thanh toán";
    $tinh_trang_giao_hang = "Đang xử lý"; // Giá trị mặc định
    $stmt_order->bind_param("iisss", $ma_khach_hang, $ma_dia_chi, $tinh_trang_thanh_toan, $tinh_trang_giao_hang, $phuong_thuc_thanh_toan);
    $stmt_order->execute();

    // Lấy ID đơn hàng vừa thêm
    $ma_don_hang = $stmt_order->insert_id;

    // Thêm chi tiết đơn hàng vào bảng `chi_tiet_don_hang`
    $stmt_order_detail = $conn->prepare("
        INSERT INTO chi_tiet_don_hang (ma_don_hang, SKU_phien_ban, soluong, gia, thanh_tien)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($cart_items as $item) {
        $sku = $item['SKU_phien_ban'];
        $quantity = $item['quantity'];
        $price = $item['gia'];
        $total_price = $item['total_price'];

        $stmt_order_detail->bind_param("isidd", $ma_don_hang, $sku, $quantity, $price, $total_price);
        $stmt_order_detail->execute();
    }

    // Commit giao dịch
    $conn->commit();

    // Xóa giỏ hàng khỏi cơ sở dữ liệu
    $stmt_clear_cart = $conn->prepare("DELETE FROM giohang WHERE user_id = (SELECT user_id FROM account WHERE ma_khach_hang = ?)");
    $stmt_clear_cart->bind_param("i", $ma_khach_hang);
    $stmt_clear_cart->execute();
    $stmt_clear_cart->close();

    // Chuyển hướng đến trang thành công
    header("Location: ../BE/order_success.php?order_id=" . $ma_don_hang);
    exit();
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo "Lỗi khi xử lý đơn hàng: " . $e->getMessage();
    exit();
}


$conn->close();
?>
