<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Bliss</title>
    
    <!-- Thêm các liên kết CSS -->
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/footer.css">
    <link rel="stylesheet" href="../Css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Font Awesome cho icon -->
</head>
<body>
<?php include '../Partials/header.php';
 ?>

    <div class="breadcrumb">
        <a href="../index.php">Trang Chủ</a> > <span>Liên hệ</span>
    </div>

    <div class="page-container">
        <!-- Container bên trái -->
        <div class="left-container">
            <div class="contact-info">
                <h2>CÔNG TY NỘI THẤT BLISS</h2>
                <p><i class="fas fa-map-marker-alt"></i><strong> Địa chỉ:</strong> 55 Đường số 39, Khu Đô thị Vạn Phúc, Hiệp Bình Chánh, Ho Chi Minh City</p>
                <p><i class="fas fa-envelope"></i><strong> Email:</strong> <a href="mailto:Longprotonq2@gmail.com">Longprotonq2@gmail.com</a></p>
                <p><i class="fas fa-phone"></i><strong> Hotline:</strong> 0344704QJK</p>
            </div>

            <div class="contact-form-container">
                <h3>LIÊN HỆ VỚI CHÚNG TÔI</h3>
                <form action="../admin/contact_process.php" method="POST">
                    <div class="form-group">
                        <input type="text" id="name" name="name" required>
                        <label for="name">Họ và tên</label>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" required>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-group">
                        <input type="tel" id="phone" name="phone" required>
                        <label for="phone">Điện thoại</label>
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" rows="4" required></textarea>
                        <label for="message">Nội Dung</label>
                    </div>
                    <button type="submit">Gửi thông tin</button>
                </form>
            </div>
        </div>

        <!-- Bản đồ bên phải -->
        <div class="contact-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15674.14337522358!2d106.6926054871582!3d10.846788799999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317527a84135a42b%3A0xfc10630dd7c158a0!2sScandiHome!5e0!3m2!1svi!2s!4v1731560930691!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

    <!-- JavaScript nhúng cho hiệu ứng Floating Label -->
    <script>
        document.querySelectorAll('.form-group input, .form-group textarea').forEach(element => {
            element.addEventListener('focus', () => {
                element.nextElementSibling.classList.add('focused');
            });
            element.addEventListener('blur', () => {
                if (element.value === "") {
                    element.nextElementSibling.classList.remove('focused');
                }
            });
        });
    </script>

</body>
<?php include '../Partials/footer.php'; ?>
</html>
