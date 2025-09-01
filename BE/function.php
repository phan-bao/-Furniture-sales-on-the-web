<?php
// functions.php

function them_khach_hang($ten_khach_hang, $mail, $so_dien_thoai, $quoc_gia, $thanh_pho, $huyen, $xa, $conn) {
    // Chèn dữ liệu vào bảng Khách Hàng
    $sql = "INSERT INTO khach_hang (ten_khach_hang, mail, so_dien_thoai, quoc_gia, thanh_pho, huyen, xa) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $ten_khach_hang, $mail, $so_dien_thoai, $quoc_gia, $thanh_pho, $huyen, $xa);

    if ($stmt->execute()) {
        // Lấy ID tự động tăng vừa được chèn
        $last_id = $conn->insert_id;

        // Tạo ma_khach_hang theo định dạng "KHxxx"
        $ma_khach_hang = 'KH' . str_pad($last_id, 3, '0', STR_PAD_LEFT);

        // Cập nhật lại ma_khach_hang cho khách hàng vừa thêm
        $update_sql = "UPDATE khach_hang SET ma_khach_hang = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $ma_khach_hang, $last_id);
        $update_stmt->execute();

        return $ma_khach_hang;
    } else {
        return false;  // Trả về false nếu không thể thêm khách hàng
    }
}
?>
