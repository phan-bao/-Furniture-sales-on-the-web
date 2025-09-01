<?php
include 'db_connect.php';
session_start(); // Khởi tạo session

// Kiểm tra xem có truyền SKU qua URL không
if (isset($_GET['sku'])) {
    $sku = $_GET['sku'];

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Xóa các bản ghi trong bảng phien_ban_san_pham liên quan đến SKU
        $sql_delete_phienban = "DELETE FROM phien_ban_san_pham WHERE SKU_san_pham = ?";
        $stmt = $conn->prepare($sql_delete_phienban);
        $stmt->bind_param("s", $sku);
        $stmt->execute();

        // Sau khi xóa các bản ghi liên quan, xóa sản phẩm khỏi bảng san_pham
        $sql_delete_sanpham = "DELETE FROM san_pham WHERE SKU = ?";
        $stmt = $conn->prepare($sql_delete_sanpham);
        $stmt->bind_param("s", $sku);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        // Thiết lập session để thông báo thành công
        $_SESSION['delete_success'] = true;

        // Chuyển hướng về trang danh sách sản phẩm
        header("Location: ../admin/sanpham.php");
        exit; // Dừng script sau khi chuyển hướng

    } catch (Exception $e) {
        // Nếu có lỗi, rollback transaction
        $conn->rollback();
        echo "Lỗi khi xóa sản phẩm: " . $e->getMessage();
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();
} else {
    echo "Không có SKU sản phẩm để xóa.";
}
?>
<?php
session_start(); // Khởi tạo session

// Kiểm tra thông báo xóa thành công
if (isset($_SESSION['delete_success']) && $_SESSION['delete_success']) {
    echo '<script type="text/javascript">
        window.onload = function() {
            showToast();
        };
    </script>';
    unset($_SESSION['delete_success']); // Xóa session sau khi hiển thị thông báo
}
?>
