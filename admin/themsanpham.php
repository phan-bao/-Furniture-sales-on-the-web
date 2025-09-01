<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/themsanpham.css">
    <title>Responsive Dashboard Design #1 | AsmrProg</title>
</head>

<body>
    <div class="container">
        <!-- Sidebar Section -->
        <?php include('menu.php'); ?>
        <div class="form-container">
    <h2>Thêm Sản Phẩm Mới</h2>
    <form id="add-product-form" action="../BE/process_product.php" method="POST" enctype="multipart/form-data">
        <!-- Các trường nhập liệu sản phẩm -->
        <input type="text" name="sku" placeholder="Mã SKU" required>
        <input type="text" name="ten_san_pham" placeholder="Tên Sản Phẩm" required>
        <textarea name="mo_ta" placeholder="Mô Tả"></textarea>
        <textarea name="noi_dung" placeholder="Nội Dung"></textarea>
        <input type="file" name="anh" required />
        <input type="text" name="tag" placeholder="Tag (Chuyên Mục)">
        
        <!-- Thêm các trường cho phiên bản sản phẩm -->
        <div id="versions-container">
            <div class="version">
                <h3>Phiên bản 1</h3>
                <input type="text" name="mau_sac[]" placeholder="Màu Sắc" required>
                <input type="text" name="kich_thuoc[]" placeholder="Kích Thước" required>
                <input type="text" name="vat_lieu[]" placeholder="Vật Liệu" required> 
                <input type="number" name="gia_version[]" placeholder="Giá" step="0.01" required>
            </div>
        </div>
        
        <button type="button" id="add-version-btn">Thêm Phiên Bản</button>
        <button type="submit">Thêm Sản Phẩm</button>
    </form>
    <div id="notification" class="notification"></div>
</div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
    // Xử lý thêm phiên bản
    $('#add-version-btn').on('click', function() {
        var versionCount = $('#versions-container .version').length + 1; // Đếm số phiên bản hiện tại
        var newVersionHTML = `
            <div class="version">
                <h3>Phiên bản ${versionCount}</h3>
                <input type="text" name="mau_sac[]" placeholder="Màu Sắc" required>
                <input type="text" name="kich_thuoc[]" placeholder="Kích Thước" required>
                <input type="number" name="gia_version[]" placeholder="Giá" step="0.01" required>
                <input type="text" name="vat_lieu[]" placeholder="Vật Liệu" required> <!-- Thêm trường Vật Liệu -->
                <button type="button" class="remove-version-btn">Xóa Phiên Bản</button>

            </div>
        `;
        $('#versions-container').append(newVersionHTML); // Thêm phiên bản mới vào container
        $(document).on('click', '.remove-version-btn', function() {
    $(this).closest('.version').remove(); // Xóa phiên bản khi nhấn nút "Xóa Phiên Bản"
});

    });
});

    </script>
</body>
</html>