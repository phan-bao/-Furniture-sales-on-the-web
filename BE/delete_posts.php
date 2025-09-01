<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy mảng các ID bài viết từ yêu cầu POST
$postIds = json_decode($_POST['postIds']); // Mảng ID bài viết

if (!empty($postIds)) {
    // Chuẩn bị câu lệnh SQL để xóa bài viết theo ID
    $sql = "DELETE FROM blogs WHERE id IN (" . implode(',', array_map('intval', $postIds)) . ")";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]); // Trả về phản hồi thành công
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]); // Trả về thông báo lỗi
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Không có bài viết để xóa']); // Nếu không có bài viết
}

$conn->close();