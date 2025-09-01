<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/themkhachhang.css">
    <title>Danh sách đơn hàng</title>
    <style>
        
    </style>    
</head>

<body>

<div class="container">
    
    <!-- Sidebar Section -->
    <aside>
        <div class="toggle">
            <div class="logo">
                <img src="../Images/logo1.png">
                <h2>B<span class="danger">LISS</span></h2>
            </div>
            <div class="close" id="close-btn">
                <span class="material-icons-sharp">close</span>
            </div>
        </div>

        <div class="sidebar">
            <a href="../admin/dashboard.php">
                <span class="material-icons-sharp">insights</span>
                <h3>Tổng Quan</h3>
            </a>
            <a href="../admin/donhang.php" > 
                <span class="material-icons-sharp">dashboard</span>
                <h3>Đơn Hàng</h3>
            </a>
            <a href="../admin/khachhang.php"  class="active">
                <span class="material-icons-sharp">person_outline</span>
                <h3>Khách Hàng</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">receipt_long</span>
                <h3>History</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">mail_outline</span>
                <h3>Tickets</h3>
                <span class="message-count">27</span>
            </a>
            <a href="#">
                <span class="material-icons-sharp">inventory</span>
                <h3>Sale List</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">report_gmailerrorred</span>
                <h3>Reports</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">settings</span>
                <h3>Settings</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">add</span>
                <h3>New Login</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">logout</span>
                <h3>Logout</h3>
            </a>
        </div>
        
    </aside>
    <!-- End of Sidebar Section -->
    <!-- Form Section -->
    <div class="form-container">
        <h2>Thêm Khách Hàng</h2>
        <form action="../BE/process_customer.php" method="POST">
    <input type="text" name="ho" placeholder="Họ" required>
    <input type="text" name="ten" placeholder="Tên" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="tel" name="sdt" placeholder="SĐT" required>
    <select name="quoc_gia" required>
        <option value="" disabled selected>Quốc Gia</option>
        <option value="Vietnam">Việt Nam</option>
        <option value="USA">Mỹ</option>
        <option value="Japan">Nhật Bản</option>
    </select>
    <input type="text" name="thanh_pho" placeholder="Thành Phố" required>
    <input type="text" name="huyen" placeholder="Huyện" required> 
    <input type="text" name="xa" placeholder="Xã" required>
    <button type="submit">Lưu</button>
</form>

    </div>
    <div id="notification" class="notification"></div>


</div>


    </body>
    <?php if (isset($_SESSION['notification'])): ?>
    <script>
        // Hiển thị thông báo
        const notification = document.getElementById('notification');
        notification.innerHTML = "<?php echo $_SESSION['notification']['message']; ?>";
        notification.classList.add("<?php echo $_SESSION['notification']['type']; ?>");
        notification.style.display = 'block';

        // Tự động ẩn thông báo sau 3 giây
        setTimeout(function() {
            notification.style.display = 'none';
        }, 3000);
    </script>
    <?php unset($_SESSION['notification']); // Xóa thông báo sau khi hiển thị ?>
<?php endif; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(document).ready(function() {
    $('form').on('submit', function(e) {
        e.preventDefault(); // Ngăn chặn form gửi thông thường

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                const notification = $('#notification');
                
                // Hiển thị thông báo với nội dung và kiểu lớp
                notification.html(response.message)
                            .removeClass('success error fadeOut')
                            .addClass(response.type + ' show')
                            .show();

                // Tự động thêm hiệu ứng mờ dần sau 3 giây
                setTimeout(function() {
                    notification.addClass('fadeOut');
                }, 3000);

                // Ẩn hoàn toàn sau khi hiệu ứng kết thúc
                setTimeout(function() {
                    notification.hide().removeClass('show fadeOut');
                }, 3500);
            },
            error: function() {
                const notification = $('#notification');
                
                notification.html('Lỗi khi thêm khách hàng.')
                            .removeClass('success error fadeOut')
                            .addClass('error show')
                            .show();

                setTimeout(function() {
                    notification.addClass('fadeOut');
                }, 3000);

                setTimeout(function() {
                    notification.hide().removeClass('show fadeOut');
                }, 3500);
            }
        });
    });
});

</script>

</html>
