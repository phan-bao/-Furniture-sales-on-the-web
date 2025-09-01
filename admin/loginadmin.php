<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập Admin</title>
    <link rel="stylesheet" href="../Css/loginadmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div class="login-container">
    <h2>Đăng Nhập</h2>
    <?php
    // Hiển thị thông báo lỗi nếu có
    if (isset($_GET['error'])) {
        echo '<div class="error">Tên đăng nhập hoặc mật khẩu không chính xác.</div>';
    }
    ?>
    <form action="login_process.php" method="post">
        <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" id="username" name="username" placeholder="Tên Đăng Nhập" required>
        </div>

        <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Mật Khẩu" required>
            <i class="fas fa-eye toggle-password" id="toggle-password" style="cursor: pointer;"></i>
        </div>

        <button type="submit" class="btn">Đăng Nhập</button>
    </form>
</div>

<script>
    // JavaScript để ẩn/hiện mật khẩu
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        // Thay đổi biểu tượng con mắt
        togglePassword.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>
