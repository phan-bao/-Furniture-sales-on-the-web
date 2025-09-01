<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Css/taodonhang.css">
    <title>Tạo Đơn Hàng</title>
</head>

<body>

<div class="container">
    
    <!-- Sidebar Section -->
    <aside>
        <div class="toggle">
            <div class="logo">
                <img src="../Images/logo1.png">
                <h2>B<span class="danger">LISS</span></h2>
            </div>
            <div class="close" id="close-btn">
                <span class="material-icons-sharp">close</span>
            </div>
        </div>

        <div class="sidebar">
            <a href="../admin/dashboard.php">
                <span class="material-icons-sharp">insights</span>
                <h3>Tổng Quan</h3>
            </a>
            <a href="../admin/donhang.php" class="active"> 
                <span class="material-icons-sharp">dashboard</span>
                <h3>Đơn Hàng</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">person_outline</span>
                <h3>Users</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">receipt_long</span>
                <h3>History</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">mail_outline</span>
                <h3>Tickets</h3>
                <span class="message-count">27</span>
            </a>
            <a href="#">
                <span class="material-icons-sharp">inventory</span>
                <h3>Sale List</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">report_gmailerrorred</span>
                <h3>Reports</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">settings</span>
                <h3>Settings</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">add</span>
                <h3>New Login</h3>
            </a>
            <a href="#">
                <span class="material-icons-sharp">logout</span>
                <h3>Logout</h3>
            </a>
        </div>
    </aside>
    <!-- End of Sidebar Section -->

    <!-- Main Content -->
    <main>
        <h1>Tạo Đơn Hàng</h1>

        <!-- Form chứa chi tiết đơn hàng -->
        <div class="order-form">
            <div id="selected-products">
                <!-- Danh sách sản phẩm sẽ hiển thị tại đây -->
            </div>

            <!-- Thanh tìm kiếm sản phẩm -->
            <div class="search-container">
                <input type="text" id="search-bar" placeholder="Tìm kiếm sản phẩm...">
                <div class="product-list" id="product-list">
                    <!-- Danh sách sản phẩm sẽ được hiển thị ở đây -->
                    <?php
                    // Kết nối đến cơ sở dữ liệu
                    $conn = new mysqli("localhost", "root", "", "furniture_store");

                    // Kiểm tra kết nối
                    if ($conn->connect_error) {
                        die("Kết nối thất bại: " . $conn->connect_error);
                    }

                    // Truy vấn danh sách sản phẩm
                    $sql = "SELECT SKU_san_pham, ten_san_pham, gia FROM san_pham";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Hiển thị danh sách sản phẩm theo dạng hàng ngang
                        while($row = $result->fetch_assoc()) {
                            echo "<div class='product-item' onclick='selectProduct(\"" . $row["SKU_san_pham"] . "\", \"" . $row["ten_san_pham"] . "\", \"" . $row["gia"] . "\")'>";
                            echo "Mã: " . $row["SKU_san_pham"] . " - " . $row["ten_san_pham"] . " - Giá: " . number_format($row["gia"], 0, ',', '.') . " VND";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='product-item'>Không có sản phẩm nào</div>";
                    }

                    $conn->close();
                    ?>
                </div>
            </div>

            <!-- Ghi chú và tổng đơn hàng -->
            <!-- Ghi chú và tổng đơn hàng -->
<div class="order-summary">
    <textarea placeholder="Thêm ghi chú..."></textarea>
    <div class="order-total">
        <h3>Tạm tính: <span id="subtotal">0đ</span></h3>
        <h3>Phí vận chuyển: <span id="shipping">0đ</span></h3>
        <h3>Giảm giá: <span id="discount-value">0đ</span></h3> <!-- Hiển thị giảm giá -->
        <h2>Tổng cộng: <span id="total">0đ</span></h2>
    </div>
</div>


            <!-- Nút thanh toán -->
            <div class="payment-buttons">
                <button class="btn primary">Đã thanh toán</button>
                <button class="btn secondary">Thanh toán sau</button>
            </div>
        </div>


        <!-- Thêm Khuyến Mãi -->
        <div class="discount-container">
    <a href="#" id="add-discount" class="text-link">Thêm khuyến mãi</a>
    <div class="discount-popup" id="discount-popup">
        <h3>Nhập giá trị giảm giá cho đơn hàng</h3>
        <div class="discount-inputs">
            <input type="text" id="discount-amount" placeholder="₫">
            <input type="text" id="discount-percent" placeholder="%">
        </div>
        <div class="discount-reason">
            <label for="discount-reason">Lý do</label>
            <input type="text" id="discount-reason" placeholder="Nhập lý do giảm giá cho đơn hàng">
        </div>
        <div class="discount-code">
            <label for="discount-code">Hoặc sử dụng mã khuyến mãi</label>
            <input type="text" id="discount-code" placeholder="Mã khuyến mãi">
        </div>
        <div class="discount-actions">
            <button id="apply-discount" class="btn primary">Áp dụng</button>
            <button id="close-popup" class="btn secondary">Đóng</button>
        </div>
    </div>
