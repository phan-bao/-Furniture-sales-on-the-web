<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "furniture_store";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$current_username = $_SESSION['username'];

// Lấy thông tin tài khoản
$query = "SELECT a.username, a.password, k.ma_khach_hang, k.ten_khach_hang, k.mail, k.so_dien_thoai, k.facebook_id
          FROM account a
          JOIN khach_hang k ON a.ma_khach_hang = k.ma_khach_hang
          WHERE a.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $current_username);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();
$stmt->close();

$is_facebook_user = !empty($user_info['facebook_id']);

// Lấy danh sách đơn hàng cùng thông tin chi tiết và tổng tiền
$order_query = "
    SELECT 
        dh.ma_don_hang, 
        dh.ngay_dat, 
        dh.tinh_trang_giao_hang, 
        dh.tinh_trang_thanh_toan,
        GROUP_CONCAT(CONCAT(ctdh.SKU_phien_ban, ' (x', ctdh.soluong, ')') SEPARATOR ', ') AS san_pham,
        SUM(ctdh.thanh_tien) AS tong_tien
    FROM don_hang dh
    LEFT JOIN chi_tiet_don_hang ctdh ON dh.ma_don_hang = ctdh.ma_don_hang
    WHERE dh.ma_khach_hang = ?
    GROUP BY dh.ma_don_hang";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param('i', $user_info['ma_khach_hang']);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$orders = [];
while ($order = $order_result->fetch_assoc()) {
    $orders[] = $order;
}
$order_stmt->close();


// Lấy danh sách địa chỉ
$addr_query = "SELECT * FROM dia_chi WHERE ma_khach_hang = ?";
$addr_stmt = $conn->prepare($addr_query);
$addr_stmt->bind_param('i', $user_info['ma_khach_hang']);
$addr_stmt->execute();
$addr_result = $addr_stmt->get_result();
$addresses = [];
while ($ad = $addr_result->fetch_assoc()) {
    $addresses[] = $ad;
}
$addr_stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Tài Khoản</title>
    <link rel="stylesheet" href="../Css/account.css">
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/footer.css">
</head>
<body>
<?php include '../Partials/header.php'; ?>
<!-- Thêm form action về process_order.php -->

<div class="account-container">
    <div class="sidebar">
    <div class="user-info">
    <h2 class="greeting">Xin Chào</h2>
    <h2 class="customer-name"><?php echo htmlspecialchars($user_info['ten_khach_hang']); ?></h2>
</div>

        <ul class="menu">
            <li><a href="javascript:void(0);" onclick="showSection('personal-info')">Thông tin cá nhân</a></li>
            <?php if (!$is_facebook_user): ?>
                <li><a href="javascript:void(0);" onclick="showSection('security')">Bảo mật</a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0);" onclick="showSection('orders')">Đơn hàng</a></li>
            <li><a href="javascript:void(0);" onclick="showSection('addresses')">Địa chỉ</a></li>
        </ul>
    </div>
    <div class="content">
  <!-- Thông tin cá nhân -->
<div id="personal-info" class="section">
    <h3>Thông tin cá nhân</h3>
    <p><strong>Tên khách hàng:</strong> <span id="display-ten-khach-hang"><?php echo htmlspecialchars($user_info['ten_khach_hang']); ?></span></p>
    <p><strong>Email:</strong> <span id="display-email"><?php echo htmlspecialchars($user_info['mail']); ?></span></p>
    <p id="phone-number"><strong>Số điện thoại:</strong> <span id="display-so-dien-thoai"><?php echo htmlspecialchars($user_info['so_dien_thoai']); ?></span></p>

    <?php if (!$is_facebook_user): ?>
        <button id="edit-info-btn" onclick="toggleEditForm()">Cập nhật thông tin</button>

        <!-- Form thay đổi thông tin cá nhân -->
        <div id="edit-info-form" style="display:none;">
            <form id="update-info-form" method="POST">
                <label for="ten_khach_hang">Tên khách hàng:</label><br>
                <input type="text" id="ten_khach_hang" name="ten_khach_hang" value="<?php echo htmlspecialchars($user_info['ten_khach_hang']); ?>" required><br><br>
                
                <label for="so_dien_thoai">Số điện thoại:</label><br>
                <input type="text" id="so_dien_thoai" name="so_dien_thoai" value="<?php echo htmlspecialchars($user_info['so_dien_thoai']); ?>" required><br><br>
                
                <label for="new_email">Email:</label><br>
                <input type="email" id="new_email" name="new_email" value="<?php echo htmlspecialchars($user_info['mail']); ?>" required><br><br>

                <button type="submit">Cập nhật thông tin</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- Bảo mật -->
