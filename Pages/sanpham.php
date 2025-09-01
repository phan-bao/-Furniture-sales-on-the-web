<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm</title>
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/sanpham1.css">
    <link rel="stylesheet" href="../Css/footer.css">

</head>

<body>

    <?php include '../Partials/header.php'; ?>

    <div class="main-container">
        <!-- Bộ lọc -->
        <div class="filter-container">
            <form method="GET" action="sanpham.php">
                <div class="search-box">
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..."
                        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                </div>

                <div class="filter-section">
                    <h3>Danh mục sản phẩm</h3>
                    <ul>
                        <li><input type="checkbox" name="tag[]" value="Giường"
                                <?php echo (isset($_GET['tag']) && in_array('Giường', $_GET['tag'])) ? 'checked' : ''; ?>>
                            Giường</li>
                        <li><input type="checkbox" name="tag[]" value="Tủ quần áo"
                                <?php echo (isset($_GET['tag']) && in_array('Tủ quần áo', $_GET['tag'])) ? 'checked' : ''; ?>>
                            Tủ quần áo</li>
                        <li><input type="checkbox" name="tag[]" value="Bàn"
                                <?php echo (isset($_GET['tag']) && in_array('Bàn', $_GET['tag'])) ? 'checked' : ''; ?>>
                            Bàn</li>
                        <li><input type="checkbox" name="tag[]" value="Kệ"
                                <?php echo (isset($_GET['tag']) && in_array('Kệ', $_GET['tag'])) ? 'checked' : ''; ?>>
                            Kệ</li>
                        <li><input type="checkbox" name="tag[]" value="Sofa"
                                <?php echo (isset($_GET['tag']) && in_array('Sofa', $_GET['tag'])) ? 'checked' : ''; ?>>
                            Sofa</li>
                    </ul>
                </div>

                <div class="filter-buttons">
                    <button class="filter-btn" type="submit">Lọc</button>
                    <button class="clear-filter-btn" type="button" onclick="window.location.href='sanpham.php';">Xóa bộ
                        lọc</button>
                </div>
            </form>
        </div>

        <!-- Hiển thị sản phẩm -->
        <div class="product-container">
            <?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "furniture_store");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy giá trị từ form
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$tags = isset($_GET['tag']) ? $_GET['tag'] : [];

// Truy vấn sản phẩm cùng thông tin khuyến mãi và giá từ bảng phien_ban_san_pham
$sql = "SELECT sp.SKU_san_pham, sp.ten_san_pham, sp.mo_ta, sp.anh, 
               km.ti_le_phan_tram, km.gia_tri_so_tien, 
               COALESCE(vsp.gia, sp.gia) AS gia_phien_ban
        FROM san_pham sp
        LEFT JOIN ma_khuyen_mai km ON sp.ten_km = km.ten_km
        LEFT JOIN phien_ban_san_pham vsp ON sp.SKU_san_pham = vsp.SKU_san_pham
        WHERE 1=1";

// Thêm điều kiện tìm kiếm theo tên sản phẩm
if (!empty($searchTerm)) {
    $sql .= " AND sp.ten_san_pham LIKE '%" . $searchTerm . "%'";
}

// Thêm điều kiện lọc theo tag
if (!empty($tags)) {
    $tagConditions = [];
    foreach ($tags as $tag) {
        $tagConditions[] = "sp.tag = '" . $conn->real_escape_string($tag) . "'";
    }
    $sql .= " AND (" . implode(" OR ", $tagConditions) . ")";
}

// Sử dụng GROUP BY để chỉ lấy một phiên bản cho mỗi sản phẩm
$sql .= " GROUP BY sp.SKU_san_pham";  // Chỉ lấy sản phẩm duy nhất dựa trên SKU

$result = $conn->query($sql);


if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $gia_phien_ban = $row['gia_phien_ban'];  // Giá đã tính từ phien_ban_san_pham hoặc san_pham
        $gia_goc = $gia_phien_ban; // Mặc định là giá phiên bản
        $ti_le_phan_tram = $row['ti_le_phan_tram'];
        $gia_tri_so_tien = $row['gia_tri_so_tien'];
        $gia_giam = $gia_goc; // Mặc định là giá gốc

        // Kiểm tra giảm giá theo phần trăm hoặc số tiền
        if (!is_null($ti_le_phan_tram) && $ti_le_phan_tram > 0) {
            $gia_giam = $gia_goc - ($gia_goc * $ti_le_phan_tram / 100);
        } elseif (!is_null($gia_tri_so_tien) && $gia_tri_so_tien > 0) {
            $gia_giam = max(0, $gia_goc - $gia_tri_so_tien); // Đảm bảo giá không âm
        }

        // Hiển thị sản phẩm
        echo '<div class="product-item">';
        echo '<a href="chitietsanpham.php?sku=' . $row['SKU_san_pham'] . '">';

        // Nhãn giảm giá (hiển thị khi có giảm giá)
        if ($gia_giam < $gia_goc) {
            echo '<div class="discount-label">- ' . round((($gia_goc - $gia_giam) / $gia_goc) * 100) . '%</div>';
        }

        echo '<img src="../Images/' . $row['anh'] . '" alt="' . htmlspecialchars($row['ten_san_pham']) . '">';
        echo '<h4>' . htmlspecialchars($row['ten_san_pham']) . '</h4>';
        echo '<div class="product-rating">★★★★☆</div>'; // Đánh giá sao giả định

        // Hiển thị giá (giá gốc + giá giảm nếu có)
        if ($gia_giam < $gia_goc) {
            echo '<p class="original-price">' . number_format($gia_goc, 0, ',', '.') . ' VNĐ</p>';
            echo '<p class="current-price">' . number_format($gia_giam, 0, ',', '.') . ' VNĐ</p>';
        } else {
            echo '<p class="current-price">' . number_format($gia_goc, 0, ',', '.') . ' VNĐ</p>';
        }

        echo '<div class="product-info">';
        echo '<span class="sold">Đã bán: ' . rand(1, 50) . '</span>';
        echo '<span class="views">Lượt xem: ' . rand(50, 500) . '</span>';
        echo '</div>';
        echo '</a>';
        echo '</div>';
    }
} else {
    echo "<p>Không có sản phẩm nào phù hợp.</p>";
}

$conn->close();
?>


        </div>

    </div>
    <?php include '../Partials/footer.php'; ?>
</body>

</html>