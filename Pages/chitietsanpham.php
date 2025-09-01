<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/chitietsanpham.css">
</head>
<body>

<?php include '../Partials/header.php'; ?>
<div class="breadcrumb">
    <span class="breadcrumb-home">Trang Chủ</span>
    <span class="breadcrumb-separator">›</span>
    <a href="sanpham.php" class="breadcrumb-link">Sản Phẩm</a>
</div>

<div class="product-detail-container">
<?php

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "furniture_store");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy SKU từ URL
$sku = $_GET['sku'];
// Truy vấn mô tả sản phẩm
$description_sql = "SELECT mo_ta FROM mo_ta_san_pham WHERE SKU_san_pham = '$sku'";
$description_result = $conn->query($description_sql);
$description = $description_result->num_rows > 0 ? $description_result->fetch_assoc()['mo_ta'] : "Chưa có thông tin mô tả.";

// Truy vấn điều khoản bảo hành
$warranty_sql = "SELECT dieu_khoan, thoi_gian_bao_hanh FROM dieu_khoan_bao_hanh LIMIT 1";
$warranty_result = $conn->query($warranty_sql);
$warranty = $warranty_result->num_rows > 0 ? $warranty_result->fetch_assoc() : null;

// Truy vấn đánh giá sản phẩm
$reviews_sql = "SELECT kh.ten_khach_hang, dg.danh_gia, dg.binh_luan, dg.hinh_anh, dg.ngay_danh_gia 
                FROM danh_gia_san_pham dg 
                JOIN khach_hang kh ON dg.ma_khach_hang = kh.ma_khach_hang 
                WHERE dg.SKU_san_pham = '$sku'";
$reviews_result = $conn->query($reviews_sql);
$reviews = [];
if ($reviews_result->num_rows > 0) {
    while ($review = $reviews_result->fetch_assoc()) {
        $reviews[] = $review;
    }
}

