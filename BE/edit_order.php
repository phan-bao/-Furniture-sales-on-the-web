<?php
include 'db_connect.php';
session_start(); // Khởi tạo session

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy mã đơn hàng từ URL
if (isset($_GET['ma_don_hang'])) {
    $ma_don_hang = $_GET['ma_don_hang'];

    // Truy vấn lấy thông tin đơn hàng và các sản phẩm
    $sql = "SELECT don_hang.ma_don_hang, don_hang.ngay_dat, don_hang.tinh_trang_thanh_toan, don_hang.tinh_trang_giao_hang, 
            chi_tiet_don_hang.gia, chi_tiet_don_hang.thanh_tien, chi_tiet_don_hang.soluong, 
            san_pham.ten_san_pham, khach_hang.ten_khach_hang, 
            khach_hang.mail, khach_hang.so_dien_thoai, dia_chi.quoc_gia, dia_chi.thanh_pho, 
            dia_chi.huyen, dia_chi.xa
            FROM don_hang
            JOIN khach_hang ON don_hang.ma_khach_hang = khach_hang.ma_khach_hang
            JOIN dia_chi ON don_hang.ma_dia_chi = dia_chi.ma_dia_chi
            JOIN chi_tiet_don_hang ON don_hang.ma_don_hang = chi_tiet_don_hang.ma_don_hang
            JOIN phien_ban_san_pham ON chi_tiet_don_hang.SKU_phien_ban = phien_ban_san_pham.SKU_phien_ban
            JOIN san_pham ON phien_ban_san_pham.SKU_san_pham = san_pham.SKU_san_pham
            WHERE don_hang.ma_don_hang = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ma_don_hang);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Gán các giá trị vào các biến để hiển thị trên form
        $ngay_dat = $row['ngay_dat'];
        $tinh_trang_thanh_toan = $row['tinh_trang_thanh_toan'];
        $tinh_trang_giao_hang = $row['tinh_trang_giao_hang'];
        $gia = $row['gia'];
        $ten_khach_hang = $row['ten_khach_hang'];
        $mail = $row['mail'];
        $so_dien_thoai = $row['so_dien_thoai'];
        $quoc_gia = $row['quoc_gia'];
        $thanh_pho = $row['thanh_pho'];
        $huyen = $row['huyen'];
        $xa = $row['xa'];
        $ten_san_pham = $row['ten_san_pham']; // Lấy tên sản phẩm
    } else {
        echo "Không tìm thấy đơn hàng.";
        exit;
    }
}

// Kiểm tra nếu form đã được gửi
if (isset($_POST['update_order'])) {
    $ma_don_hang = $_POST['ma_don_hang'];
    $ngay_dat = $_POST['ngay_dat'];
    $tinh_trang_thanh_toan = $_POST['tinh_trang_thanh_toan'];
    $tinh_trang_giao_hang = $_POST['tinh_trang_giao_hang'];
    $gia = $_POST['gia'];
    
    // Lấy thông tin khách hàng từ form
    $ten_khach_hang = $_POST['ten_khach_hang'];
    $mail = $_POST['mail'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $quoc_gia = $_POST['quoc_gia'];
    $thanh_pho = $_POST['thanh_pho'];
    $huyen = $_POST['huyen'];
    $xa = $_POST['xa'];

    // Kiểm tra xem giá có hợp lệ không
    if ($gia <= 0) {
        echo "Giá sản phẩm phải lớn hơn 0!";
        exit;
    }

    // Cập nhật đơn hàng
    $sql = "UPDATE don_hang 
    JOIN chi_tiet_don_hang ON don_hang.ma_don_hang = chi_tiet_don_hang.ma_don_hang
    JOIN phien_ban_san_pham ON chi_tiet_don_hang.SKU_phien_ban = phien_ban_san_pham.SKU_phien_ban
    JOIN san_pham ON phien_ban_san_pham.SKU_san_pham = san_pham.SKU_san_pham
    JOIN khach_hang ON don_hang.ma_khach_hang = khach_hang.ma_khach_hang
    JOIN dia_chi ON don_hang.ma_dia_chi = dia_chi.ma_dia_chi
    SET don_hang.ngay_dat = ?, don_hang.tinh_trang_thanh_toan = ?, don_hang.tinh_trang_giao_hang = ?, 
        chi_tiet_don_hang.gia = ?, san_pham.ten_san_pham = ?, khach_hang.ten_khach_hang = ?, 
        khach_hang.mail = ?, khach_hang.so_dien_thoai = ?, dia_chi.quoc_gia = ?, dia_chi.thanh_pho = ?, 
        dia_chi.huyen = ?, dia_chi.xa = ?
    WHERE don_hang.ma_don_hang = ?";

    // Sửa lại cách gọi bind_param
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdssssssssi", 
     $ngay_dat,               // don_hang.ngay_dat
     $tinh_trang_thanh_toan,   // don_hang.tinh_trang_thanh_toan
     $tinh_trang_giao_hang,    // don_hang.tinh_trang_giao_hang
     $gia,                     // chi_tiet_don_hang.gia
     $ten_san_pham,            // san_pham.ten_san_pham
     $ten_khach_hang,          // khach_hang.ten_khach_hang
     $mail,                    // khach_hang.mail
     $so_dien_thoai,           // khach_hang.so_dien_thoai
     $quoc_gia,                // dia_chi.quoc_gia
     $thanh_pho,               // dia_chi.thanh_pho
     $huyen,                   // dia_chi.huyen
     $xa,                      // dia_chi.xa
     $ma_don_hang              // don_hang.ma_don_hang
 );

    if ($stmt->execute()) {
        echo "Đơn hàng và thông tin khách hàng đã được cập nhật thành công.";
    } else {
        echo "Lỗi khi cập nhật đơn hàng: " . $conn->error;
    }

}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/edit_order.css">
    <title>Cập nhật Đơn hàng</title>