</div>
<!-- Thêm Phương Thức Vận Chuyển -->

<a href="#" id="add-shipping-btn" class="text-link">Thêm Phương Thức Vận Chuyển</a>
<!-- Modal for adding shipping method -->
<div id="shipping-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Thêm phương thức vận chuyển</h2>
        <form id="shipping-form">
            <div>
                <input type="radio" id="free-shipping" name="shipping" value="free">
                <label for="free-shipping">Miễn phí vận chuyển</label><br>
                <input type="radio" id="custom-shipping" name="shipping" value="custom" checked>
                <label for="custom-shipping">Vận chuyển tùy chọn</label>
            </div>
            <div id="custom-shipping-options">
                <input type="text" placeholder="Tên phương thức vận chuyển" id="shipping-name">
                <input type="text" placeholder="Phí vận chuyển" id="shipping-cost">
            </div>
            <div>
                <button type="submit">Áp dụng</button>
                <button type="button" id="shipping-close-btn" class="btn secondary">Đóng</button> <!-- Nút Đóng/Xóa -->
            </div>
        </form>
        <a href="#">Xem phí dự kiến của các đối tác vận chuyển</a>
    </div>
</div>

<script>
// Xử lý modal thêm phương thức vận chuyển
document.addEventListener("DOMContentLoaded", function() {
    var shippingModal = document.getElementById("shipping-modal");
    var addShippingBtn = document.getElementById("add-shipping-btn");
    var closeModalSpan = document.querySelector(".close-modal");
    var freeShippingRadio = document.getElementById("free-shipping");
    var customShippingRadio = document.getElementById("custom-shipping");
    var customShippingOptions = document.getElementById("custom-shipping-options");
    var shippingCloseBtn = document.getElementById("shipping-close-btn");
    var shippingApplied = false;

    // Hiển thị modal khi nhấn vào nút thêm phương thức vận chuyển
    addShippingBtn.addEventListener("click", function(e) {
        e.preventDefault();
        shippingModal.style.display = "block";
    });

    // Đóng modal khi nhấn vào dấu X
    closeModalSpan.addEventListener("click", function() {
        shippingModal.style.display = "none";
    });

    // Khi chọn "Miễn phí vận chuyển"
    freeShippingRadio.addEventListener('change', function() {
        customShippingOptions.style.display = 'none'; // Ẩn khi chọn miễn phí
    });

    // Khi chọn "Vận chuyển tùy chọn"
    customShippingRadio.addEventListener('change', function() {
        customShippingOptions.style.display = 'block'; // Hiện 2 ô nhập khi chọn tùy chọn
    });

// Khi áp dụng phương thức vận chuyển
document.getElementById('shipping-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var shippingCost = 0; // Khởi tạo phí vận chuyển

    if (freeShippingRadio.checked) {
        shippingCost = 0; // Nếu chọn miễn phí vận chuyển
    } else if (customShippingRadio.checked) {
        shippingCost = parseInt(document.getElementById('shipping-cost').value) || 0; // Lấy phí vận chuyển từ ô nhập
    }

    // Cập nhật phí vận chuyển
    document.getElementById('shipping').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(shippingCost);
    
    // Cập nhật tổng cộng
    updateTotalAfterShipping(shippingCost);

    shippingModal.style.display = "none"; // Đóng modal
});

// Hàm cập nhật tổng cộng sau khi đã tính phí vận chuyển
function updateTotalAfterShipping(shippingCost) {
    // Lấy tổng cộng hiện tại
    var totalText = document.getElementById('total').textContent;
    var currentTotal = parseInt(totalText.replace('₫', '').replace(/\./g, '').trim()) || 0; // Lấy giá trị tổng cộng hiện tại

    // Tính tổng cộng mới
    var newTotal = currentTotal + shippingCost; // Cộng thêm phí vận chuyển

    // Hiển thị tổng cộng mới
    document.getElementById('total').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(newTotal);
}



    // Xử lý nút đóng hoặc xóa phương thức vận chuyển
    shippingCloseBtn.addEventListener('click', function() {
        if (shippingApplied) {
            // Đặt lại trạng thái nếu đã áp dụng
            freeShippingRadio.checked = false;
            customShippingRadio.checked = true;
            document.getElementById('shipping-name').value = "";
            document.getElementById('shipping-cost').value = "";
            shippingCloseBtn.textContent = "Đóng"; // Đặt lại nút thành "Đóng"
            shippingApplied = false;
        }
        shippingModal.style.display = "none"; // Đóng modal
    });

    // Đóng modal khi nhấp ra ngoài modal
    window.addEventListener('click', function(event) {
        if (event.target == shippingModal) {
            shippingModal.style.display = "none";
        }
    });
});
</script>




    </main>

    <script>
