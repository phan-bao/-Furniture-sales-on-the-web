<?php

session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['ma_khach_hang'])) {
    header("Location: login.php"); // Chuyển hướng tới trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Lấy ma_khach_hang từ session
$ma_khach_hang = $_SESSION['ma_khach_hang'];

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "furniture_store");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Truy vấn giỏ hàng của người dùng với JOIN bảng san_pham
$stmt = $conn->prepare("
    SELECT 
        g.id_gio_hang, 
        g.SKU_phien_ban, 
        g.quantity, 
        pb.gia, 
        pb.hinh_anh, 
        sp.ten_san_pham, 
        pb.mau_sac, 
        pb.vat_lieu, 
        pb.kich_thuoc
    FROM 
        giohang g
    JOIN 
        phien_ban_san_pham pb ON g.SKU_phien_ban = pb.SKU_phien_ban
    JOIN 
        san_pham sp ON pb.SKU_san_pham = sp.SKU_san_pham
    WHERE 
        g.user_id = (SELECT user_id FROM account WHERE ma_khach_hang = ?)
");

$stmt->bind_param("i", $ma_khach_hang);  // Binding ma_khach_hang vào câu truy vấn
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$cart_items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Tính tổng tiền của từng sản phẩm trong giỏ hàng
        $row['total_price'] = $row['gia'] * $row['quantity'];
        $total += $row['total_price'];
        $cart_items[] = $row;
    }
} else 
echo "";


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/giohang.css">
    <link rel="stylesheet" href="../Css/footer.css">
</head>
<body>
    <?php include '../Partials/header.php'; ?>
    
    

    <div class="cart-container">
        <h2>Giỏ hàng của bạn</h2>
        
        <?php if (!empty($cart_items)): ?>
            <table class="cart-table">
    <thead>
        <tr>
            <th>Ảnh Sản Phẩm</th>
            <th>Tên Sản Phẩm</th>
            <th>Màu Sắc</th>
            <th>Vật Liệu</th>
            <th>Kích Thước</th>
            <th>Số Lượng</th>
            <th>Giá</th>
            <th>Tổng Tiền</th>
            <th>Thao Tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cart_items as $item): ?>
            <tr>
                <td>
                    <!-- Kiểm tra ảnh sản phẩm -->
                    <?php if (!empty($item['hinh_anh']) && file_exists('../images/' . $item['hinh_anh'])): ?>
                        <img src="../images/<?php echo htmlspecialchars($item['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($item['ten_san_pham']); ?>" class="cart-product-image">
                    <?php else: ?>
                        <img src="../images/default.png" alt="Ảnh sản phẩm không có" class="cart-product-image">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($item['ten_san_pham']); ?></td>
                <td><?php echo htmlspecialchars($item['mau_sac'] ?? 'Chưa có'); ?></td>
                <td><?php echo htmlspecialchars($item['vat_lieu'] ?? 'Chưa có'); ?></td>
                <td><?php echo htmlspecialchars($item['kich_thuoc'] ?? 'Chưa có'); ?></td>
                <td>
                    <input type="number" value="<?php echo $item['quantity']; ?>" id="quantity_<?php echo $item['id_gio_hang']; ?>" min="1" class="cart-quantity">
                </td>
                <td><?php echo number_format($item['gia'], 0, ',', '.') . ' VNĐ'; ?></td>
                <td><?php echo number_format($item['total_price'], 0, ',', '.') . ' VNĐ'; ?></td>
                <td><button onclick="removeFromCart(<?php echo $item['id_gio_hang']; ?>, this.closest('tr'))">Xóa</button></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

            <div class="cart-total">
                <h3>Tổng Tiền: <?php echo number_format($total, 0, ',', '.') . ' VNĐ'; ?></h3>
                <button onclick="checkout()">Thanh Toán</button>
            </div>
        <?php else: ?>
            <p>Giỏ hàng trống của khách hàng đang trống.</p>
        <?php endif; ?>
    </div>

    <script>
function removeFromCart(id_gio_hang, rowElement) {
    var form = new FormData();
    form.append("id_gio_hang", id_gio_hang);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../Pages/remove_from_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        try {
            var response = JSON.parse(xhr.responseText);  // Parse JSON trả về từ server

            if (xhr.status === 200) {
                if (response.status === "success") {
                    // Xóa dòng sản phẩm khỏi giao diện
                    rowElement.remove();

                    // Tải lại trang sau khi xóa
                    location.reload();  // Tự động reload trang để cập nhật giỏ hàng mới

                    alert(response.message);  // Hiển thị thông báo thành công
                } else {
                    alert("Có lỗi khi xóa sản phẩm: " + response.message);  // Thông báo lỗi
                }
            } else {
                alert("Có lỗi khi xóa sản phẩm.");
            }
        } catch (e) {
            console.error("Lỗi phân tích JSON:", e);
            alert("Có lỗi khi nhận dữ liệu từ server.");
        }
    };

    xhr.onerror = function () {
        console.error("Có lỗi khi gửi yêu cầu.");
        alert("Có lỗi khi gửi yêu cầu tới server.");
    };

    xhr.send("id_gio_hang=" + id_gio_hang);
}


function updateCartTotal() {
    let total = 0;
    // Duyệt qua các sản phẩm trong giỏ hàng để tính tổng
    document.querySelectorAll('.cart-item').forEach(function(row) {
        let price = parseFloat(row.querySelector('.total-price').innerText.replace(' VNĐ', '').replace(',', ''));
        total += price;
    });
    // Cập nhật tổng tiền giỏ hàng
    document.querySelector('.cart-total').innerText = 'Tổng Tiền: ' + total.toLocaleString('vi-VN') + ' VNĐ';
}



        function checkout() {
            window.location.href = '../Pages/thanhtoan.php'; // Đảm bảo rằng bạn đã tạo trang checkout.php
        }
    </script>
</body>
<?php include '../Partials/footer.php'; ?>
</html>
