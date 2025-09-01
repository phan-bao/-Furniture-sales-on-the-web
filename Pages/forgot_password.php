<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

session_start();

// Thông tin kết nối cơ sở dữ liệu MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

// Tạo kết nối MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Khởi tạo các biến lỗi
$email = $verification_code = $new_password = $confirm_password = "";
$message = "";
$message_type = "";

// Hàm gửi mã xác minh qua email
function sendVerificationCode($email, $verification_code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bxgangter@gmail.com';
        $mail->Password = 'cgac xxoj behj mdks'; // **Không nên lưu trực tiếp trong mã nguồn**
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('bxgangter@gmail.com', 'CTTNHH BLISS');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Mã xác minh của bạn';
        $mail->Body    = 'Mã xác minh của bạn là: <b>' . htmlspecialchars($verification_code) . '</b>';

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

// Hàm tạo mã xác minh ngẫu nhiên
function generateVerificationCode() {
    return bin2hex(random_bytes(4)); // Tạo mã ngẫu nhiên dài 8 ký tự (4 byte)
}

// Hàm kiểm tra xem email có tồn tại trong hệ thống không
function emailExists($conn, $email) {
    $check_email_query = "SELECT ma_khach_hang FROM khach_hang WHERE mail = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    return $exists;
}

// Kiểm tra và xử lý form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bước 1: Gửi mã xác minh qua email
    if (!isset($_SESSION['step']) || $_SESSION['step'] == 1) {
        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (emailExists($conn, $email)) {
                    $verification_code = generateVerificationCode();

                    // Gửi mã xác minh qua email
                    if (sendVerificationCode($email, $verification_code)) {
                        $_SESSION['email'] = $email;
                        $_SESSION['verification_code'] = $verification_code;
                        $_SESSION['verification_start_time'] = time();
                        $_SESSION['step'] = 2;
                        $message = "Mã xác minh đã được gửi đến email của bạn.";
                        $message_type = "success";
                    } else {
                        $message = "Có lỗi khi gửi email. Vui lòng thử lại.";
                        $message_type = "error";
                    }
                } else {
                    // Không tiết lộ email không tồn tại
                    // Chuyển sang bước 2 nhưng không thực hiện gì
                    $_SESSION['step'] = 2;
                    $message = "Mã xác minh đã được gửi đến email của bạn.";
                    $message_type = "success";
                }
            } else {
                $message = "Địa chỉ email không hợp lệ!";
                $message_type = "error";
            }
        } else {
            $message = "Vui lòng nhập email của bạn.";
            $message_type = "error";
        }
    }

    // Bước 2: Xác nhận mã
    if (isset($_POST['code']) && isset($_SESSION['step']) && $_SESSION['step'] == 2) {
        $input_code = trim($_POST['code']);
        $current_time = time();
        $verification_start_time = $_SESSION['verification_start_time'];
        $expiry_time = 15 * 60; // 15 phút

        if (($current_time - $verification_start_time) > $expiry_time) {
            // Mã xác minh đã hết hạn
            $message = "Mã xác minh đã hết hạn! Vui lòng gửi lại mã xác minh.";
            $message_type = "error";
            // Reset các session liên quan
            unset($_SESSION['verification_code']);
            unset($_SESSION['verification_start_time']);
            unset($_SESSION['step']);
        } else {
            if ($input_code === $_SESSION['verification_code']) {
                $_SESSION['step'] = 3;
                $message = "Mã xác minh đúng! Vui lòng nhập mật khẩu mới.";
                $message_type = "success";
            } else {
                $message = "Mã xác minh không chính xác!";
                $message_type = "error";
            }
        }
    }

    // Bước 3: Cập nhật mật khẩu
    if (isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_SESSION['step']) && $_SESSION['step'] == 3) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Kiểm tra mật khẩu mới và xác nhận mật khẩu
        if ($new_password === $confirm_password) {
            if (strlen($new_password) < 6) {
                $message = "Mật khẩu mới phải có ít nhất 6 ký tự!";
                $message_type = "error";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $email = $_SESSION['email'];

                // Cập nhật mật khẩu trong bảng account
                $update_query = "UPDATE account SET password=? WHERE ma_khach_hang=(SELECT ma_khach_hang FROM khach_hang WHERE mail=?)";
                if ($stmt = $conn->prepare($update_query)) {
                    $stmt->bind_param("ss", $hashed_password, $email);
                    if ($stmt->execute()) {
                        $message = "Mật khẩu đã được thay đổi thành công!";
                        $message_type = "success";
                        unset($_SESSION['step']);
                        unset($_SESSION['verification_code']);
                        unset($_SESSION['verification_start_time']);
                        unset($_SESSION['email']);
                        header("Location: login.php");
                        exit();
                    } else {
                        $message = "Có lỗi khi cập nhật mật khẩu!";
                        $message_type = "error";
                    }
                    $stmt->close();
                } else {
                    $message = "Lỗi truy vấn cơ sở dữ liệu!";
                    $message_type = "error";
                }
            }
        } else {
            $message = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
            $message_type = "error";
        }
    }
}

