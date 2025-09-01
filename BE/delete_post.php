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

// Lấy ID bài viết từ request
$postId = $_POST['postId']; // ID bài viết cần xóa

if (!empty($postId)) {
    // Chuẩn bị câu lệnh xóa
    $sql = "DELETE FROM blogs WHERE id = ?"; // Xóa bài viết theo ID

    if ($stmt = $conn->prepare($sql)) {
        // Liên kết tham số và thực thi câu lệnh
        $stmt->bind_param("i", $postId);
        if ($stmt->execute()) {
            // Nếu xóa thành công
            echo json_encode(['success' => true]);
        } else {
            // Lỗi khi xóa
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể chuẩn bị câu lệnh xóa']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID bài viết không hợp lệ']);
}

$conn->close();