<div id="security" class="section">
    <h3>Bảo mật</h3>


    <div class="security-card">
        <h4>Thay đổi mật khẩu</h4>
        <form id="change-password-form" method="POST" action="../BE/change_password.php">
            <label for="current_password">Mật khẩu cũ:</label><br>
            <input type="password" id="current_password" name="current_password" required><br><br>

            <label for="new_password">Mật khẩu mới:</label><br>
            <input type="password" id="new_password" name="new_password" required><br><br>

            <label for="confirm_password">Xác nhận mật khẩu mới:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit" name="change_password" >Thay đổi mật khẩu</button>
        </form>
    </div>
</div>

        <!-- Đơn hàng -->
    <div id="orders" class="section" style="display:none;">
        <h3>Đơn hàng của bạn</h3>
        <?php if (count($orders) > 0): ?>
            <ul>
                <?php foreach ($orders as $order): ?>
                    <li>
                        <strong>Đơn hàng <?php echo htmlspecialchars($order['ma_don_hang']); ?></strong><br>
                        Ngày đặt: <?php echo htmlspecialchars($order['ngay_dat']); ?><br>
                        Trạng thái giao hàng: <?php echo htmlspecialchars($order['tinh_trang_giao_hang']); ?><br>
                        Trạng thái thanh toán: <?php echo htmlspecialchars($order['tinh_trang_thanh_toan']); ?><br>
                        Sản phẩm: <?php echo htmlspecialchars($order['san_pham']); ?><br>
                        Tổng tiền: <?php echo number_format($order['tong_tien'], 2); ?> VND
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Chưa có đơn hàng nào.</p>
        <?php endif; ?>
    </div>


        <!-- Địa chỉ -->
        
        <div id="addresses" class="section" style="display:none;">
            <h3>Địa chỉ của bạn</h3>
            <?php if (!empty($addresses)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tên Địa Chỉ</th>
                            <th>SĐT Giao Hàng</th>
                            <th>Địa Chỉ Chi Tiết</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($addresses as $ad):
                        $full_addr = $ad['so_nha'].' '.$ad['duong_pho'].', '.$ad['xa'].', '.$ad['huyen'].', '.$ad['thanh_pho'].', '.$ad['quoc_gia'];
                        // Chuyển dữ liệu address sang JSON
                        $ad_json = json_encode($ad, JSON_HEX_APOS|JSON_HEX_QUOT);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ad['ten_dia_chi']); ?></td>
                            <td><?php echo htmlspecialchars($ad['so_dien_thoai_giao_hang']); ?></td>
                            <td><?php echo htmlspecialchars($full_addr); ?></td>
                            <td>
                                <!-- Chỉnh sửa địa chỉ truyền ad_json vào hàm showEditForm -->
                                <button class="action-btn" type="button" onclick='showEditForm(<?php echo $ad_json; ?>)'>Sửa</button>

                                <button class="action-btn" onclick="deleteAddress(<?php echo $ad['ma_dia_chi']; ?>)">Xóa</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Chưa có địa chỉ nào, vui lòng thêm địa chỉ mới.</p>
            <?php endif; ?>
            <form action="../BE/process_diachi.php" method="POST" class="checkout-form">
            <button type="button" onclick="showAddAddressForm()">Thêm Địa Chỉ Mới</button>
            <div id="add_address_form" style="display:none;">
                <h3>Thêm Địa Chỉ Mới</h3>
                <label for="address_search">Tìm kiếm địa chỉ:</label>
                <input type="text" id="address_search" name="address_search" placeholder="Nhập địa chỉ...">

                <ul id="address_suggestions"></ul>
                
                <label>Số Nhà:</label><input type="text" name="so_nha"><br>
                <label>Đường Phố:</label><input type="text" name="duong_pho"><br>
                <label>Quốc Gia:</label><input type="text" name="quoc_gia" value="Vietnam"><br>
                <label>Thành Phố:</label><input type="text" name="thanh_pho"><br>
                <label>Quận/Huyện:</label><input type="text" name="huyen"><br>
                <label>Xã/Phường:</label><input type="text" name="xa"><br>
                <label>SĐT Giao Hàng:</label><input type="text" name="so_dien_thoai_giao_hang"><br>
                <label>Tên Địa Chỉ (VD: Nhà, Cơ Quan):</label><input type="text" name="ten_dia_chi"><br>
                <button type="submit" name="them_dia_chi">Lưu Địa Chỉ</button>
            </div>
            </form>
            <!-- Form sửa địa chỉ ẩn -->
            <div id="edit_address_form" style="display:none;">
                <h3>Sửa Địa Chỉ</h3>
                <form id="update-address-form">
                <input type="hidden" name="ma_dia_chi" id="edit_ma_dia_chi">
                <label>Số Nhà:</label><input type="text" name="so_nha" id="edit_so_nha"><br>
                <label>Đường Phố:</label><input type="text" name="duong_pho" id="edit_duong_pho"><br>
                <label>Quốc Gia:</label><input type="text" name="quoc_gia" id="edit_quoc_gia" value="Vietnam"><br>
                <label>Thành Phố:</label><input type="text" name="thanh_pho" id="edit_thanh_pho"><br>
                <label>Quận/Huyện:</label><input type="text" name="huyen" id="edit_huyen"><br>
                <label>Xã/Phường:</label><input type="text" name="xa" id="edit_xa"><br>
                <label>SĐT Giao Hàng:</label><input type="text" name="so_dien_thoai_giao_hang" id="edit_so_dien_thoai_giao_hang"><br>
                <label>Tên Địa Chỉ:</label><input type="text" name="ten_dia_chi" id="edit_ten_dia_chi"><br>
                <button type="submit" name="update_dia_chi">Cập Nhật Địa Chỉ</button>
                </form>
            </div>
        </div>
    </div>