document.addEventListener("DOMContentLoaded", function() {
    var searchBar = document.getElementById('search-bar');
    var productList = document.getElementById('product-list');
    var productItems = document.querySelectorAll('.product-item');

    // Khi người dùng click vào thanh tìm kiếm
    searchBar.addEventListener('click', function() {
        productList.style.display = 'block';  // Hiển thị danh sách sản phẩm
    });

    // Khi người dùng nhập từ khóa vào thanh tìm kiếm
    searchBar.addEventListener('input', function() {
        var filter = searchBar.value.toLowerCase();  // Lấy giá trị từ thanh tìm kiếm và chuyển thành chữ thường
        productItems.forEach(function(item) {
            var productText = item.textContent.toLowerCase();  // Lấy nội dung sản phẩm và chuyển thành chữ thường
            if (productText.includes(filter)) {
                item.style.display = '';  // Hiển thị sản phẩm phù hợp
            } else {
                item.style.display = 'none';  // Ẩn sản phẩm không phù hợp
            }
        });
    });
    // Hàm chọn sản phẩm và hiển thị sản phẩm vào chi tiết đơn hàng
    window.selectProduct = function(ma, ten, gia) {
        var selectedProductsDiv = document.getElementById('selected-products');
        
        // Thêm sản phẩm vào chi tiết đơn hàng
        var productHTML = `
            <div class="product-row">
                <span class="product-name">${ten}</span>
                <input type="number" value="1" min="1" class="quantity" onchange="updateTotalPrice(this, ${gia})">
                <span class="product-price">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(gia)}</span>
                <button class="remove-product" onclick="removeProduct(this)">  X</button>
            </div>
        `;
        selectedProductsDiv.innerHTML += productHTML;

        // Ẩn danh sách sản phẩm sau khi chọn
        document.getElementById('product-list').style.display = 'none';

        // Cập nhật tổng giá
        updateSubtotal();
    };

    // Hàm cập nhật tổng giá
    window.updateTotalPrice = function(quantityInput, price) {
        var productRow = quantityInput.parentElement;
        var totalPriceElement = productRow.querySelector('.product-price');
        var quantity = quantityInput.value;
        var totalPrice = price * quantity;

        totalPriceElement.innerHTML = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(totalPrice);
        updateSubtotal();
    }

    // Hàm xóa sản phẩm khi click vào nút "X"
    window.removeProduct = function(button) {
        var productRow = button.parentElement;
        productRow.remove();
        updateSubtotal();
    }

    // Hàm cập nhật tạm tính và tổng giá
    function updateSubtotal() {
        var subtotal = 0;
        var hasProducts = false; // Biến để kiểm tra nếu có sản phẩm trong danh sách

        document.querySelectorAll('.product-row').forEach(function(row) {
            var priceText = row.querySelector('.product-price').textContent.replace(' VND', '').replace(/\./g, '');
            subtotal += parseInt(priceText);
            hasProducts = true; // Có sản phẩm trong danh sách
        });

        var shipping = 0; // Giả sử phí vận chuyển là 0
        var total = subtotal + shipping;

        // Hiển thị giá trị tạm tính
        document.getElementById('subtotal').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(subtotal);
        document.getElementById('shipping').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(shipping);

        // Nếu không có sản phẩm nào, đặt giảm giá về 0
        if (!hasProducts) {
            document.getElementById('discount-amount').value = "";
            document.getElementById('discount-percent').value = "";
            document.getElementById('discount-value').textContent = "0đ";
        }

        // Tính toán lại tổng giá với giảm giá
        applyDiscount(subtotal, shipping);
    }

    // Hàm áp dụng giảm giá và cập nhật tổng cộng
    function applyDiscount(subtotal, shipping) {
        var discountAmount = parseInt(document.getElementById('discount-amount').value.replace(/\./g, '')) || 0;
        var discountPercent = parseInt(document.getElementById('discount-percent').value) || 0;

        var discountValue = discountAmount;

        // Nếu có phần trăm giảm giá, tính giá trị giảm theo phần trăm
        if (discountPercent > 0) {
            discountValue = (subtotal * discountPercent) / 100;
        }

        var total = subtotal - discountValue + shipping;

        // Hiển thị số tiền giảm giá
        document.getElementById('discount-value').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(discountValue);

        // Cập nhật tổng cộng
        document.getElementById('total').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }

    // Popup khuyến mãi
    var discountPopup = document.getElementById('discount-popup');
    var addDiscountBtn = document.getElementById('add-discount');
    var closePopupBtn = document.getElementById('close-popup');
    var applyDiscountBtn = document.getElementById('apply-discount');
    var discountApplied = false; // Biến để kiểm tra nếu khuyến mãi đã áp dụng

    // Mở popup khi click vào "Thêm khuyến mãi"
    addDiscountBtn.addEventListener('click', function(e) {
        e.preventDefault();
        discountPopup.style.display = 'block';

        // Nếu khuyến mãi đã áp dụng, chuyển nút "Đóng" thành "Xóa"
        if (discountApplied) {
            closePopupBtn.textContent = "Xóa";
        } else {
            closePopupBtn.textContent = "Đóng";
        }
    });

    // Xử lý khi click "Áp dụng" khuyến mãi
    applyDiscountBtn.addEventListener('click', function() {
        var discountAmount = document.getElementById('discount-amount').value;
        var discountPercent = document.getElementById('discount-percent').value;

        // Đặt biến kiểm tra khuyến mãi đã áp dụng
        discountApplied = true;

        // Sau khi áp dụng, đóng popup và chuyển nút "Đóng" thành "Xóa"
        closePopupBtn.textContent = "Xóa";
        discountPopup.style.display = 'none';

        // Cập nhật lại tổng cộng sau khi áp dụng giảm giá
        updateSubtotal();
    });

    // Xử lý khi click vào "Xóa" (khi khuyến mãi đã được áp dụng)
    closePopupBtn.addEventListener('click', function() {
        if (discountApplied) {
            // Xóa giảm giá và đặt lại giá trị
            document.getElementById('discount-amount').value = "";
            document.getElementById('discount-percent').value = "";
            document.getElementById('discount-value').textContent = "0đ";

            // Đặt lại biến kiểm tra khuyến mãi
            discountApplied = false;

            // Cập nhật lại tổng cộng sau khi xóa giảm giá
            updateSubtotal();
        }

        // Đóng popup
        discountPopup.style.display = 'none';
    });

    // Đóng popup nếu click ra ngoài bảng
    window.addEventListener('click', function(event) {
        if (!discountPopup.contains(event.target) && !addDiscountBtn.contains(event.target)) {
            discountPopup.style.display = 'none';
        }
    });
});
// Lấy các phần tử cần thiết
var modal = document.getElementById("shipping-modal");
var btn = document.getElementById("add-shipping-btn");
var span = document.getElementsByClassName("close-modal")[0];
// Phương thức vận chuyển
document.addEventListener("DOMContentLoaded", function() {
    var shippingModal = document.getElementById("shipping-modal");
    var freeShippingRadio = document.getElementById("free-shipping");
    var customShippingRadio = document.getElementById("custom-shipping");
    var customShippingOptions = document.getElementById("custom-shipping-options");
    var shippingCloseBtn = document.getElementById("shipping-close-btn");
    var shippingApplied = false; // Biến kiểm tra trạng thái đã áp dụng phương thức vận chuyển

    // Khi chọn "Miễn phí vận chuyển"
    freeShippingRadio.addEventListener('change', function() {
        customShippingOptions.style.display = 'none'; // Ẩn 2 ô nhập khi chọn Miễn phí vận chuyển
    });

    // Khi chọn "Vận chuyển tùy chọn"
    customShippingRadio.addEventListener('change', function() {
        customShippingOptions.style.display = 'block'; // Hiện 2 ô nhập khi chọn Vận chuyển tùy chọn
    });

    // Khi áp dụng phương thức vận chuyển
    document.getElementById('shipping-form').addEventListener('submit', function(e) {
        e.preventDefault();
        shippingApplied = true;
        shippingCloseBtn.textContent = "Xóa"; // Sau khi áp dụng, đổi nút Đóng thành Xóa
        shippingModal.style.display = "none";
    });

    // Xử lý khi click nút Đóng/Xóa
    shippingCloseBtn.addEventListener('click', function() {
        if (shippingApplied) {
            // Nếu đã áp dụng, nút "Đóng" chuyển thành "Xóa"
            freeShippingRadio.checked = false;
            customShippingRadio.checked = true;
            document.getElementById('shipping-name').value = "";
            document.getElementById('shipping-cost').value = "";
            document.getElementById('shipping').value = "";
            shippingCloseBtn.textContent = "Đóng"; // Đặt lại trạng thái của nút
            shippingApplied = false; // Đặt lại trạng thái kiểm tra
        }
        shippingModal.style.display = "none"; // Đóng modal
    });
});
    // Khi áp dụng phương thức vận chuyển
