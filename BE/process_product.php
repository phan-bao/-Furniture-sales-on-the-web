<?php
// Bắt đầu phiên
session_start();

// Bao gồm kết nối cơ sở dữ liệu
include 'db_connect.php';

// Kiểm tra nếu có dữ liệu gửi từ client (thông qua phương thức POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu chung cho sản phẩm
    $sku = isset($_POST['sku']) ? htmlspecialchars($_POST['sku']) : '';
    $ten_san_pham = isset($_POST['ten_san_pham']) ? htmlspecialchars($_POST['ten_san_pham']) : '';
    $gia = isset($_POST['gia_version']) ? $_POST['gia_version'][0] : ''; // Lấy giá từ phiên bản đầu tiên
    $mo_ta = isset($_POST['mo_ta']) ? htmlspecialchars($_POST['mo_ta']) : '';
    $noi_dung = isset($_POST['noi_dung']) ? htmlspecialchars($_POST['noi_dung']) : '';
    $tag = isset($_POST['tag']) ? htmlspecialchars($_POST['tag']) : '';

    // Kiểm tra nếu tất cả các trường bắt buộc đã được điền đầy đủ
    if (empty($sku) || empty($ten_san_pham) || empty($gia) || empty($mo_ta) || empty($noi_dung)) {
        echo json_encode(["status" => "error", "message" => "Các trường không được để trống."]);
        exit;
    }

    // Xử lý ảnh sản phẩm nếu có
    $imageName = "";
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] === 0) {
        $imageName = $_FILES['anh']['name'];
        $imageTmpName = $_FILES['anh']['tmp_name'];
        $imageSize = $_FILES['anh']['size'];
        $imageError = $_FILES['anh']['error'];

        if ($imageError === 0) {
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($imageExtension, $allowedExtensions)) {
                echo json_encode(["status" => "error", "message" => "Định dạng tệp ảnh không hợp lệ."]);
                exit;
            }

            $newImageName = uniqid('', true) . "." . $imageExtension;
            $imageDestination = "../Images/" . $newImageName;
            
            if (!move_uploaded_file($imageTmpName, $imageDestination)) {
                echo json_encode(["status" => "error", "message" => "Không thể tải ảnh lên."]);
                exit;
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi khi tải ảnh lên."]);
            exit;
        }
    }

    // Thêm sản phẩm vào bảng `san_pham`
    $sql = "INSERT INTO san_pham (SKU_san_pham, ten_san_pham, gia, mo_ta, noi_dung, anh, tag) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsdss", $sku, $ten_san_pham, $gia, $mo_ta, $noi_dung, $newImageName, $tag);

    if ($stmt->execute()) {
        // Thêm các phiên bản sản phẩm vào bảng `phien_ban_san_pham`
        if (isset($_POST['mau_sac']) && is_array($_POST['mau_sac'])) {
            $mau_sac = $_POST['mau_sac'];
            $kich_thuoc = $_POST['kich_thuoc'];
            $vat_lieu = $_POST['vat_lieu'];
            $gia_version = $_POST['gia_version'];

            // Lặp qua các phiên bản và thêm vào bảng `phien_ban_san_pham`
            foreach ($mau_sac as $key => $value) {
                if (isset($vat_lieu[$key])) {
                    $vat_lieu_value = $vat_lieu[$key];
                } else {
                    echo json_encode(["status" => "error", "message" => "Vật liệu không tồn tại cho phiên bản này."]);
                    exit;
                }

                // Tạo SKU cho phiên bản, thêm số ngẫu nhiên để đảm bảo tính duy nhất
                $sku_phien_ban = $sku . '-vb' . ($key + 1) . '-' . uniqid(); // Thêm `uniqid()` để tạo SKU duy nhất

                // Kiểm tra nếu `SKU_phien_ban` đã tồn tại trong bảng `phien_ban_san_pham`
                $check_sql = "SELECT COUNT(*) FROM phien_ban_san_pham WHERE SKU_phien_ban = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("s", $sku_phien_ban);
                $check_stmt->execute();
                $check_stmt->bind_result($count);
                $check_stmt->fetch();
                $check_stmt->close();

                if ($count > 0) {
                    // Nếu SKU đã tồn tại, tạo SKU mới (với số ngẫu nhiên hoặc thay đổi cách tạo SKU)
                    $sku_phien_ban = $sku . '-vb' . ($key + 1) . '-' . uniqid();
                }

                // Thêm phiên bản vào bảng `phien_ban_san_pham`
                $version_sql = "INSERT INTO phien_ban_san_pham (SKU_phien_ban, SKU_san_pham, mau_sac, kich_thuoc, vat_lieu, gia)
                                VALUES (?, ?, ?, ?, ?, ?)";
                $version_stmt = $conn->prepare($version_sql);
                $version_stmt->bind_param("ssssss", $sku_phien_ban, $sku, $mau_sac[$key], $kich_thuoc[$key], $vat_lieu[$key], $gia_version[$key]);

                if (!$version_stmt->execute()) {
                    echo json_encode(["status" => "error", "message" => "Lỗi khi thêm phiên bản sản phẩm: " . $conn->error]);
                    exit;
                }
            }
        }
        
        echo json_encode(["status" => "success", "message" => "Sản phẩm và phiên bản đã được thêm thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi thêm sản phẩm: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
