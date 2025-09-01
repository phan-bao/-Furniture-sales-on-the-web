<?php
session_start();

// Bật hiển thị lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra login
if (!isset($_SESSION['ma_khach_hang'])) {
    header("Location: login.php");
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "furniture_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy user_id và thông tin khách hàng cơ bản
$stmt_user = $conn->prepare("
    SELECT a.user_id, kh.ten_khach_hang, kh.mail, kh.so_dien_thoai
    FROM account a
    JOIN khach_hang kh ON a.ma_khach_hang = kh.ma_khach_hang
    WHERE a.ma_khach_hang = ?
");
if (!$stmt_user) {
    die("Prepare failed: " . $conn->error);
}
$stmt_user->bind_param("i", $ma_khach_hang);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    echo "Không tìm thấy thông tin khách hàng.";
    exit();
}

$user_info = $result_user->fetch_assoc();
$user_id = $user_info['user_id'];
$stmt_user->close();

// Lấy giỏ hàng
$stmt_cart = $conn->prepare("
    SELECT 
        g.id_gio_hang, g.SKU_phien_ban, g.quantity, 
        pb.gia, pb.hinh_anh, 
        sp.ten_san_pham, pb.mau_sac, pb.vat_lieu, pb.kich_thuoc
    FROM giohang g
    JOIN phien_ban_san_pham pb ON g.SKU_phien_ban = pb.SKU_phien_ban
    JOIN san_pham sp ON pb.SKU_san_pham = sp.SKU_san_pham
    WHERE g.user_id = ?
");
if (!$stmt_cart) {
    die("Prepare failed: " . $conn->error);
}
$stmt_cart->bind_param("i", $user_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

$cart_items = [];
$total = 0;
if ($result_cart->num_rows > 0) {
    while ($row = $result_cart->fetch_assoc()) {
        $row['total_price'] = $row['gia'] * $row['quantity'];
        $total += $row['total_price'];
        $cart_items[] = $row;
    }
} else {
    echo "Giỏ hàng trống.";
    exit();
}
$stmt_cart->close();

// Lấy danh sách địa chỉ
$stmt_addr = $conn->prepare("
    SELECT ma_dia_chi, so_nha, duong_pho, quoc_gia, thanh_pho, huyen, xa, so_dien_thoai_giao_hang, ten_dia_chi
    FROM dia_chi
    WHERE ma_khach_hang = ?
");
if (!$stmt_addr) {
    die("Prepare failed: " . $conn->error);
}
$stmt_addr->bind_param("i", $ma_khach_hang);
$stmt_addr->execute();
$result_addr = $stmt_addr->get_result();

$addresses = [];
while ($ad = $result_addr->fetch_assoc()) {
    $addresses[] = $ad;
}
$stmt_addr->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/footer.css">
    <link rel="stylesheet" href="../Css/thanhtoan.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p1P3oFrReiYk1cMfYGLFy8YZtvt4wVevvZ9iF8nFejIKGEmhx06vE1o/GoGVCdJp2z1ax1+nQXOkGL+b4gPeFw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head> 
<body>
    <?php include '../Partials/header.php'; ?>

    <div class="breadcrumb">
        <span class="breadcrumb-home">Trang Chủ</span>
        <span class="breadcrumb-separator">›</span>
        <span class="breadcrumb-current">Thanh Toán</span>
    </div>

    <form action="../BE/process_order.php" method="POST" class="checkout-form">
        <div class="checkout-container">
           

            <div class="step-navigation">
    <button type="button" class="step-btn active" id="step-1-btn" data-step="1" onclick="showStep(1)">
        Xác Nhận Sản Phẩm
    </button>
    <button type="button" class="step-btn" id="step-2-btn" data-step="2" onclick="showStep(2)">
        Thông Tin Khách Hàng & Địa Chỉ
    </button>
    <button type="button" class="step-btn" id="step-3-btn" data-step="3" onclick="showStep(3)">
        Phương Thức Vận Chuyển & Thanh Toán
    </button>
</div>
<!-- Step 1: Xác Nhận Sản Phẩm -->
<div id="step-1" class="checkout-step">
    <h3>Sản Phẩm Trong Giỏ Hàng</h3>
    <?php if (!empty($cart_items)): ?>
        <div class="checkout-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="checkout-item">
                    <div class="item-image">
                        <?php if (!empty($item['hinh_anh']) && file_exists('../images/' . $item['hinh_anh'])): ?>
                            <img src="../images/<?php echo htmlspecialchars($item['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($item['ten_san_pham']); ?>">
                        <?php else: ?>
                            <img src="../images/default.png" alt="No Image">
                        <?php endif; ?>
                    </div>
                    <div class="item-details">
                        <h4 class="item-name"><?php echo htmlspecialchars($item['ten_san_pham']); ?></h4>
                        <p class="item-info"><strong>Màu sắc:</strong> <?php echo htmlspecialchars($item['mau_sac'] ?? ''); ?></p>
                        <p class="item-info"><strong>Vật liệu:</strong> <?php echo htmlspecialchars($item['vat_lieu'] ?? ''); ?></p>
                        <p class="item-info"><strong>Kích thước:</strong> <?php echo htmlspecialchars($item['kich_thuoc'] ?? ''); ?></p>
                        <p class="item-info"><strong>Số lượng:</strong> <?php echo $item['quantity']; ?></p>
                        <p class="item-info"><strong>Giá:</strong> <?php echo number_format($item['gia'], 0, ',', '.') . ' VNĐ'; ?></p>
                        <p class="item-info"><strong>Tổng:</strong> <?php echo number_format($item['total_price'], 0, ',', '.') . ' VNĐ'; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Phần Mã Giảm Giá -->
        <div class="discount-section">
            <input type="text" id="discount-code" placeholder="Nhập mã giảm giá" />
            <button type="button" class="apply-discount-btn">Áp Dụng</button>
        </div>
        
        <div class="checkout-total">
            <strong>Tổng Tiền: </strong> <?php echo number_format($total, 0, ',', '.') . ' VNĐ'; ?>
        </div>
    <?php else: ?>
        <p>Giỏ hàng trống.</p>
    <?php endif; ?>
    <button type="button" class="next-btn" onclick="showStep(2)">Tiếp Tục</button>
</div>

            <!-- Step 2: Thông Tin Khách Hàng & Địa Chỉ -->
            <div id="step-2" class="checkout-step" style="display: none;">
                <h3>Thông Tin Khách Hàng</h3>
                <p><strong>Tên:</strong> <?php echo htmlspecialchars($user_info['ten_khach_hang']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['mail']); ?></p>
                <p><strong>Số Điện Thoại:</strong> <?php echo htmlspecialchars($user_info['so_dien_thoai']); ?></p>

                <h3>Địa Chỉ Giao Hàng</h3>
                <?php if (!empty($addresses)): ?>
                    <div class="address-checkboxes">
                        <?php foreach($addresses as $ad):
                            $full_addr = $ad['so_nha'].' '.$ad['duong_pho'].', '.$ad['xa'].', '.$ad['huyen'].', '.$ad['thanh_pho'].', '.$ad['quoc_gia'];

                            $labelParts = [];
                            if (!empty($ad['ten_dia_chi'])) {
                                $labelParts[] = $ad['ten_dia_chi'];
                            }
                            if (!empty($ad['so_dien_thoai_giao_hang'])) {
                                $labelParts[] = $ad['so_dien_thoai_giao_hang'];
                            }

                            $label = implode('  -  ', $labelParts);
                        ?>
                            <label class="address-option">
                                <input type="radio" name="ma_dia_chi" value="<?php echo $ad['ma_dia_chi']; ?>" required>
                                <span><?php echo htmlspecialchars($label . ' ' . $full_addr); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Chưa có địa chỉ nào, vui lòng thêm địa chỉ mới.</p>
                <?php endif; ?>

                <button type="button" onclick="showAddAddressForm()">Thêm Địa Chỉ Mới</button>

                <!-- Thêm Địa Chỉ Mới Form -->
                <div id="add_address_form" style="display:none;">
                    <h3>Thêm Địa Chỉ Mới</h3>
                    <label for="address_search">Tìm Kiếm Địa Chỉ:</label>
                    <input type="text" id="address_search" placeholder="Nhập địa chỉ..." autocomplete="off">
                    <ul id="address_suggestions"></ul>

                    <label>Số Nhà:</label><input type="text" name="so_nha" required>
                    <label>Đường Phố:</label><input type="text" name="duong_pho" required>
                    <label>Quốc Gia:</label><input type="text" name="quoc_gia" value="Vietnam" required>
                    <label>Thành Phố:</label><input type="text" name="thanh_pho" required>
                    <label>Quận/Huyện:</label><input type="text" name="huyen" required>
                    <label>Xã/Phường:</label><input type="text" name="xa" required>
                    <label>SĐT Giao Hàng:</label><input type="text" name="so_dien_thoai_giao_hang" required>
                    <label>Tên Địa Chỉ (VD: Nhà, Cơ Quan):</label><input type="text" name="ten_dia_chi" required>
                    
                    <div id="map" style="height:200px; margin-top:10px;"></div>
                    <button type="submit" name="them_dia_chi">Lưu Địa Chỉ</button>
                </div>

                <button type="button" class="prev-btn" onclick="showStep(1)">Quay Lại</button>
                <button type="button" class="next-btn" onclick="showStep(3)">Tiếp Tục</button>
            </div>

            <div id="step-3" class="checkout-step" style="display: none;">
    <h3>Phương Thức Thanh Toán</h3>
    <select name="phuong_thuc_thanh_toan" id="payment-method" required>
        <option value="" disabled selected>-- Chọn phương thức thanh toán --</option>
        <option value="Thanh toán khi nhận hàng">Thanh toán khi nhận hàng</option>
        <option value="Thanh toán trực tuyến">Chuyển khoản qua VietQR</option>
    </select>

    <div id="qr-code-section" style="display:none; text-align: center; margin-top: 20px;">
        <h4>Mã QR Thanh Toán</h4>
        <img src="" id="qr-code" alt="Mã QR VietQR" style="width: 200px; height: 200px;">
        <p style="font-size: 18px; margin-top: 10px;">
            <strong>Số TK:</strong> 18811181<br>
            <strong>Ngân hàng:</strong> Ngân hàng Á Châu (ACB)<br>
        </p>
        <a id="vietqr-link" href="#" target="_blank" style="text-decoration: none; color: #00796b; font-weight: bold;">Mở VietQR</a>
    </div>

    <!-- Phần Thành Công Thanh Toán (Ẩn Mặc Định) -->
  <!-- Phần Thành Công Thanh Toán (Ẩn Mặc Định) -->
<div id="payment-success-section" class="payment-success" style="display: none;">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i> <!-- Dấu tích xanh bằng Font Awesome -->
    </div>
    <h4>Thanh Toán Thành Công !</h4>
    <p>Vui Lòng Nhấn Vào Nút Bên Dưới. Chúng Tôi Sẽ Xử Lý Đơn Hàng Của Bạn Ngay Lập Tức</p>
</div>


    <div id="countdown-timer" style="display:none; text-align: center; margin-top: 20px;">
        <p>Thời gian còn lại để thanh toán: <span id="timer">5:00</span></p>
    </div>

    <div id="bank-check-section" style="display:none; text-align: center; margin-top: 20px;">
        <p id="bank-status" style="font-size: 18px; color: orange;">Đang chờ thanh toán...</p>
    </div>

    <p id="payment-message" style="text-align: center; margin-top: 20px;"></p>

    <button type="button" class="prev-btn" onclick="showStep(2)">Quay Lại</button>
    <button type="submit" class="checkout-button" name="submit_order">Đặt Hàng</button>

</div>

    </form>

    <script>
function showStep(step) {
    // Ẩn tất cả các bước và loại bỏ `required` khỏi các trường trong bước bị ẩn
    document.querySelectorAll('.checkout-step').forEach(function (stepElement) {
        stepElement.style.display = 'none'; // Ẩn bước
        stepElement.querySelectorAll('input, select, textarea').forEach(function (input) {
            input.removeAttribute('required'); // Xóa thuộc tính required khi ẩn
        });
    });

    // Hiển thị bước hiện tại và thêm lại `required` vào các trường cần thiết
    const currentStep = document.getElementById('step-' + step);
    currentStep.style.display = 'block'; // Hiển thị bước
    currentStep.querySelectorAll('input, select, textarea').forEach(function (input) {
        if (input.dataset.required === "true") {
            input.setAttribute('required', true); // Gán lại required nếu cần
        }
    });

    // Cập nhật trạng thái các nút điều hướng bước
    updateStepNavigation(step);
}


function updateStepNavigation(currentStep) {
    // Xóa trạng thái active và completed
    document.querySelectorAll('.step-btn').forEach(function (button, index) {
        button.classList.remove('active', 'completed');
        // Nếu bước đã hoàn thành (nhỏ hơn bước hiện tại)
        if (index + 1 < currentStep) {
            button.classList.add('completed');
        }
    });

    // Thêm active cho bước hiện tại
    document.getElementById('step-' + currentStep + '-btn').classList.add('active');
}

// Hiển thị mặc định bước 1 khi tải trang
document.addEventListener('DOMContentLoaded', function () {
    showStep(1); // Hiển thị step 1 khi load trang
});

    </script>
</body>
</html>

    </script>
</body>
</html>

<script>
    
let timer; // Biến lưu timer
let startTime = null; // Thời gian bắt đầu
let isPaymentConfirmed = false; // Trạng thái để kiểm tra đã xác nhận thanh toán
let generatedOrderInfo = ""; // Lưu mã nội dung thanh toán ngẫu nhiên

// Hàm tạo nội dung thanh toán ngẫu nhiên
function generateRandomOrderInfo() {
    const randomCode = Math.floor(1000 + Math.random() * 9000); // Sinh số ngẫu nhiên 4 chữ số
    return `DH${randomCode}`;
}

// Hàm hiển thị QR và bắt đầu đếm ngược
function showQRCode(accountNumber, amount) {
    const qrCodeSection = document.getElementById("qr-code-section");
    const qrCode = document.getElementById("qr-code");
    const vietqrLink = document.getElementById("vietqr-link");

    // Sinh nội dung thanh toán ngẫu nhiên
    generatedOrderInfo = generateRandomOrderInfo();
    console.log("Nội dung thanh toán:", generatedOrderInfo);

    // URL VietQR
    const qrUrl = `https://img.vietqr.io/image/acb-${accountNumber}-compact.jpg?amount=${amount}&addInfo=${encodeURIComponent(generatedOrderInfo)}`;
    qrCode.src = qrUrl;
    vietqrLink.href = qrUrl;
    qrCodeSection.style.display = "block";

    // Lưu thời gian bắt đầu
    startTime = new Date();
    console.log("Bắt đầu đếm ngược:", startTime);

    // Reset trạng thái thanh toán
    isPaymentConfirmed = false;

    // Bắt đầu đếm ngược
    startCountdown(300, accountNumber, amount); // 300 giây = 5 phút
}

// Hàm đếm ngược
function startCountdown(duration, accountNumber, amount) {
    const countdownElement = document.getElementById("countdown-timer");
    countdownElement.style.display = "block";
    let timeLeft = duration;

    timer = setInterval(() => {
        // Nếu thanh toán đã được xác nhận, dừng mọi hoạt động
        if (isPaymentConfirmed) {
            clearInterval(timer); // Dừng đếm ngược
            return;
        }

        // Cập nhật đồng hồ đếm ngược
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownElement.querySelector("#timer").textContent = `${minutes}:${seconds < 10 ? "0" + seconds : seconds}`;

        // Gọi API kiểm tra thanh toán
        checkBankPayment(accountNumber, amount, generatedOrderInfo, (status) => {
            if (status === "success" && !isPaymentConfirmed) {
                isPaymentConfirmed = true; // Đánh dấu đã xác nhận thanh toán
                clearInterval(timer); // Dừng đếm ngược

                // Ẩn các phần liên quan đến QR và đếm ngược
                document.getElementById("qr-code-section").style.display = "none";
                countdownElement.style.display = "none";
                document.getElementById("bank-check-section").style.display = "none";

                // Hiển thị phần thông báo thành công với hiệu ứng hoạt hình
                const paymentSuccessSection = document.getElementById("payment-success-section");
                paymentSuccessSection.style.display = "block";

                // Thêm lớp 'animate' để kích hoạt các hiệu ứng CSS
                paymentSuccessSection.classList.add('animate');

                // Tùy chọn: Hiển thị thông báo alert (có thể bỏ qua nếu không muốn)
                // alert("Thanh toán thành công!");
            }
        });

        timeLeft--;

        if (timeLeft < 0 && !isPaymentConfirmed) {
            clearInterval(timer);
            countdownElement.querySelector("#timer").textContent = "0:00";
            countdownElement.querySelector("p").textContent = "Thời gian thanh toán đã hết.";
        }
    }, 1000);
}


// Hàm Gọi API Kiểm Tra Thanh Toán
function checkBankPayment(accountNumber, amount, orderInfo, callback) {
    const apiUrl =
        "https://script.googleusercontent.com/macros/echo?user_content_key=nh_0wNY5ida6aZAupHHJsxwR7gka28Zgy_rgz02iiQbW2savDcKuyW8JBn49QX1e7rpGglJqtGzSU0elYo_GWmm417X1JcLsm5_BxDlH2jW0nuo2oDemN9CCS2h10ox_1xSncGQajx_ryfhECjZEnD-mifvw1z_QEVD5EGyoNkwID9eF0QBDbP1nNc56gGQcQnSterGhm3HVhT5HnphFLxQsN08LXezGDih5z0VTJLWb0PKoXoG73Q&lib=M4N-UeuxAbkuK8uqm2Zxo-Rm7uEsyuDi6";

    fetch(apiUrl)
        .then((response) => response.json())
        .then((data) => {
            console.log("Dữ liệu API:", data);

            if (data.error) {
                console.error("API trả về lỗi:", data.error);
                callback("error");
                return;
            }

            // Lấy giao dịch mới nhất trong mảng `data.data`
            const latestTransaction = data.data.find((tx) => {
                // Chỉ kiểm tra giao dịch mới hơn
                const transactionTime = new Date(tx["Ngày diễn ra"]);
                return (
                    tx["Số tài khoản"] === accountNumber &&
                    tx["Giá trị"] === amount &&
                    tx["Mô tả"].includes(orderInfo) && // Kiểm tra mã nội dung thanh toán
                    transactionTime > startTime // Chỉ tính giao dịch sau thời gian bắt đầu
                );
            });

            if (latestTransaction) {
                callback("success"); // Thanh toán thành công
            } else {
                callback("pending"); // Chưa có thanh toán
            }
        })
        .catch((error) => {
            console.error("Lỗi khi kiểm tra thanh toán:", error);
            callback("error"); // Lỗi hệ thống
        });
}

// Khởi tạo mã QR khi chọn phương thức thanh toán
document.getElementById("payment-method").addEventListener("change", function () {
    const totalAmount = <?php echo json_encode($total); ?>; // Lấy tổng tiền từ PHP
    if (this.value === "Thanh toán trực tuyến") {
        showQRCode("18811181", totalAmount); // Số tài khoản, số tiền
    }
});


function showAddAddressForm() {
    document.getElementById('add_address_form').style.display = 'block';
}

// Xóa toàn bộ code liên quan đến Leaflet map, marker, và setView.
// Chỉ giữ phần logic tìm kiếm địa chỉ và suggestions.

const addressSearch = document.getElementById('address_search');
const suggestionsBox = document.getElementById('address_suggestions');

// Giữ phần JS logic cho autocomplete địa chỉ, bỏ tất cả code map.
let timeout = null;

addressSearch.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value;

    if (query.length < 3) {
        suggestionsBox.innerHTML = '';
        return;
    }

    timeout = setTimeout(() => {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&countrycodes=vn&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                showSuggestions(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }, 500);
});

function showSuggestions(predictions) {
    suggestionsBox.innerHTML = '';

    if (predictions.length === 0) {
        return;
    }

    predictions.forEach(prediction => {
        const listItem = document.createElement('li');

        // Loại bỏ postcode trong display_name
        let cleanedDisplayName = prediction.display_name.replace(/\b\d{4,6}\b/g, '');
        cleanedDisplayName = cleanedDisplayName.replace(/,\s*,/g, ', ').trim();

        listItem.textContent = cleanedDisplayName;
        listItem.style.borderBottom = '1px solid #ddd';
        listItem.style.cursor = 'pointer';
        listItem.style.padding = '8px';

        listItem.addEventListener('click', function() {
            addressSearch.value = cleanedDisplayName;
            suggestionsBox.innerHTML = '';

            console.log('Nominatim Address:', prediction.address);

            // Điền thông tin vào các trường
            soNha.value = prediction.address.house_number || '';
            duongPho.value = prediction.address.road || prediction.address.street || prediction.address.residential || '';
            quocGia.value = prediction.address.country || '';
            thanhPho.value = prediction.address.city || prediction.address.town || prediction.address.state || '';
            let district = prediction.address.suburb || '';
            huyen.value = district;
            xa.value = prediction.address.quarter || '';
        });

        suggestionsBox.appendChild(listItem);
    });
}
document.getElementById('add-address-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn form gửi dữ liệu theo cách thông thường

    const formData = new FormData(this);

    fetch('../BE/process_order.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // Thông báo thành công
            // Cập nhật danh sách địa chỉ hoặc reload lại phần địa chỉ
            location.reload(); // Reload lại trang để hiển thị địa chỉ mới
        } else {
            alert(data.message); // Thông báo lỗi nếu có
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
    });
});

// Khi người dùng nhấp ngoài dropdown, đóng nó lại
document.addEventListener('click', function(e) {
    if (e.target !== addressSearch) {
        suggestionsBox.innerHTML = '';
    }
});


function showSuggestions(predictions) {
    suggestionsBox.innerHTML = '';

    if (predictions.length === 0) {
        return;
    }

    predictions.forEach(prediction => {
        const listItem = document.createElement('li');
        let cleanedDisplayName = prediction.display_name.replace(/\b\d{4,6}\b/g, '');
        cleanedDisplayName = cleanedDisplayName.replace(/,\s*,/g, ', ').trim();
        listItem.textContent = cleanedDisplayName;
        listItem.style.borderBottom = '1px solid #ddd';
        listItem.style.cursor = 'pointer';
        listItem.style.padding = '8px';

        listItem.addEventListener('click', function() {
            addressSearch.value = cleanedDisplayName;
            suggestionsBox.innerHTML = '';

            console.log('Nominatim Address:', prediction.address);

            // Tìm các input field
            const soNha = document.querySelector('[name="so_nha"]');
            const duongPho = document.querySelector('[name="duong_pho"]');
            const quocGia = document.querySelector('[name="quoc_gia"]');
            const thanhPho = document.querySelector('[name="thanh_pho"]');
            const huyen = document.querySelector('[name="huyen"]');
            const xa = document.querySelector('[name="xa"]');

            soNha.value = prediction.address.house_number || '';
            duongPho.value = prediction.address.road || prediction.address.street || prediction.address.residential || '';
            quocGia.value = prediction.address.country || 'Vietnam';
            thanhPho.value = prediction.address.city || prediction.address.town || prediction.address.state || '';
            let district = prediction.address.suburb || prediction.address.county || prediction.address.city_district || '';
            huyen.value = district;
            xa.value = prediction.address.quarter || prediction.address.neighbourhood || '';

            const lat = prediction.lat;
            const lon = prediction.lon;
            map.setView([lat, lon], 15);

            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lon]).addTo(map)
                .bindPopup(prediction.display_name)
                .openPopup();
        });

        suggestionsBox.appendChild(listItem);
    });
}

document.addEventListener('click', function(e) {
    if (e.target !== addressSearch) {
        suggestionsBox.innerHTML = '';
    }
});
function showAddAddressForm() {
    document.getElementById('add_address_form').style.display = 'block';
}

</script>

</body>
</html>