// Truy vấn sản phẩm dựa trên SKU và thông tin khuyến mãi
$sql = "
    SELECT sp.*, mk.ti_le_phan_tram AS phan_tram_giam_gia, mk.gia_tri_so_tien AS so_tien_giam, 
           ctkm.ti_le_phan_tram AS phan_tram_ctkm, ctkm.gia_tri_so_tien AS so_tien_ctkm
    FROM san_pham sp
    LEFT JOIN ma_khuyen_mai mk ON sp.ten_km = mk.ten_km
    LEFT JOIN chuong_trinh_khuyen_mai ctkm ON sp.ten_ctkm = ctkm.ten_ctkm
    WHERE sp.SKU_san_pham = '$sku'
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Truy vấn các phiên bản sản phẩm
    $version_sql = "SELECT * FROM phien_ban_san_pham WHERE SKU_san_pham = '$sku'";
    $version_result = $conn->query($version_sql);

    $versions = [];
    if ($version_result->num_rows > 0) {
        while ($version_row = $version_result->fetch_assoc()) {
            $versions[] = $version_row;
        }
    }

    // Lấy thông tin giảm giá
    $giam_gia = 0;
    if (!is_null($row['phan_tram_giam_gia'])) {
        $giam_gia += $row['phan_tram_giam_gia'] / 100;
    }
    if (!is_null($row['so_tien_giam'])) {
        $giam_gia += $row['so_tien_giam'];
    }
    if (!is_null($row['phan_tram_ctkm'])) {
        $giam_gia += $row['phan_tram_ctkm'] / 100;
    }
    if (!is_null($row['so_tien_ctkm'])) {
        $giam_gia += $row['so_tien_ctkm'];
    }

    // Hiển thị sản phẩm
    echo "<div class='product-image'>";
    echo '<img src="../images/' . $row['anh'] . '" alt="' . $row['ten_san_pham'] . '">';
    echo "</div>";

    echo "<div class='product-info'>";
    echo "<h1>" . $row['ten_san_pham'] . "</h1>";

    // Thêm phần lượt mua và lượt xem như mẫu
    echo "<div class='product-purchase-info'>";
    echo "<div class='rating'>";
    echo "<span>⭐⭐⭐⭐⭐</span>";
    echo "<a href='#write-review'>Viết đánh giá của bạn (0)</a>";
    echo "</div>";

    echo "<div class='purchase-view-info'>";
    echo "<div class='purchase-count'>";
    echo "<span class='icon-tag'>🏷️</span>";
    echo "Có " . rand(0, 100) . " lượt mua sản phẩm";
    echo "</div>";
    echo "<div class='view-count'>";
    echo "<span class='icon-view'>👁️</span>";
    echo "Có " . rand(0, 100) . " lượt xem sản phẩm";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    // Phần giá sản phẩm
    echo "<div class='product-detail-price'>";
    echo "<span id='original-price' class='original-price'>" . number_format($versions[0]['gia'], 0, ',', '.') . " VNĐ</span>";
    echo "<span id='discount-price' class='discount-price'>" . number_format($versions[0]['gia'] * (1 - $giam_gia), 0, ',', '.') . " VNĐ</span>";
    echo "</div>";

    // Buttons chọn màu sắc
    echo "<div class='product-options'>";
    echo "<label>Chọn màu sắc:</label>";
    echo "<div class='options-container'>";
    $mau_sac_options = array_unique(array_column($versions, 'mau_sac'));
    foreach ($mau_sac_options as $mau_sac) {
        $activeClass = ($mau_sac == $versions[0]['mau_sac']) ? 'active' : '';
        echo "<button class='option-button $activeClass' data-type='color' data-value='$mau_sac' onclick='selectOption(this)'>";
        echo $mau_sac;
        echo "</button>";
    }
    echo "</div>";
    echo "</div>";

    // Buttons chọn vật liệu
    echo "<div class='product-options'>";
    echo "<label>Chọn vật liệu:</label>";
    echo "<div class='options-container'>";
    $vat_lieu_options = array_unique(array_column($versions, 'vat_lieu'));
    foreach ($vat_lieu_options as $vat_lieu) {
        $activeClass = ($vat_lieu == $versions[0]['vat_lieu']) ? 'active' : '';
        echo "<button class='option-button $activeClass' data-type='material' data-value='$vat_lieu' onclick='selectOption(this)'>";
        echo $vat_lieu;
        echo "</button>";
    }
    echo "</div>";
    echo "</div>";

    // Buttons chọn kích thước
    echo "<div class='product-options'>";
    echo "<label>Chọn kích thước:</label>";
    echo "<div class='options-container'>";
    $kich_thuoc_options = array_unique(array_column($versions, 'kich_thuoc'));
    foreach ($kich_thuoc_options as $kich_thuoc) {
        $activeClass = ($kich_thuoc == $versions[0]['kich_thuoc']) ? 'active' : '';
        echo "<button class='option-button $activeClass' data-type='size' data-value='$kich_thuoc' onclick='selectOption(this)'>";
        echo $kich_thuoc;
        echo "</button>";
    }
    echo "</div>";
    echo "</div>";

    // Thêm số lượng sản phẩm và nút "Mua Ngay"
    echo "<div class='product-quantity-and-actions'>";
    echo "<div class='product-quantity'>";
    echo "<button type='button' class='quantity-btn' onclick='decreaseQuantity()'>-</button>";
    echo "<input type='number' id='quantity' name='quantity' value='1' min='1' class='quantity-input'>";
    echo "<button type='button' class='quantity-btn' onclick='increaseQuantity()'>+</button>";
    echo "</div>";



    echo "</div>"; // Đóng div .product-info

    // Tách biệt phần chi tiết sản phẩm ở dưới phần thông tin chính và hình ảnh
    echo "<div class='product-details-container'>";
    echo "<div class='product-details-tabs'>";

// Tabs tiêu đề
echo "<ul class='tabs'>";
echo "<li class='tab-item active' onclick='showTabContent(\"details\")'>Chi Tiết Sản Phẩm</li>";
echo "<li class='tab-item' onclick='showTabContent(\"warranty\")'>Điều Khoản Bảo Hành</li>";
echo "</ul>";

// Nội dung Tab: Mô Tả Sản Phẩm (hiển thị mặc định)
echo "<div class='tab-content' id='details' style='display: block;'>";
echo "<h3>Mô Tả Sản Phẩm</h3>";
echo "<p>$description</p>";
echo "</div>";

