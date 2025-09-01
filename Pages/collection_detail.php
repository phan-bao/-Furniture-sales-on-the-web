<?php

$conn = new mysqli('localhost', 'root', '', 'furniture_store');


if ($conn->connect_error) {
    die("Connection failed: {$conn->connect_error}");
}


$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$tags = isset($_GET['tag']) ? $_GET['tag'] : '';


$tagArray = is_array($tags) ? $tags : [$tags];


$sql = "SELECT sp.SKU_san_pham, sp.ten_san_pham, sp.mo_ta, sp.anh, 
               km.ti_le_phan_tram, km.gia_tri_so_tien, 
               COALESCE(vsp.gia, sp.gia) AS gia_phien_ban
        FROM san_pham sp
        LEFT JOIN ma_khuyen_mai km ON sp.ten_km = km.ten_km
        LEFT JOIN phien_ban_san_pham vsp ON sp.SKU_san_pham = vsp.SKU_san_pham
        WHERE 1=1";


$params = [];
$types = '';

if (!empty($searchTerm)) {
    $sql .= " AND sp.ten_san_pham LIKE ?";
    $types .= 's';
    $params[] = '%' . $searchTerm . '%';
}

if (!empty($tagArray) && $tagArray[0] !== '') {

    $tagConditions = [];
    foreach ($tagArray as $tag) {
        $tagConditions[] = "sp.tag = ?";
        $types .= 's';
        $params[] = $tag;
    }
    $sql .= " AND (" . implode(" OR ", $tagConditions) . ")";
}


$sql .= " GROUP BY sp.SKU_san_pham";


$stmt = $conn->prepare($sql);

if ($types && $params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Bộ Sưu Tập</title>
    <link rel="stylesheet" href="../Css/footer.css">
    <link rel="stylesheet" href="../Css/collection_detail.css">
</head>

<body>
    <?php include '../Partials/header.php'; ?>
    <header>
        <i class="fas fa-times close-icon" onclick="window.location.href='../Pages/collection.php'"></i>
        <h1>Chi Tiết Bộ Sưu Tập</h1>
    </header>
    <main>
        <div class="collection-detail">
            <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
            <?php
                    $gia_phien_ban = $product['gia_phien_ban'];
                    $gia_goc = $gia_phien_ban;
                    $ti_le_phan_tram = $product['ti_le_phan_tram'];
                    $gia_tri_so_tien = $product['gia_tri_so_tien'];
                    $gia_giam = $gia_goc;

                    if ($ti_le_phan_tram !== null && $ti_le_phan_tram > 0) {
                        $gia_giam = $gia_goc - $gia_goc * $ti_le_phan_tram / 100;
                    } elseif ($gia_tri_so_tien !== null && $gia_tri_so_tien > 0) {
                        $gia_giam = max(0, $gia_goc - $gia_tri_so_tien);
                    }
                    ?>
            <div class="product-item">
                <a href="chitietsanpham.php?sku=<?php echo $product['SKU_san_pham']; ?>">
                    <?php if ($gia_giam < $gia_goc): ?>
                    <div class="discount-label">- <?php echo round((($gia_goc - $gia_giam) / $gia_goc) * 100); ?>%</div>
                    <?php endif; ?>
                    <img src="../Images/<?php echo $product['anh']; ?>"
                        alt="<?php echo htmlspecialchars($product['ten_san_pham']); ?>">
                    <h4><?php echo htmlspecialchars($product['ten_san_pham']); ?></h4>
                    <div class="product-rating">★★★★☆</div>
                    <?php if ($gia_giam < $gia_goc): ?>
                    <p class="original-price"><?php echo number_format($gia_goc, 0, ',', '.'); ?> VNĐ</p>
                    <p class="current-price"><?php echo number_format($gia_giam, 0, ',', '.'); ?> VNĐ</p>
                    <?php else: ?>
                    <p class="current-price"><?php echo number_format($gia_goc, 0, ',', '.'); ?> VNĐ</p>
                    <?php endif; ?>
                    <div class="product-info">
                        <span class="sold">Đã bán: <?php echo rand(1, 50); ?></span>
                        <span class="views">Lượt xem: <?php echo rand(50, 500); ?></span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>Không có sản phẩm nào phù hợp.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php include '../Partials/footer.php'; ?>
</body>

</html>