<?php
// Kết nối đến MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

// Tạo kết nối đến MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Bắt đầu session
session_start();

// Xử lý yêu cầu POST
$message = ""; // Khởi tạo biến $message
$message_type = "success"; // Mặc định là thông báo thành công
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "register") {
        // Xử lý đăng ký
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $so_dien_thoai = mysqli_real_escape_string($conn, $_POST['so_dien_thoai']); // Thêm dòng này
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Kiểm tra xem email hoặc số điện thoại đã tồn tại chưa
        $checkQuery = "SELECT * FROM khach_hang WHERE mail=? OR so_dien_thoai=?";
        if ($stmt = $conn->prepare($checkQuery)) {
            $stmt->bind_param("ss", $email, $so_dien_thoai);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Email hoặc số điện thoại đã tồn tại!";
                $message_type = "error";
            } else {
                // Thêm khách hàng mới vào bảng khach_hang
                $insertQuery = "INSERT INTO khach_hang (ten_khach_hang, mail, so_dien_thoai) VALUES (?, ?, ?)";
                if ($stmt = $conn->prepare($insertQuery)) {
                    $stmt->bind_param("sss", $username, $email, $so_dien_thoai); // Thêm so_dien_thoai
                    if ($stmt->execute()) {
                        $last_id = $conn->insert_id;

                        // Thêm tài khoản mới vào bảng account
                        $insertAccountQuery = "INSERT INTO account (ma_khach_hang, username, password) VALUES (?, ?, ?)";
                        if ($stmt = $conn->prepare($insertAccountQuery)) {
                            $stmt->bind_param("iss", $last_id, $username, $password);
                            if ($stmt->execute()) {
                                $message = "Đăng ký thành công!";
                                $message_type = "success";
                            } else {
                                $message = "Lỗi: " . $stmt->error;
                                $message_type = "error";
                            }
                        } else {
                            $message = "Lỗi chuẩn bị truy vấn: " . $conn->error;
                            $message_type = "error";
                        }
                    } else {
                        $message = "Lỗi: " . $stmt->error;
                        $message_type = "error";
                    }
                } else {
                    $message = "Lỗi chuẩn bị truy vấn: " . $conn->error;
                    $message_type = "error";
                }
            }
        } else {
            $message = "Lỗi chuẩn bị truy vấn: " . $conn->error;
            $message_type = "error";
        }

    } elseif ($action == "login") {
        // Xử lý đăng nhập
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Kiểm tra tài khoản và mật khẩu
        $query = "SELECT account.ma_khach_hang, account.password, khach_hang.ten_khach_hang FROM account INNER JOIN khach_hang ON account.ma_khach_hang = khach_hang.ma_khach_hang WHERE account.username=?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Lưu thông tin vào session
                    $_SESSION['ma_khach_hang'] = $row['ma_khach_hang'];
                    $_SESSION['username'] = $username;
                    $_SESSION['ten_khach_hang'] = $row['ten_khach_hang'];

                    // Chuyển hướng đến trang chính
                    header("Location: ../index.php");
                    exit();
                } else {
                    $message = "Sai mật khẩu!";
                    $message_type = "error";
                }
            } else {
                $message = "Tài khoản không tồn tại!";
                $message_type = "error";
            }
        } else {
            $message = "Lỗi chuẩn bị truy vấn: " . $conn->error;
            $message_type = "error";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../Css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Sign in & Sign up Form</title>
</head>

<body>
    <?php if (!empty($message)): ?>
    <div class="message <?php echo $message_type; ?>">
        <p><?php echo $message; ?></p>
    </div>
    <?php endif; ?>

    <div class="container">
        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" id="login-tab">Đăng Nhập</button>
            <button class="tab" id="register-tab">Đăng Ký</button>
        </div>
        <!-- Forms -->
        <div class="forms">
            <!-- Form Đăng Nhập -->
            <form action="login.php" method="POST" class="form active" id="login-form">
                <h2 class="title">Đăng Nhập</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Tên người dùng" required />
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="login-password" placeholder="Mật khẩu" required />
                    <i class="fas fa-eye" id="toggle-password" style="cursor: pointer;"></i>
                </div>


                <!-- Remember Me và Forgot Password -->
                <div class="options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember_me" />
                        Nhớ mật khẩu
                    </label>
                    <a href="forgot_password.php" class="forgot-password">Quên mật khẩu?</a>
                </div>

                <input type="hidden" name="action" value="login">
                <button type="submit" class="btn solid">Đăng Nhập</button>
                <p class="social-text">Hoặc đăng nhập bằng </p>
                <div class="social-media">
                    <a href="../Pages/fblogin.php" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                </div>
            </form>

            <!-- Form Đăng Ký -->
            <form action="login.php" method="POST" class="form" id="register-form">
                <h2 class="title">Đăng Ký</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Tên người dùng" required />
                </div>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required />
                </div>
                <div class="input-field">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="so_dien_thoai" placeholder="Số điện thoại" required />
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Mật khẩu" required />
                </div>
                <input type="hidden" name="action" value="register">
                <button type="submit" class="btn solid">Đăng Ký</button>
            </form>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script>
    // JavaScript để chuyển đổi giữa đăng nhập và đăng ký
    document.addEventListener('DOMContentLoaded', () => {
        const loginTab = document.getElementById('login-tab');
        const registerTab = document.getElementById('register-tab');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        loginTab.addEventListener('click', () => {
            loginTab.classList.add('active');
            registerTab.classList.remove('active');
            loginForm.classList.add('active');
            registerForm.classList.remove('active');
        });

        registerTab.addEventListener('click', () => {
            registerTab.classList.add('active');
            loginTab.classList.remove('active');
            registerForm.classList.add('active');
            loginForm.classList.remove('active');
        });
    });

    // Tự động ẩn thông báo sau 3 giây
    setTimeout(function() {
        var message = document.querySelector('.message');
        if (message) {
            message.classList.add('hidden');
        }
    }, 3000);

    document.addEventListener('DOMContentLoaded', () => {
        const togglePassword = document.getElementById('toggle-password');
        const passwordField = document.getElementById('login-password');

        togglePassword.addEventListener('click', () => {
            // Kiểm tra nếu mật khẩu đang ở dạng 'password', thay đổi thành 'text'
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                togglePassword.classList.replace('fa-eye', 'fa-eye-slash'); // Thay đổi biểu tượng mắt
            } else {
                passwordField.type = 'password';
                togglePassword.classList.replace('fa-eye-slash', 'fa-eye'); // Đổi lại biểu tượng mắt
            }
        });
    });
    </script>
</body>

</html>