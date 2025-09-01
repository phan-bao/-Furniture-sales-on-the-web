<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/sanpham.css">
    <title>Responsive Dashboard Design #1 | AsmrProg</title>
</head>


<body>

    <div class="container">
    <?php include('menu.php'); ?>
  


<!-- Main Content -->
<main>
        <h1>Danh Sách Sản Phẩm</h1>

        <!-- Search Bar and Create Product Button -->
        <div class="search-create-container">
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm..." onkeyup="searchProducts()">
                <span class="material-icons-sharp">search</span>
            </div>
            <!-- Toast Notification -->
<div id="toast" class="toast">
    <p>Sản phẩm đã được xóa thành công!</p>
</div>

            <a href="../admin/themsanpham.php" class="create-order-btn">Thêm Sản Phẩm</a>
        </div>

        <!-- Display products in a table -->
        <div class="products">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th> <!-- SKU sản phẩm -->
                        <th>Tên sản phẩm</th> <!-- Tên sản phẩm -->
                        <th>Giá</th> <!-- Giá sản phẩm -->
                        <th>Số lượng tồn kho</th> <!-- Tổng số lượng tồn kho từ tất cả phiên bản -->
                    </tr>
                </thead>
                <tbody>
                <?php
    // Kết nối đến MySQL
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

    // Truy vấn để lấy thông tin sản phẩm và số lượng tồn kho
    $sql = "SELECT san_pham.SKU_san_pham, san_pham.ten_san_pham, 
                   COALESCE(phien_ban_san_pham.gia, san_pham.gia) AS gia, 
                   COALESCE(SUM(phien_ban_san_pham.so_luong_ton_kho), 0) AS so_luong_ton_kho
            FROM san_pham
            LEFT JOIN phien_ban_san_pham ON san_pham.SKU_san_pham = phien_ban_san_pham.SKU_san_pham
            GROUP BY san_pham.SKU_san_pham
            ORDER BY san_pham.ten_san_pham ASC";

    $result = $conn->query($sql);

    // Hiển thị dữ liệu từ truy vấn
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['SKU_san_pham'] . "</td>"; // SKU sản phẩm
            echo "<td>" . $row['ten_san_pham'] . "</td>"; // Tên sản phẩm
            echo "<td>" . number_format($row['gia'], 0, ',', '.') . "</td>"; // Giá sản phẩm
            echo "<td>" . $row['so_luong_ton_kho'] . "</td>"; // Tổng số lượng tồn kho
            // Thêm nút xóa và nút sửa
            echo "<td>
                    <a href='../BE/update_product.php?sku=" . $row['SKU_san_pham'] . "' class='edit-btn'>
                        <span class='material-icons-sharp'>edit</span>
                    </a>
                    <a href='../BE/delete_product.php?sku=" . $row['SKU_san_pham'] . "' class='delete-btn'>
                        <span class='material-icons-sharp'>delete</span>
                    </a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Không có sản phẩm nào</td></tr>";
    }
    // Đóng kết nối
    $conn->close();
?>

                </tbody>
            </table>
        </div>
    </main>

    <script>
// Function to display the toast notification
// Function to display the toast notification
function showToast() {
    var toast = document.getElementById("toast");
    toast.classList.add("show");
    // Hide toast after 5 seconds
    setTimeout(function() {
        toast.classList.remove("show");
    }, 5000);
}


// Kiểm tra xem session đã set thông báo chưa
window.onload = function() {
    <?php
    session_start(); // Khởi tạo session để lấy biến từ PHP

    // Hiển thị thông báo nếu xóa thành công
    if (isset($_SESSION['delete_success']) && $_SESSION['delete_success']) {
        echo 'showToast();'; // Gọi hàm hiển thị thông báo
        unset($_SESSION['delete_success']); // Xóa session sau khi thông báo đã hiển thị
    }
    ?>
};



        function searchProducts() {
            let input = document.getElementById('search-input').value.toLowerCase();
            let table = document.querySelector(".products table tbody");
            let rows = table.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                let productNameCell = rows[i].getElementsByTagName('td')[1];
                if (productNameCell) {
                    let productName = productNameCell.textContent.toLowerCase();
                    rows[i].style.display = productName.indexOf(input) > -1 ? '' : 'none';
                }
            }
        }
    </script>

</body>
</html>