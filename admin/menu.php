<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/menu.css">
    <title>Responsive Dashboard Design #1 | AsmrProg</title>
</head>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Lấy URL hiện tại (bao gồm cả tên file)
    const currentPage = window.location.pathname.split('/').pop();
    console.log("Current Page:", currentPage); // Kiểm tra đường dẫn của trang hiện tại

    // Lấy tất cả các mục trong sidebar
    const sidebarItems = document.querySelectorAll('aside .sidebar a');

    // Lặp qua tất cả các mục và thêm class 'active' vào mục có href khớp với URL
    sidebarItems.forEach(item => {
        const itemPath = item.getAttribute('href').split('/').pop(); // Lấy tên file từ href
        console.log("Item href:", itemPath); // Kiểm tra href của các mục

        // So sánh tên file của trang hiện tại với href của mục menu
        if (currentPage === itemPath) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
});
</script>

<body>

    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="">
                    <img src="">
                    <h2>B<span class="danger">LISS</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <a href="../admin/dashboard.php">
                    <span class="material-icons-sharp">
                        insights
                    </span>
                    <h3>Tổng Quan</h3>
                </a>
                <a href="../admin/donhang.php">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Đơn Hàng</h3>
                </a>
                <a href="../admin/khachhang.php">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Khách Hàng</h3>
                </a>
                <a href="../admin/khuyenmai.php">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>Khuyến Mãi</h3>
                </a>

                <a href="../admin/sanpham.php">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Sản Phẩm</h3>

                </a>

                <a href="../admin/BlogAdmin.php">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Blog</h3>
                </a>

                <a href="#">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>New Login</h3>
                </a>
                <a href="logoutadmin.php">
                    <span class="material-icons-sharp">
                        logout
                    </span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

</body>

</html>