document.getElementById('shipping-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var shippingCost = 0; // Khởi tạo phí vận chuyển

    if (freeShippingRadio.checked) {
        shippingCost = 0; // Nếu chọn miễn phí vận chuyển
    } else if (customShippingRadio.checked) {
        shippingCost = parseInt(document.getElementById('shipping-cost').value) || 0; // Lấy phí vận chuyển từ ô nhập
    }

    // Cập nhật phí vận chuyển và tổng cộng
    document.getElementById('shipping').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(shippingCost);
    updateSubtotal(); // Cập nhật lại tổng cộng
    shippingModal.style.display = "none"; // Đóng modal
});
document.getElementById('shipping-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var shippingCost = 0; // Khởi tạo phí vận chuyển

    if (freeShippingRadio.checked) {
        shippingCost = 0; // Nếu chọn miễn phí vận chuyển
    } else if (customShippingRadio.checked) {
        shippingCost = parseInt(document.getElementById('shipping-cost').value) || 0; // Lấy phí vận chuyển từ ô nhập
    }

    // Cập nhật phí vận chuyển và tổng cộng
    document.getElementById('shipping').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(shippingCost);
    updateSubtotal(); // Cập nhật lại tổng cộng
    shippingModal.style.display = "none"; // Đóng modal
});

