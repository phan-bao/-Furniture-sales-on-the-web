<?php
// Kết nối đến MySQL 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy `post_id` từ URL
$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : 0;

// Truy vấn để lấy dữ liệu từ bảng blogs
$sql = "SELECT title, date, description, content, author, image_path FROM blogs WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Lỗi trong câu lệnh prepare: " . $conn->error);
}

$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu bài viết tồn tại
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Bài viết không tồn tại.";
    exit;
}

$stmt->close();
$conn->close();
?>