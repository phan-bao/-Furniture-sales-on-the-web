<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website</title>

    <!-- Thêm Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Liên kết đến file CSS -->
    <link rel="stylesheet" href="../css/top-bar.css">
    <link rel="stylesheet" href="../css/header.css">

    <!-- Thêm font-awesome để có các icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <div class="overlay"></div>
    <!-- Phần header của website -->
    <header class="site-header">
        <div class="logo">
            <a href="../index.php">
                <img src="/BLISS/Images/logo.jpg" alt="Bliss" style="max-height: 60px; width: 60px;">
            </a>
        </div>

        <div class="bars-icon">
            <!-- Kiểm tra nếu người dùng đã đăng nhập -->
            <?php if (isset($_SESSION['username'])): ?>
            <a href="../Pages/account.php" class="account-icon"><i class="fas fa-user"></i></a>
            <!-- Thêm icon tài khoản -->
            <a href="../Pages/logout.php" class="logout-icon"><i class="fas fa-sign-out-alt"></i></a>
            <!-- Thêm icon đăng xuất -->
            <?php else: ?>
            <a href="../Pages/login.php" class="account-icon"><i class="fas fa-user"></i></a>
            <!-- Thêm icon tài khoản -->
            <?php endif; ?>
            <a href="../Pages/giohang.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a>
            <!-- Thêm icon giỏ hàng -->
            <a href="#" id="menuToggle" style="color: #000;">
                <i class="fas fa-bars"></i>
            </a>
        </div>

        <nav class="main-nav mobile">
            <ul>
                <li><a href="../index.php">Trang Chủ</a></li>
                <li><a href="../Pages/sanpham.php">Sản Phẩm</a></li>
                <li><a href="../Pages/contact.php">Liên Hệ</a></li>
                <li><a href="../Pages/blog.php">Tin Tức</a></li>
                <li><a href="../Pages/ktsanpham.php">Kiểm Tra Đơn Hàng</a></li>
            </ul>
        </nav>

        <nav class="main-nav desktop">
            <ul>
                <li><a href="../index.php">Trang Chủ</a></li>
                <li><a href="/Bliss/Pages/sanpham.php">Sản Phẩm</a></li>
                <li><a href="/Bliss/Pages/contact.php">Liên Hệ</a></li>
                <li><a href="/Bliss/Pages/blog.php">Tin Tức</a></li>
                <li><a href="/Bliss/Pages/ktsanpham.php">Kiểm Tra Đơn Hàng</a></li>
            </ul>
        </nav>

        <div class="header-icons">
            <a href="#"><i class="far fa-heart"></i></a>
            <a href="/Bliss/Pages/giohang.php"><i class="fas fa-shopping-cart"></i></a>
            <?php if (isset($_SESSION['username'])): ?>
            <a href="/Bliss/Pages/account.php"><i class="fas fa-user"></i></a>
            <a href="/Bliss/Pages/logout.php"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
            <a href="/Bliss/Pages/login.php"><i class="fas fa-user"></i></a>
            <?php endif; ?>
        </div>
    </header>

    <script>
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.querySelector('.main-nav.mobile');
    const overlay = document.querySelector('.overlay');

    // Thiết lập các thuộc tính CSS cho overlay ban đầu
    overlay.style.opacity = '0'; // Mặc định trong suốt
    overlay.style.transition = 'opacity 0.5s ease'; // Thêm hiệu ứng chuyển tiếp

    menuToggle.addEventListener('click', function() {
        mainNav.classList.toggle('show');

        if (mainNav.classList.contains('show')) {
            overlay.style.display = 'block'; // Hiển thị overlay
            setTimeout(() => {
                overlay.style.opacity = '1'; // Đặt độ mờ thành 1 sau khi hiển thị
            }, 10); // Thêm chút thời gian để đảm bảo CSS transition hoạt động
        } else {
            overlay.style.opacity = '0'; // Đặt độ mờ thành 0 để ẩn
            setTimeout(() => {
                overlay.style.display = 'none'; // Ẩn overlay sau khi chuyển tiếp
            }, 500); // Thời gian ẩn phải trùng khớp với thời gian chuyển tiếp
        }
    });

    document.addEventListener('click', function(event) {
        if (!mainNav.contains(event.target) && !menuToggle.contains(event.target)) {
            mainNav.classList.remove('show');
            overlay.style.opacity = '0'; // Đặt độ mờ thành 0 để ẩn
            setTimeout(() => {
                overlay.style.display = 'none'; // Ẩn overlay sau khi chuyển tiếp
            }, 500); // Thời gian ẩn phải trùng khớp với thời gian chuyển tiếp
        }
    });
    </script>
</body>

</html>