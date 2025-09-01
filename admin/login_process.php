<?php
// login_process.php

session_start();

// Kiểm tra nếu người dùng đã đăng nhập, nếu có thì chuyển hướng đến trang admin
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Kiểm tra nếu form đã được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Thông tin kết nối đến cơ sở dữ liệu
    $servername = "localhost";
    $username_db = "root";  // Tên đăng nhập MySQL
    $password_db = "";      // Mật khẩu MySQL (nếu dùng XAMPP thì thường là rỗng)
    $dbname = "furniture_store";  // Tên cơ sở dữ liệu

    // Tạo kết nối với MySQL
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        // Nếu kết nối không thành công, không tiết lộ thông tin lỗi chi tiết
        header("Location: login.php?error=1");
        exit;
    }

    // Truy vấn người dùng dựa trên tên đăng nhập
    $sql = "SELECT users.id, users.password, roles.role_name FROM users 
            JOIN roles ON users.role_id = roles.id 
            WHERE users.username = ?";

    // Sử dụng câu lệnh prepared statement để tránh SQL injection
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $username);  // "s" cho biết tham số là chuỗi
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra nếu tìm thấy người dùng
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];  // Mật khẩu đã lưu trong DB

            // Kiểm tra mật khẩu (so sánh trực tiếp mà không mã hóa)
            if ($password === $stored_password) {
                // Đăng nhập thành công, lưu thông tin vào session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $row['role_name'];

                // Chuyển hướng đến trang admin
                header("Location: dashboard.php");
                exit;
            } else {
                // Mật khẩu không chính xác
                header("Location: login.php?error=1");
                exit;
            }
        } else {
            // Người dùng không tồn tại
            header("Location: login.php?error=1");
            exit;
        }

        // Đóng statement để giải phóng tài nguyên
        $stmt->close();
    } else {
        // Lỗi trong quá trình chuẩn bị câu lệnh
        header("Location: login.php?error=1");
        exit;
    }

    // Đóng kết nối
    $conn->close();
} else {
    // Nếu truy cập trực tiếp vào file này mà không qua form
    header("Location: login.php");
    exit;
}
?>
