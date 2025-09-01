<?php
session_start();

?>

<?php include 'Partials/header.php'; ?>
<?php include 'Partials/slider.php'; ?>

<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/header.css">

<!-- Các phần tiếp theo của trang web -->
<section class="features-section">
    <div class="feature-box">
        <div class="feature-icon">
            <i class="fas fa-shipping-fast"></i>
        </div>
        <div class="feature-content">
            <h3>Miễn Phí Giao Hàng</h3>
            <p>Miễn phí vận chuyển cho đơn hàng trên 5,000,000 VND</p>
        </div>
    </div>

    <div class="vertical-divider"></div> <!-- Đường phân cách -->

    <div class="feature-box">
        <div class="feature-icon">
            <i class="fas fa-plane"></i>
        </div>
        <div class="feature-content">
            <h3>Giao Hàng Trong Ngày</h3>
            <p>Chúng tôi giao hàng nhanh chóng trong 24 giờ.</p>
        </div>
    </div>

    <div class="vertical-divider"></div> <!-- Đường phân cách -->

    <div class="feature-box">
        <div class="feature-icon">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="feature-content">
            <h3>Thanh Toán Linh Hoạt</h3>
            <p>Nhiều tùy chọn thanh toán an toàn.</p>
        </div>
    </div>

    <div class="vertical-divider"></div> <!-- Đường phân cách -->

    <div class="feature-box">
        <div class="feature-icon">
            <i class="fas fa-headset"></i>
        </div>
        <div class="feature-content">
            <h3>Hỗ Trợ 24/7</h3>
            <p>Chúng tôi hỗ trợ trực tuyến mọi ngày.</p>
        </div>
    </div>
</section>

<?php include 'Partials/footer.php'; ?>