<?php
include 'db_connect.php'; // Đảm bảo đường dẫn chính xác tới db_connect.php
session_start(); // Khởi tạo session

// Lấy SKU sản phẩm từ URL hoặc từ form
$sku = isset($_GET['sku']) ? $_GET['sku'] : '';

// Kiểm tra nếu SKU không tồn tại
if (empty($sku)) {
    echo "SKU không hợp lệ!";
    exit;
}

// Lấy thông tin sản phẩm từ cơ sở dữ liệu
$sql_product = $conn->prepare("SELECT * FROM san_pham WHERE SKU_san_pham = ?");
$sql_product->bind_param("s", $sku);
$sql_product->execute();
$result_product = $sql_product->get_result();

if ($result_product->num_rows > 0) {
    $product = $result_product->fetch_assoc();
} else {
    echo "Sản phẩm không tồn tại!";
    exit;
}

// Lấy thông tin các phiên bản sản phẩm từ cơ sở dữ liệu
$sql_versions = $conn->prepare("SELECT * FROM phien_ban_san_pham WHERE SKU_san_pham = ?");
$sql_versions->bind_param("s", $sku);
$sql_versions->execute();
$result_versions = $sql_versions->get_result();

// Xử lý khi người dùng gửi form cập nhật
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ten_san_pham = $_POST['ten_san_pham'];
    $mo_ta = $_POST['mo_ta'];
    $noi_dung = $_POST['noi_dung'];
    $tag = $_POST['tag'];
    $anh = $product['anh']; // Mặc định ảnh cũ

    // Xử lý ảnh sản phẩm nếu có
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] === 0) {
        $imageName = $_FILES['anh']['name'];
        $imageTmpName = $_FILES['anh']['tmp_name'];
        $imageSize = $_FILES['anh']['size'];
        $imageError = $_FILES['anh']['error'];

        // Kiểm tra nếu có lỗi khi tải ảnh
        if ($imageError === 0) {
            // Lấy phần mở rộng của ảnh
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Kiểm tra nếu định dạng ảnh hợp lệ
            if (!in_array($imageExtension, $allowedExtensions)) {
                echo json_encode(["status" => "error", "message" => "Định dạng tệp ảnh không hợp lệ."]);
                exit;
            }

            // Kiểm tra kích thước ảnh (ví dụ giới hạn 5MB)
            $maxSize = 5 * 1024 * 1024;  // 5MB
            if ($imageSize > $maxSize) {
                echo json_encode(["status" => "error", "message" => "Ảnh quá lớn. Vui lòng chọn ảnh có kích thước nhỏ hơn 5MB."]);
                exit;
            }

            // Tạo tên ảnh mới để tránh trùng lặp
            $newImageName = uniqid('', true) . "." . $imageExtension;
            $imageDestination = "../Images/" . $newImageName;

            // Di chuyển ảnh từ thư mục tạm tới thư mục lưu trữ
            if (!move_uploaded_file($imageTmpName, $imageDestination)) {
                echo json_encode(["status" => "error", "message" => "Không thể tải ảnh lên."]);
                exit;
            }

            // Đổi giá trị của $anh thành tên ảnh mới
            $anh = $newImageName;
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi khi tải ảnh lên."]);
            exit;
        }
    }

    // Cập nhật thông tin sản phẩm
    $sql_update_product = $conn->prepare("UPDATE san_pham SET 
        ten_san_pham=?, 
        mo_ta=?, 
        noi_dung=?, 
        anh=?, 
        tag=? 
        WHERE SKU_san_pham=?");

    $sql_update_product->bind_param("ssssss", $ten_san_pham, $mo_ta, $noi_dung, $anh, $tag, $sku);

    if ($sql_update_product->execute()) {
        $status = 'success'; // Cập nhật thành công
    } else {
        $status = 'error'; // Có lỗi trong khi cập nhật
    }
    

    // Cập nhật giá và số lượng tồn kho cho các phiên bản sản phẩm
    if (isset($_POST['versions'])) {
        foreach ($_POST['versions'] as $version_id => $version_data) {
            $mau_sac = $version_data['mau_sac'];
            $vat_lieu = $version_data['vat_lieu'];
            $kich_thuoc = $version_data['kich_thuoc'];
            $gia = $version_data['gia'];  // Giá cho từng phiên bản
            $so_luong_ton_kho = $version_data['so_luong_ton_kho'];  // Số lượng tồn kho cho phiên bản

            // Kiểm tra xem giá và số lượng tồn kho đã được nhập hay chưa
            if (empty($gia)) {
                echo "Giá cho phiên bản #$version_id không được để trống!";
                exit;
            }

            // Cập nhật giá và số lượng tồn kho cho phiên bản sản phẩm
            $sql_update_version = $conn->prepare("UPDATE phien_ban_san_pham SET 
                mau_sac=?, 
                vat_lieu=?, 
                kich_thuoc=?, 
                gia=?, 
                so_luong_ton_kho=? 
                WHERE ma_phien_ban=?");

            $sql_update_version->bind_param("ssssii", $mau_sac, $vat_lieu, $kich_thuoc, $gia, $so_luong_ton_kho, $version_id);

            if ($sql_update_version->execute() !== TRUE) {
                echo "Lỗi cập nhật phiên bản: " . $conn->error;
            }
        }
    }
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/update_product.css">
    <title>Sửa Sản Phẩm</title>
</head>
<body>

<h1>Sửa Sản Phẩm</h1>

<!-- Form sửa sản phẩm -->
<form method="POST" action="" enctype="multipart/form-data">
    <label for="ten_san_pham">Tên Sản Phẩm:</label>
    <input type="text" name="ten_san_pham" value="<?php echo $product['ten_san_pham']; ?>" required><br><br>

    <label for="mo_ta">Mô Tả:</label>
    <textarea name="mo_ta"><?php echo $product['mo_ta']; ?></textarea><br><br>

    <label for="noi_dung">Nội Dung:</label>
    <textarea name="noi_dung"><?php echo $product['noi_dung']; ?></textarea><br><br>

    <label for="anh">Ảnh:</label>
    <input type="file" name="anh"><br><br>
    <p><img src="../Images/<?php echo $product['anh']; ?>" alt="Ảnh sản phẩm" width="100"></p>

    <label for="tag">Tag:</label>
    <input type="text" name="tag" value="<?php echo $product['tag']; ?>"><br><br>

    <h3>Phiên Bản Sản Phẩm</h3>
    <?php while ($version = $result_versions->fetch_assoc()) { ?>
        <div>
            <h4>Phiên bản #<?php echo $version['ma_phien_ban']; ?></h4>
            <label for="mau_sac_<?php echo $version['ma_phien_ban']; ?>">Màu sắc:</label>
            <input type="text" name="versions[<?php echo $version['ma_phien_ban']; ?>][mau_sac]" value="<?php echo $version['mau_sac']; ?>"><br><br>

            <label for="vat_lieu_<?php echo $version['ma_phien_ban']; ?>">Vật liệu:</label>
            <input type="text" name="versions[<?php echo $version['ma_phien_ban']; ?>][vat_lieu]" value="<?php echo $version['vat_lieu']; ?>"><br><br>

            <label for="kich_thuoc_<?php echo $version['ma_phien_ban']; ?>">Kích thước:</label>
            <input type="text" name="versions[<?php echo $version['ma_phien_ban']; ?>][kich_thuoc]" value="<?php echo $version['kich_thuoc']; ?>"><br><br>

            <label for="gia_<?php echo $version['ma_phien_ban']; ?>">Giá:</label>
            <input type="text" name="versions[<?php echo $version['ma_phien_ban']; ?>][gia]" value="<?php echo $version['gia']; ?>"><br><br>

            <label for="so_luong_ton_kho_<?php echo $version['ma_phien_ban']; ?>">Số lượng tồn kho:</label>
            <input type="number" name="versions[<?php echo $version['ma_phien_ban']; ?>][so_luong_ton_kho]" value="<?php echo $version['so_luong_ton_kho']; ?>"><br><br>
        </div>
    <?php } ?>

    <input type="submit" value="Cập nhật sản phẩm">
</form>

</body>
<script>
    // Kiểm tra xem thông báo đã được hiển thị chưa
    if (!sessionStorage.getItem('notificationShown')) {
        function showNotification(message, isError = false) {
            const notification = document.createElement("div");
            notification.classList.add("notification");

            if (isError) {
                notification.classList.add("error");
            }

            notification.innerHTML = message;
            document.body.appendChild(notification);

            // Hiển thị thông báo
            notification.style.display = "block";

            // Sau 5 giây, ẩn thông báo và reload trang nếu thành công
            setTimeout(function() {
                notification.style.display = "none";

                // Nếu không có lỗi, tự động reload trang
                if (!isError) {
                    sessionStorage.setItem('notificationShown', 'true');  // Lưu trạng thái thông báo
                    location.reload(); // Reload trang sau 5 giây
                }
            }, 5000);
        }

        // Gọi hàm khi có kết quả từ PHP
        <?php if (isset($status) && $status == 'success') { ?>
            showNotification('Cập nhật sản phẩm thành công!', false);
        <?php } elseif (isset($status) && $status == 'error') { ?>
            showNotification('Có lỗi xảy ra! Vui lòng thử lại.', true);
        <?php } ?>
    }
</script>

</html>