</div>
</form>

<?php if (isset($_SESSION['message'])): ?>
    <div id="notification" class="notification <?php echo htmlspecialchars($_SESSION['message_type']); ?>">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
    </div>
    <?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
<?php endif; ?>
<script>
document.getElementById('change-password-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn tải lại trang

    const formData = new FormData(this);

    fetch('../BE/change_password.php', { // Đảm bảo đường dẫn chính xác
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success'); // Hiển thị thông báo thành công
            this.reset(); // Đặt lại form
        } else {
            showNotification(data.message, 'error'); // Hiển thị thông báo lỗi
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showNotification('Có lỗi xảy ra, vui lòng thử lại.', 'error'); // Thông báo lỗi chung
    });
});

</script>

<script>
// Hàm hiển thị thông báo
function showNotification(message, type) {
    const existingNotification = document.querySelector(".notification");
    if (existingNotification) existingNotification.remove(); // Xóa thông báo cũ (nếu có)

    const notification = document.createElement("div");
    notification.className = `notification ${type}`; // Thêm class tương ứng với type (success/error)
    notification.textContent = message;

    document.body.appendChild(notification);

    // Tự động xóa thông báo sau 3 giây
    setTimeout(() => {
        if (notification.parentNode) notification.remove();
    }, 3000);
}

// Hàm submit form cập nhật
document.getElementById('update-info-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Ngăn form submit mặc định

    const formData = new FormData(this);
    fetch('../BE/edit_profile.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Thông tin cập nhật thành công!", "success");
                // Cập nhật giao diện sau khi thành công
                document.getElementById('display-ten-khach-hang').textContent = formData.get('ten_khach_hang');
                document.getElementById('display-so-dien-thoai').textContent = formData.get('so_dien_thoai');
                document.getElementById('display-email').textContent = formData.get('new_email');
            } else {
                showNotification("Cập nhật thất bại: " + data.message, "error");
            }
             // Đóng form chỉnh sửa
             document.getElementById('edit-info-form').style.display = 'none';
        })
        .catch(error => {
            console.error("Error:", error);
            showNotification("Có lỗi xảy ra. Vui lòng thử lại.", "error");
        });
});

function showSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(function(section) {
        section.style.display = 'none';
    });
    const activeSection = document.getElementById(sectionId);
    if (activeSection) {
        activeSection.style.display = 'block';
    }
}

function editInfo() {
    var form = document.getElementById("edit-info-form");
    var btn = document.getElementById("edit-info-btn");
    
    // Hiển thị form và ẩn nút
    form.style.display = "block";
    btn.style.display = "none";
}
 
// Hàm hiển thị thông tin cá nhân
function showPersonalInfoSection() {
    activeSection = 'personal-info';
    document.getElementById('personal-info').style.display = 'block';
    document.getElementById('security').style.display = 'none';
}

// Hàm hiển thị phần bảo mật
function showSecuritySection() {
    activeSection = 'security';
    document.getElementById('personal-info').style.display = 'none';
    document.getElementById('security').style.display = 'block';
}

// Hàm hiển thị/ẩn form chỉnh sửa
function toggleEditForm() {
    const form = document.getElementById('edit-info-form');
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}

// Giữ trạng thái khi tải lại trang
window.onload = function () {
    // Dựa vào trạng thái để hiển thị phần đúng
    if (activeSection === 'personal-info') {
        showPersonalInfoSection();
    } else if (activeSection === 'security') {
        showSecuritySection();
    }
};

// Lưu trạng thái vào sessionStorage để giữ trạng thái khi tải lại trang
window.onbeforeunload = function () {
    sessionStorage.setItem('activeSection', activeSection);
};