// Nội dung Tab: Chính Sách Bảo Hành (ẩn ban đầu)
echo "<div class='tab-content' id='warranty' style='display: none;'>";
if ($warranty) {
    echo "<h3>Điều Khoản Bảo Hành</h3>";
    echo "<p>" . $warranty['dieu_khoan'] . "</p>";
    echo "<p>Thời gian bảo hành: " . $warranty['thoi_gian_bao_hanh'] . " tháng</p>";
} else {
    echo "<p>Chưa có thông tin bảo hành.</p>";
}
echo "</div>";


    // Truyền dữ liệu phiên bản và giảm giá sang JavaScript
    echo "<script>var versions = " . json_encode($versions) . "; var discountRate = $giam_gia;</script>";
} else {
    echo "<p>Sản phẩm không tồn tại.</p>";
}
// SP Liên Quan
// Phần sản phẩm liên quan
echo "<div class='related-products'>";
echo "<h2>Sản Phẩm Liên Quan</h2>";

// Truy vấn sản phẩm liên quan dựa trên Tag hoặc danh mục
$related_sql = "
    SELECT SKU_san_pham, ten_san_pham, gia, anh
    FROM san_pham
    WHERE SKU_san_pham != '$sku' -- Loại trừ sản phẩm hiện tại
      AND tag = (SELECT tag FROM san_pham WHERE SKU_san_pham = '$sku') -- Cùng Tag
    LIMIT 4 -- Giới hạn 4 sản phẩm
";
$related_result = $conn->query($related_sql);

if ($related_result->num_rows > 0) {
    echo "<div class='related-products-container'>";
    while ($related_row = $related_result->fetch_assoc()) {
        echo "<div class='related-product-item'>";
        echo "<a href='chitietsanpham.php?sku=" . $related_row['SKU_san_pham'] . "'>";
        echo '<img src="../images/' . $related_row['anh'] . '" alt="' . $related_row['ten_san_pham'] . '">';
        echo "<h4>" . $related_row['ten_san_pham'] . "</h4>";
        echo "<p>" . number_format($related_row['gia'], 0, ',', '.') . " VNĐ</p>";
        echo "</a>";
        echo "</div>";
    }
    echo "</div>"; // Đóng related-products-container
} else {
    echo "<p>Không có sản phẩm liên quan.</p>";
}

echo "</div>"; // Đóng related-products

$conn->close();
?>
<div class='product-quantity-and-actions'>
    <div class='product-quantity'>
        <!-- Nút giảm số lượng -->
        <button type='button' class='quantity-btn' onclick='decreaseQuantity()'>-</button>
        
        <!-- Input số lượng -->
        <input type='number' id='quantity' name='quantity' value='1' min='1' class='quantity-input'>
        
        <!-- Nút tăng số lượng -->
        <button type='button' class='quantity-btn' onclick='increaseQuantity()'>+</button>
    </div>

    <!-- Nút "Mua Ngay" sẽ gửi GET request với SKU và số lượng -->
    <button class='buy-now-button' onclick="window.location.href='mua-hang.php?sku=<?php echo $sku; ?>&quantity=' + document.getElementById('quantity').value">
        Mua Ngay
    </button>

    <!-- Nút "Thêm vào giỏ hàng" gọi hàm JavaScript để xử lý -->
    <button class='add-to-cart-btn' onclick='addToCart()'>
        Thêm vào giỏ hàng
    </button>
</div>

</div>

