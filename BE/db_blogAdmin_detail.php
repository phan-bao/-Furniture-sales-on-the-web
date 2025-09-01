<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $old_image = '';
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        if (
            isset($_POST['title']) && isset($_POST['content']) && isset($_POST['status']) && isset($_POST['id']) && isset($_POST['author']) && isset($_POST['category'])
        ) {

            $id = $_POST['id'];
            $old_image = isset($_POST['old_image']) ? $_POST['old_image'] : '';
            $title = $_POST['title'];
            $content = $_POST['content'];
            $status = $_POST['status'];
            $author = $_POST['author'];
            $category = $_POST['category'];
            $image_caption = $_POST['image_caption'];

            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = $_FILES['image']['type'];

                if (!in_array($file_type, $allowed_types)) {
                    echo json_encode(["status" => "error", "message" => "Chỉ chấp nhận tệp ảnh JPG, PNG, GIF."]);
                    exit;
                }

                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    echo json_encode(["status" => "error", "message" => "Tệp ảnh quá lớn. Kích thước tối đa là 5MB."]);
                    exit;
                }

                $image_name = uniqid('img_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_path = "../images/blog/" . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                    echo json_encode(["status" => "error", "message" => "Lỗi khi tải tệp ảnh."]);
                    exit;
                }
            } else {
                $image_path = $old_image;
            }


            $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ?, status = ?, author = ?, category = ?, image_path = ?, description = ? WHERE id = ?");
            $stmt->bind_param("sssssssi", $title, $content, $status, $author, $category, $image_path, $image_caption, $id);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Bài viết đã được lưu thành công."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ. 1"]);
        }

        // Đóng kết nối
        $conn->close();
    }

    else {
        if (
            isset($_POST['title']) && isset($_POST['content']) && isset($_POST['status'])  && isset($_POST['author']) && isset($_POST['category'])
        ) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $status = $_POST['status'];
            $author = $_POST['author'];
            $category = $_POST['category'];
            $image_caption = $_POST['image_caption'];
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Kiểm tra loại tệp ảnh
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = $_FILES['image']['type'];

                if (!in_array($file_type, $allowed_types)) {
                    echo json_encode(["status" => "error", "message" => "Chỉ chấp nhận tệp ảnh JPG, PNG, GIF."]);
                    exit;
                }

                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    echo json_encode(["status" => "error", "message" => "Tệp ảnh quá lớn. Kích thước tối đa là 5MB."]);
                    exit;
                }

                $image_name = uniqid('img_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_path = "../images/blog/" . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                    echo json_encode(["status" => "error", "message" => "Lỗi khi tải tệp ảnh."]);
                    exit;
                }
            }

            $current_time = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("INSERT INTO blogs (title, content, status, author, category, image_path, date,description) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $title, $content, $status, $author, $category, $image_path, $current_time, $image_caption);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Bài viết đã được lưu thành công."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ.2"]);
        }

        $conn->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {

    $inputData = file_get_contents("php://input");

    parse_str($inputData, $parsedData);
}