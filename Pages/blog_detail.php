<?php
include '../BE/blog_detail.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/top-bar.css">
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../Css/blog_detail.css">
</head>

<body>
    <?php include '../Partials/header.php'; ?>

    <!-- Main Blog Container -->
    <div class="container">
        <header class="detail_all">
            <div class="blog-detail">
                <h1><?php echo htmlspecialchars($row['title']); ?></h1>
                <p class="date">Ngày đăng: <?php echo date("d/m/Y H:i:s", strtotime($row['date'])); ?></p>
                <p class="author">Tác giả: <?php echo htmlspecialchars($row['author']); ?></p>

                <div class="text">
                    <img src="<?php echo  htmlspecialchars($row['image_path']); ?>" alt="Blog Image">
                    <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>

                    <div class="content">
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    </div>

                    <!-- Phần Gợi ý bài viết liên quan -->
                    <h3>Gợi ý bài viết liên quan:</h3>
                    <div class="related-products">
                        <div class="related-item">
                            <img src="../Images/phongkhach3.jpg" alt="Sofa">
                            <a href="#">SOFA NHỮNG GÓC NHÌN MỚI VỀ MỘT BỘ SOFA ĐẸP</a>
                        </div>
                        <div class="related-item">
                            <img src="../Images/phongkhach5.jpg" alt="Table">
                            <a href="#">GIỚI THIỆU NHỮNG MẪU THẾT KẾ SOFA MỚI</a>
                        </div>
                        <div class="related-item">
                            <img src="../Images/phòng khach.jpg" alt="Lamp">
                            <a href="#">10 Ý TƯỞNG THIẾT KẾ SOFA CỦA BLISS</a>
                        </div>
                    </div>

                    <!-- Phần thẻ Tags -->
                    <h3>Thiết kế</h3>
                    <div class="tags">
                        <a href="#">Nội thất</a>
                        <a href="#">Trang trí</a>
                        <a href="#">Sofa</a>
                        <a href="#">Phong cách</a>
                    </div>

                    <!-- Phần bình luận -->
                    <h3>Bình luận</h3>
                    <form action="#" method="post" class="comment-form">
                        <textarea name="comment" placeholder="Để lại bình luận..." required></textarea>
                        <button type="submit">Gửi bình luận</button>
                    </form>

                    <!-- Phần chia sẻ bài viết -->
                    <div class="share-buttons">
                        <p>Chia sẻ bài viết:</p>
                        <a href="#" class="share-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="share-btn"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="share-btn"><i class="fab fa-instagram"></i></a>
                    </div>

                </div>
            </div>
        </header>
    </div>

    <?php include '../Partials/footer.php'; ?>
</body>

</html>