<script>
    let selectedSKUPhienBan = null; // Khai báo biến tại đây

    let selectedOptions = { color: null, material: null, size: null };

    function selectOption(button) {
        const type = button.getAttribute('data-type');
        const value = button.getAttribute('data-value');

        // Cập nhật giao diện nút active
        const buttons = document.querySelectorAll(`.option-button[data-type='${type}']`);
        buttons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        // Lưu lựa chọn của người dùng
        selectedOptions[type] = value;

        // Lọc phiên bản phù hợp với các lựa chọn
        const selectedVersion = versions.find(version =>
            (selectedOptions.color === null || version.mau_sac === selectedOptions.color) &&
            (selectedOptions.material === null || version.vat_lieu === selectedOptions.material) &&
            (selectedOptions.size === null || version.kich_thuoc === selectedOptions.size)
        );

        if (selectedVersion) {
            // Lưu sku_phien_ban
            selectedSKUPhienBan = selectedVersion.SKU_phien_ban;

            // Cập nhật giá gốc và giá giảm
            const originalPrice = selectedVersion.gia;
            const discountPrice = originalPrice * (1 - discountRate);

            document.getElementById('original-price').innerText = new Intl.NumberFormat().format(originalPrice) + " VNĐ";
            document.getElementById('discount-price').innerText = new Intl.NumberFormat().format(discountPrice) + " VNĐ";
        } else {
            selectedSKUPhienBan = null;
            // Nếu không tìm thấy phiên bản phù hợp, reset giá
            document.getElementById('original-price').innerText = "N/A";
            document.getElementById('discount-price').innerText = "N/A";
        }
    }

    function initializeSelectedOptions() {
        const optionTypes = ['color', 'material', 'size'];
        optionTypes.forEach(type => {
            const activeButton = document.querySelector(`.option-button[data-type='${type}'].active`);
            if (activeButton) {
                selectedOptions[type] = activeButton.getAttribute('data-value');
            }
        });
    }

    function increaseQuantity() {
        let quantityInput = document.getElementById('quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
    }

    function decreaseQuantity() {
        let quantityInput = document.getElementById('quantity');
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    }

    function showTabContent(tabId) {
        // Ẩn tất cả nội dung tab
        const tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(tab => {
            tab.style.display = tab.id === tabId ? 'block' : 'none';
        });

        // Xóa trạng thái active của tất cả tab tiêu đề
        const tabItems = document.querySelectorAll('.tab-item');
        tabItems.forEach(tab => tab.classList.remove('active'));

        // Đặt tab được chọn làm active
        const activeTab = document.querySelector(`.tab-item[onclick="showTabContent('${tabId}')"]`);
        if (activeTab) {
            activeTab.classList.add('active');
        }
    }

    // Đảm bảo tab "Chi Tiết Sản Phẩm" hiển thị mặc định khi tải trang
    document.addEventListener('DOMContentLoaded', () => {
        initializeSelectedOptions();
        showTabContent('details');
    });
    function addToCart() {
    var quantity = document.getElementById('quantity').value;  // Lấy số lượng từ input

    // Kiểm tra tất cả các tùy chọn đã được chọn và sku_phien_ban đã được xác định
    if (!selectedOptions.color || !selectedOptions.material || !selectedOptions.size || !selectedSKUPhienBan) {
        alert("Vui lòng chọn đầy đủ các tùy chọn sản phẩm (màu sắc, vật liệu, kích thước).");
        return;
    }

    // Kiểm tra số lượng có hợp lệ không (số nguyên và lớn hơn 0)
    if (isNaN(quantity) || quantity <= 0) {
        alert("Số lượng không hợp lệ. Vui lòng nhập một số lượng lớn hơn 0.");
        return;
    }

    console.log("Selected Options:", selectedOptions);
    console.log("Selected SKU Phien Ban:", selectedSKUPhienBan);

    var data = {
        sku_phien_ban: selectedSKUPhienBan, // Gửi sku_phien_ban thay vì sku
        quantity: quantity
    };

    // Gửi yêu cầu đến server với Fetch API
    fetch('../Pages/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Có lỗi xảy ra khi gửi yêu cầu.");
        }
        return response.text();  // Lấy phản hồi dưới dạng văn bản (text)
    })
    .then(data => {
        console.log("Response:", data);  // Kiểm tra phản hồi thực tế

        // Xử lý phản hồi dưới dạng văn bản
        alert(data);  // Hiển thị thông báo trả về từ server
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Có lỗi xảy ra. Vui lòng thử lại.");
    });
}


// Hàm này dùng để cập nhật giao diện giỏ hàng mà không cần tải lại trang
function updateCartUI() {
    // Ví dụ về cách bạn có thể làm mới giỏ hàng trong giao diện (tùy thuộc vào cách bạn triển khai giao diện giỏ hàng)
    fetch('../Pages/cart_details.php', { method: 'GET' })
        .then(response => response.json())
        .then(cartData => {
            // Cập nhật giỏ hàng (thí dụ: hiển thị tổng số sản phẩm trong giỏ hàng)
            document.getElementById('cart-count').innerText = cartData.totalItems;
            // Hiển thị danh sách các sản phẩm trong giỏ hàng
            let cartItemsHTML = '';
            cartData.items.forEach(item => {
                cartItemsHTML += `<div class="cart-item">${item.name} - ${item.quantity}</div>`;
            });
            document.getElementById('cart-items').innerHTML = cartItemsHTML;
        })
        .catch(error => {
            console.error('Error fetching cart data:', error);
        });
}

</script>


</body>
</html>