</head>
<body>

<h2>Cập nhật thông tin đơn hàng</h2>

<form method="POST">
    <input type="hidden" name="ma_don_hang" value="<?php echo $ma_don_hang; ?>">

    <label for="ngay_dat">Ngày đặt:</label>
    <input type="date" id="ngay_dat" name="ngay_dat" value="<?php echo $ngay_dat; ?>" required><br><br>

    <label for="tinh_trang_thanh_toan">Tình trạng thanh toán:</label>
    <select name="tinh_trang_thanh_toan" id="tinh_trang_thanh_toan">
        <option value="Đã thanh toán" <?php echo ($tinh_trang_thanh_toan == 'Đã thanh toán' ? 'selected' : ''); ?>>Đã thanh toán</option>
        <option value="Chưa thanh toán" <?php echo ($tinh_trang_thanh_toan == 'Chưa thanh toán' ? 'selected' : ''); ?>>Chưa thanh toán</option>
    </select><br><br>

    <label for="tinh_trang_giao_hang">Tình trạng giao hàng:</label>
    <select name="tinh_trang_giao_hang" id="tinh_trang_giao_hang">
        <option value="Đã giao" <?php echo ($tinh_trang_giao_hang == 'Đã giao' ? 'selected' : ''); ?>>Đã giao</option>
        <option value="Chưa giao" <?php echo ($tinh_trang_giao_hang == 'Chưa giao' ? 'selected' : ''); ?>>Chưa giao</option>
    </select><br><br>

    <label for="gia">Giá sản phẩm:</label>
    <input type="number" id="gia" name="gia" value="<?php echo $gia; ?>" required><br><br>

    <label for="ten_khach_hang">Tên khách hàng:</label>
    <input type="text" id="ten_khach_hang" name="ten_khach_hang" value="<?php echo $ten_khach_hang; ?>" required><br><br>

    <label for="mail">Email:</label>
    <input type="email" id="mail" name="mail" value="<?php echo $mail; ?>" required><br><br>

    <label for="so_dien_thoai">Số điện thoại:</label>
    <input type="text" id="so_dien_thoai" name="so_dien_thoai" value="<?php echo $so_dien_thoai; ?>" required><br><br>

    <label for="quoc_gia">Quốc gia:</label>
    <input type="text" id="quoc_gia" name="quoc_gia" value="<?php echo $quoc_gia; ?>" required><br><br>

    <label for="thanh_pho">Thành phố:</label>
    <input type="text" id="thanh_pho" name="thanh_pho" value="<?php echo $thanh_pho; ?>" required><br><br>

    <label for="huyen">Huyện:</label>
    <input type="text" id="huyen" name="huyen" value="<?php echo $huyen; ?>" required><br><br>

    <label for="xa">Xã:</label>
    <input type="text" id="xa" name="xa" value="<?php echo $xa; ?>" required><br><br>

    <button type="submit" name="update_order">Cập nhật</button>
</form>

</body>
</html>
