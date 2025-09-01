<?php
// cart.php
header('Content-Type: text/plain');  // Hoặc có thể là 'text/html' tùy theo yêu cầu
$response = "Sản phẩm đã được thêm vào giỏ hàng!";

// Hoặc có thể gửi thông báo lỗi nếu có sự cố
// $response = "Có lỗi xảy ra: ...";

echo $response;  // Trả về văn bản hoặc HTML




// Bắt đầu session
session_start();

// Thiết lập header JSON
header('Content-Type: application/json');

// Hàm để trả về lỗi dưới dạng JSON và dừng script
function return_error($message) {
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit();
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    return_error('Bạn chưa đăng nhập.');
}

// Lấy username từ session
$username = $_SESSION['username'];

// Đọc dữ liệu JSON từ yêu cầu POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Kiểm tra dữ liệu hợp lệ
if (!$data || !isset($data['sku_phien_ban']) || !isset($data['quantity'])) {
    return_error('Dữ liệu không hợp lệ.');
}

// Lấy và làm sạch dữ liệu
$sku_phien_ban = htmlspecialchars($data['sku_phien_ban']);
$quantity = intval($data['quantity']);

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "furniture_store");

// Kiểm tra kết nối
if ($conn->connect_error) {
    return_error('Không thể kết nối đến cơ sở dữ liệu: ' . $conn->connect_error);
}

// Đặt charset cho kết nối để hỗ trợ Unicode
$conn->set_charset("utf8");

// Lấy user_id dựa trên username
$stmt = $conn->prepare("SELECT user_id FROM account WHERE username = ?");
if (!$stmt) {
    return_error('Lỗi cơ sở dữ liệu: ' . $conn->error);
}
$stmt->bind_param("s", $username);
if (!$stmt->execute()) {
    return_error('Lỗi thực thi câu lệnh SQL: ' . $stmt->error);
}
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    return_error('Người dùng không tồn tại.');
}
$row = $result->fetch_assoc();
$user_id = $row['user_id'];
$stmt->close();

// Kiểm tra xem phiên bản sản phẩm có tồn tại không
$stmt = $conn->prepare("SELECT SKU_phien_ban FROM phien_ban_san_pham WHERE SKU_phien_ban = ?");
if (!$stmt) {
    return_error('Lỗi cơ sở dữ liệu: ' . $conn->error);
}
$stmt->bind_param("s", $sku_phien_ban);
if (!$stmt->execute()) {
    return_error('Lỗi thực thi câu lệnh SQL: ' . $stmt->error);
}
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    return_error('Sản phẩm không tồn tại.');
}
$stmt->close();

// Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
$stmt = $conn->prepare("SELECT id_gio_hang, quantity FROM giohang WHERE user_id = ? AND SKU_phien_ban = ?");
if (!$stmt) {
    return_error('Lỗi cơ sở dữ liệu: ' . $conn->error);
}
$stmt->bind_param("is", $user_id, $sku_phien_ban);
if (!$stmt->execute()) {
    return_error('Lỗi thực thi câu lệnh SQL: ' . $stmt->error);
}
$existing = $stmt->get_result();
if ($existing->num_rows > 0) {
    // Nếu đã tồn tại, cập nhật số lượng
    $row = $existing->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;

    $update_stmt = $conn->prepare("UPDATE giohang SET quantity = ? WHERE id_gio_hang = ?");
    if (!$update_stmt) {
        return_error('Lỗi cơ sở dữ liệu: ' . $conn->error);
    }
    $update_stmt->bind_param("ii", $new_quantity, $row['id_gio_hang']);
    if ($update_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật giỏ hàng thành công.']);
    } else {
        return_error('Không thể cập nhật giỏ hàng: ' . $update_stmt->error);
    }
    $update_stmt->close();
} else {
    // Nếu chưa tồn tại, thêm sản phẩm mới vào giỏ hàng
    $stmt = $conn->prepare("INSERT INTO giohang (user_id, SKU_phien_ban, quantity) VALUES (?, ?, ?)");
    if (!$stmt) {
        return_error('Lỗi cơ sở dữ liệu: ' . $conn->error);
    }

    $stmt->bind_param("isi", $user_id, $sku_phien_ban, $quantity);

    if (!$stmt->execute()) {
        return_error('Không thể thêm sản phẩm vào giỏ hàng: ' . $stmt->error);
    }

    $stmt->close();
}

$conn->close();

?>
