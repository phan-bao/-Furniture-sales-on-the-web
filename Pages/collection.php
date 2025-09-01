<!DOCTYPE html>
<html lang="vi" spellcheck="false">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/footer.css">
    <link rel="stylesheet" href="../Css/collection.css">
    <title>Bộ Sưu Tập</title>
</head>

<body>
    <?php include '../Partials/header.php'; ?>
    <section class="showcase">
        <div class="container">
            <h1>Chào Mừng Đến Với Bộ Sưu Tập Nội Thất</h1>
            <p>Khám phá những sản phẩm nội thất tuyệt vời của chúng tôi</p>
        </div>
    </section>

    <section class="collection container">
        <?php

        $conn = new mysqli("localhost", "root", "", "furniture_store");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT SKU_san_pham, ten_san_pham FROM san_pham";
        $result = $conn->query($sql);

        $products = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }

        $conn->close();
        ?>
        <div class="collection-item">
            <img src="../Images/ke sach.jpg" alt="Kệ Sách">
            <h3>Kệ Sách</h3>
            <p>Một chiếc kệ sách đẹp và tiện dụng cho không gian của bạn.</p>
            <a href="../Pages/collection_detail.php?tag=Kệ">Xem Thêm</a>
        </div>
        <div class="collection-item">
            <img src="../Images/iuong ngu.webp" alt="Giường Ngủ">
            <h3>Giường Ngủ</h3>
            <p>Giường ngủ thoải mái và hiện đại cho giấc ngủ ngon.</p>
            <a href="../Pages/collection_detail.php?tag=Giường">Xem Thêm</a>
        </div>
        <div class="collection-item">
            <img src="../Images/WAR141.JPG" alt="Tủ Quần Áo">
            <h3>Tủ Quần Áo</h3>
            <p>Tủ quần áo rộng rãi và phong cách cho phòng ngủ của bạn.</p>
            <a href="../Pages/collection_detail.php?tag=Tủ">Xem Thêm</a>
        </div>
    </section>
    <?php include '../Partials/footer.php'; ?>
</body>

</html>