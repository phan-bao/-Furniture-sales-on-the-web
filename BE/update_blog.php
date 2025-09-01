<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_caption = $_POST['image_caption'];
    $status = $_POST['status'];
    $schedule = $_POST['schedule'];
    $author = $_POST['author'];
    $category = $_POST['category'];


    $image_path = null;
    if (isset($_FILES["image"]) && $_FILES["image"]["tmp_name"]) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {

            $sql = "SELECT image_path FROM blogs WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (file_exists($row['image_path'])) {
                    unlink($row['image_path']);
                }
            }
        } else {
            echo "Lỗi tải ảnh lên.";
            exit;
        }
    } else {

        $sql = "SELECT image_path FROM blogs WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_path = $row['image_path'];
        }
    }


    $sql = "UPDATE blogs SET title = ?, content = ?, image_path = ?, image_caption = ?, status = ?, schedule = ?, author = ?, category = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $title, $content, $image_path, $image_caption, $status, $schedule, $author, $category, $id);

    if ($stmt->execute()) {
        echo "Cập nhật bài viết thành công!";
    } else {
        echo "Lỗi cập nhật bài viết: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();