<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/blog.css">
    <link rel="stylesheet" href="../Css/top-bar.css">
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <?php include '../Partials/header.php'; ?>

    <header class="sit-header">
        <div>
            <div class="center">
                <div class="image-container">
                    <img src="../Images/anh-khau-do-lon-mogi.jpg" alt="Image 1" class="main-image">
                </div>
            </div>
            <div class="Text-header">
                <li class="Text-text"><a href="">Blog</a></li>
                <li><a href="../index.php">Home </a> <a href=""> > </a> <a href="">Blog</a></li>
            </div>
        </div>


        <?php

        $servername = "localhost";
        $username = "root";  
        $password = "";
        $dbname = "furniture_store";  

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Kết nối thất bại: " . $conn->connect_error);
        }

        $sql = "SELECT id, title, date, description, author, image_path FROM blogs ORDER BY date DESC";
        $result = $conn->query($sql);
        ?>

        <h1 href="">BÀI VIẾT</h1>
        <div class="container">
            <?php

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $image_path =  $row['image_path'];
                    $formatted_date = date("d/m/Y H:i", strtotime($row['date'])); // Định dạng dd/mm/yyyy hh:mm

                    echo "
                <div class='card'>
                    <img src='{$image_path}' alt='Hình ảnh bài viết'>
                    <div class='title'>{$row['title']}</div>
                    
                    <div class='info'>
                        <div class='date'>
                            <i class='fas fa-clock'></i> {$formatted_date}
                        </div>
                        <div class='author'>
                            <i class='fas fa-user'></i> {$row['author']}
                        </div>
                    </div>
                    
                    <div class='description'>{$row['description']}</div>
                    <a href='../Pages/blog_detail.php?post_id={$row['id']}' class='read-more'>Read More</a>
                </div>
                ";
                }
            } else {
                // Nếu không có bài viết nào
                echo "<div class='no-posts'>Không có bài đăng nào</div>";
            }



            // Đóng kết nối
            $conn->close();
            ?>
        </div>
        <div class="pagination">
            <a href="#">&laquo;</a>
            <a href="#">1</a>
            <a href="#">2</a>
            <a href="#">.</a>
            <a href="#">.</a>
            <a href="#">.</a>
            <a href="#">10</a>
            <a href="#">&raquo;</a>
        </div>

    </header>

    <?php include '../Partials/footer.php'; ?>
    <script src="../Js/blog.js"></script>

</body>

</html>