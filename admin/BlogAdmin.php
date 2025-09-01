<?php
include '../BE/db_blogAdmin.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/blogAdmin.css">
    <title>Blog</title>
    <style>
    </style>
</head>

<body>
    <header>
        <?php include('menu.php'); ?>

        <div class="main-content">
            <h2>BÀI VIẾT</h2>

            <div class="search-container">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Tìm kiếm bài viết..."
                    title="Search for titles, dates, or categories">
                <button class="delete-all-btn" onclick="deleteAllPosts()">Xóa tất cả</button>
                <a href="../admin/blogAdmin_detail.php" class="create-order-btn">Tạo bài viết</a>
            </div>

            <table id="blogTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Image</th>
                        <th>Image Caption</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                        if ($result->num_rows > 0) {

                            while ($row = $result->fetch_assoc()) {
                                $description = $row['description'] ;
                                $content = $row['content'];
                                $title = $row['title'];

                                $description_words = explode(' ', $description);
                                $description_excerpt = implode(' ', array_slice($description_words, 0, 3)) . '...';

                                $content_words = explode(' ', $content);
                                $content_excerpt = implode(' ', array_slice($content_words, 0, 3)) . '...';

                                $title_words = explode(' ', $title);
                                $title_excerpt = implode(' ', array_slice($title_words, 0, 3)) . '...';
                        ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" data-post-id="<?php echo $row['id']; ?>">
                        </td>
                        <td><?php echo $title_excerpt; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><img src="../Bliss/<?php echo $row['image_path']; ?>" alt="Image" width="50"></td>
                        <td><?php echo $description_excerpt; ?></td>
                        <td><?php echo $content_excerpt; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['author']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <!-- Edit Button -->
                                <button class="edit-btn"
                                    onclick="window.location.href='blogAdmin_detail.php?id=<?php echo $row['id']; ?>'">
                                    <span class="material-icons-sharp">edit</span>
                                </button>

                                <!-- Delete Button -->
                                <button class="delete-btn" onclick="deletePost(this)">
                                    <span class="material-icons-sharp">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='11'>Không có bài viết nào</td></tr>";
                        }
                        ?>
                </tbody>
            </table>
        </div>
        </div>
    </header>
    <script src="../Js/blogAdmin.js"></script>
</body>

</html>