// Xử lý yêu cầu gửi lại mã xác minh
if (isset($_GET['resend']) && $_GET['resend'] == 'true') {
    unset($_SESSION['step']);
    unset($_SESSION['verification_code']);
    unset($_SESSION['verification_start_time']);
    unset($_SESSION['email']);
    header("Location: forgot_password.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../Css/login.css">
    <link rel="stylesheet" href="../Css/forgot_password.css">
    <title>Quên mật khẩu</title>
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <?php
                // Hiển thị thông báo nếu có
                if (!empty($message)) {
                    echo '<div id="message" class="message ' . htmlspecialchars($message_type) . '">';
                    echo '<p>' . htmlspecialchars($message) . '</p>';
                    echo '</div>';
                }

                // Bước 1: Hiển thị form nhập email
                if (!isset($_SESSION['step']) || $_SESSION['step'] == 1) {
                    ?>
                    <form action="forgot_password.php" method="POST" class="sign-in-form">
                        <h2 class="title">Quên Mật Khẩu</h2>
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email của bạn" required />
                        </div>
                        <input type="submit" value="Gửi Mã Xác Minh" class="btn solid" />
                    </form>
                    <?php
                }
                // Bước 2: Hiển thị form nhập mã xác minh
                elseif ($_SESSION['step'] == 2) {
                    ?>
                    <form action="forgot_password.php" method="POST" class="sign-in-form">
                        <h2 class="title">Nhập Mã Xác Minh</h2>
                        <div class="input-field">
                            <i class="fas fa-key"></i>
                            <input type="text" name="code" placeholder="Mã xác minh" required />
                        </div>
                        <div class="countdown" id="countdown">Thời gian còn lại: 15:00</div>
                        <button type="button" class="btn solid resend-btn" id="resend-btn" onclick="resendCode()" style="display:none;"> Gửi lại mã xác minh</button>
                        <input type="submit" value="Xác nhận mã" class="btn solid" />
                    </form>
                    <?php
                }
                // Bước 3: Hiển thị form nhập mật khẩu mới
                elseif ($_SESSION['step'] == 3) {
                    ?>
                    <form action="forgot_password.php" method="POST" class="sign-in-form">
                        <h2 class="title">Cập nhật Mật Khẩu</h2>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="new_password" placeholder="Mật khẩu mới" required />
                        </div>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required />
                        </div>
                        <input type="submit" value="Cập nhật mật khẩu" class="btn solid" />
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Nếu cần, bạn có thể thêm logic JavaScript để cải thiện giao diện người dùng

        var countdownTime = 15 * 60; // 15 phút
        var countdownElement = document.getElementById('countdown');
        var resendButton = document.getElementById('resend-btn');
        var verificationCodeInput = document.querySelector('input[name="code"]'); // Trường nhập mã xác minh
        var submitButton = document.querySelector('input[type="submit"]'); // Nút xác nhận mã

        if (countdownElement) {
            // Hàm đếm ngược
            var countdownInterval = setInterval(function() {
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    countdownElement.innerText = 'Thời gian xác minh đã hết!';
                    resendButton.style.display = 'block'; // Hiển thị nút gửi lại mã
                    verificationCodeInput.disabled = true; // Vô hiệu hóa trường nhập mã xác minh
                    submitButton.style.display = 'none'; // Ẩn nút xác nhận mã
                    showResendMessage(); // Hiển thị thông báo yêu cầu gửi lại mã
                } else {
                    countdownElement.innerText = 'Thời gian còn lại: ' + formatTime(countdownTime);
                }
                countdownTime--;
            }, 1000);
        }

        // Hàm format thời gian
        function formatTime(seconds) {
            var minutes = Math.floor(seconds / 60);
            var seconds = seconds % 60;
            return minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
        }

        // Hàm hiển thị thông báo khi hết thời gian
        function showResendMessage() {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message', 'error');
            messageElement.innerHTML = '<p>Đã hết thời gian xác minh, vui lòng gửi lại mã xác minh mới.</p>';
            document.body.appendChild(messageElement);

            // Ẩn thông báo sau 5 giây (nếu cần)
            setTimeout(() => {
                messageElement.remove();
            }, 5000);
        }

        // Hàm gửi lại mã xác minh
        function resendCode() {
            window.location.href = 'forgot_password.php?resend=true';
        }

        // Hiển thị thông báo và tự động ẩn sau 3 giây
        <?php if (!empty($message)): ?>
            setTimeout(function() {
                var messageElement = document.getElementById('message');
                if (messageElement) {
                    messageElement.classList.add('hidden');
                }
            }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>