// Lấy trạng thái từ sessionStorage khi trang được tải
window.onload = function () {
    const storedSection = sessionStorage.getItem('activeSection');
    if (storedSection === 'security') {
        showSecuritySection();
    } else {
        showPersonalInfoSection();
    }
};
function showAddAddressForm() {
    document.getElementById('add_address_form').style.display = 'block';
}

// Chức năng sửa địa chỉ
function showEditForm(ad) {
    document.getElementById('edit_ma_dia_chi').value = ad.ma_dia_chi;
    document.getElementById('edit_so_nha').value = ad.so_nha || '';
    document.getElementById('edit_duong_pho').value = ad.duong_pho || '';
    document.getElementById('edit_quoc_gia').value = ad.quoc_gia || 'Vietnam';
    document.getElementById('edit_thanh_pho').value = ad.thanh_pho || '';
    document.getElementById('edit_huyen').value = ad.huyen || '';
    document.getElementById('edit_xa').value = ad.xa || '';
    document.getElementById('edit_so_dien_thoai_giao_hang').value = ad.so_dien_thoai_giao_hang || '';
    document.getElementById('edit_ten_dia_chi').value = ad.ten_dia_chi || '';

    document.getElementById('edit_address_form').style.display = 'block';
}
document.getElementById('update-address-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Ngăn form submit mặc định

    const formData = new FormData(this);

    fetch('../BE/update_address.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success'); // Hiển thị thông báo thành công
                setTimeout(() => {
                    location.reload();
                }, 3000);
                
                // Ẩn form sửa địa chỉ
                document.getElementById('edit_address_form').style.display = 'none';

                // Cập nhật giao diện nếu cần
                const addressRow = document.querySelector(`tr[data-id="${formData.get('ma_dia_chi')}"]`);
                if (addressRow) {
                    addressRow.querySelector('.address-name').textContent = formData.get('ten_dia_chi');
                    addressRow.querySelector('.address-phone').textContent = formData.get('so_dien_thoai_giao_hang');
                    addressRow.querySelector('.address-detail').textContent =
                        `${formData.get('so_nha')} ${formData.get('duong_pho')}, ${formData.get('xa')}, ${formData.get('huyen')}, ${formData.get('thanh_pho')}, ${formData.get('quoc_gia')}`;
                }
            } else {
                showNotification(data.message, 'error'); // Hiển thị thông báo lỗi
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            showNotification('Có lỗi xảy ra, vui lòng thử lại.', 'error');
        });
});

function editAddress(ma_dia_chi) {
    // Hàm cũ alert đã bỏ đi
    // Giờ chúng ta không cần hàm này nữa,
    // Đã chuyển sang showEditForm ngay trong onclick.

    // Nếu vẫn muốn có hàm, có thể xóa hàm này hoặc để trống.
}

function deleteAddress(ma_dia_chi) {
    if (confirm("Bạn có chắc chắn muốn xóa địa chỉ này?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../BE/delete_addres.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText == "success") {
                    alert("Địa chỉ đã được xóa!");
                    // Xóa địa chỉ khỏi DOM mà không cần reload lại trang
                    document.getElementById("address-" + ma_dia_chi).remove(); 
                } else {
                    alert("Xóa địa chỉ thất bại. Vui lòng thử lại.");
                }
            }
        };
        // Gửi mã địa chỉ để xóa
        xhr.send("ma_dia_chi=" + ma_dia_chi);
    }
}


// API Tìm địa chỉ
const addressSearch = document.getElementById('address_search');
const suggestionsBox = document.getElementById('address_suggestions');

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
    if (predictions.length === 0) return;

    predictions.forEach(prediction => {
        const listItem = document.createElement('li');
        let cleanedDisplayName = prediction.display_name.replace(/\b\d{4,6}\b/g, '');
        cleanedDisplayName = cleanedDisplayName.replace(/,\s*,/g, ', ').trim();

        listItem.textContent = cleanedDisplayName;
        listItem.addEventListener('click', function() {
            addressSearch.value = cleanedDisplayName;
            suggestionsBox.innerHTML = '';

            // Điền thông tin vào các trường form thêm địa chỉ
            const form = document.getElementById('add_address_form');
            const address = prediction.address;
            form.querySelector('input[name="so_nha"]').value = address.house_number || '';
            form.querySelector('input[name="duong_pho"]').value = address.road || address.street || address.residential || '';
            form.querySelector('input[name="quoc_gia"]').value = address.country || 'Vietnam';
            form.querySelector('input[name="thanh_pho"]').value = address.city || address.town || address.state || '';
            form.querySelector('input[name="huyen"]').value = address.suburb || '';
            form.querySelector('input[name="xa"]').value = address.quarter || '';
        });
        suggestionsBox.appendChild(listItem);
    });
}

document.addEventListener('click', function(e) {
    if (e.target !== addressSearch) {
        suggestionsBox.innerHTML = '';
    }
});
</script>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