// Hàm chỉ cho phép nhập số và loại bỏ ký tự không hợp lệ
function allowOnlyNumbers(event) {
    const key = event.key;
    // Kiểm tra xem phím nhấn có phải là số hay không (0-9) hoặc các phím điều khiển như backspace
    if (!/^\d$/.test(key) && key !== "Backspace" && key !== "Tab" && key !== "ArrowLeft" && key !== "ArrowRight") {
        event.preventDefault(); // Ngăn không cho nhập ký tự không hợp lệ
    }
}



// Hàm chỉ cho phép nhập số và loại bỏ ký tự không hợp lệ
function allowOnlyNumbers(event) {
    const key = event.key;
    // Kiểm tra xem phím nhấn có phải là số hay không (0-9) hoặc các phím điều khiển như backspace
    if (!/^\d$/.test(key) && key !== "Backspace" && key !== "Tab" && key !== "ArrowLeft" && key !== "ArrowRight") {
        event.preventDefault(); // Ngăn không cho nhập ký tự không hợp lệ
    }
}

// Gán sự kiện input cho ô nhập giảm giá
const discountInput = document.getElementById('discount-amount');
discountInput.addEventListener('keypress', allowOnlyNumbers); // Chỉ cho phép nhập số

// Gán sự kiện input cho ô nhập phí vận chuyển
const shippingInput = document.getElementById('shipping-cost');
shippingInput.addEventListener('keypress', allowOnlyNumbers); // Chỉ cho phép nhập số

// Khi áp dụng phương thức vận chuyển
document.getElementById('shipping-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var shippingCost = 0; // Khởi tạo phí vận chuyển

    if (freeShippingRadio.checked) {
        shippingCost = 0; // Nếu chọn miễn phí vận chuyển
    } else if (customShippingRadio.checked) {
        shippingCost = parseInt(document.getElementById('shipping-cost').value) || 0; // Lấy phí vận chuyển từ ô nhập
    }

    // Cập nhật phí vận chuyển
    document.getElementById('shipping').textContent = shippingCost; // Hiển thị phí vận chuyển
    updateSubtotal(); // Cập nhật lại tổng cộng
    shippingModal.style.display = "none"; // Đóng modal
});
console.log("Xóa vận chuyển, giá trị được đặt về 0");
document.getElementById('shipping').value = "0"; // Đặt giá trị về 0


    </script>

</body>

</html>
