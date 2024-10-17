<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website</title>

    <!-- Thêm Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Liên kết đến file CSS -->
    <link rel="stylesheet" href="css/top-bar.css">
    <link rel="stylesheet" href="css/header.css">
    
    <!-- Thêm font-awesome để có các icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <!-- Gọi phần top-bar từ file top-bar.php -->
    <?php include 'top-bar.php'; ?>
    
    <!-- Phần header của website -->
    <header class="site-header">
        <div class="logo">
            <a href="#">Bliss</a>
        </div>
        <nav class="main-nav">
            <ul>
            <li><a href="../index.php">Trang Chủ</a></li>
                <li><a href="Pages/sanpham.php">Sản Phẩm</a></li>
                <li><a href="">Liên Hệ</a></li>
                <li><a href="#">Tin Tức</a></li>
                <li><a href="#">Kiểm Tra Đơn Hàng</a></li>
            </ul>
        </nav>
        <div class="header-icons">
            <a href="#"><i class="fas fa-search"></i></a>
            <a href="#"><i class="far fa-heart"></i></a>
            <a href="#"><i class="fas fa-shopping-cart"></i></a>
            <a href="Pages/login.php"><i class="fas fa-user"></i></a>
        </div>
    </header>
</body>
</html>
