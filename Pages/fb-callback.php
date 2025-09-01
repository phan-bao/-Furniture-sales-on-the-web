<?php
session_start();

// Include Facebook SDK
require_once '../facebook-sdk/src/Facebook/autoload.php'; // Đảm bảo đường dẫn đúng

// Cấu hình Facebook SDK
$fb = new \Facebook\Facebook([
  'app_id' => '2033776310473406', // Thay bằng App ID của bạn
  'app_secret' => '6dd992518f2da3918e293c2c8b2571ed', // Thay bằng App Secret của bạn
  'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();

// Kiểm tra xem có lỗi gì xảy ra khi nhận mã hay không
if (isset($_GET['state'])) {
    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
}

try {
    // Lấy token từ Facebook sau khi người dùng đăng nhập
    $accessToken = $helper->getAccessToken();

    if (!isset($accessToken)) {
        // Nếu không có token, chuyển hướng người dùng về trang login
        exit('Lỗi khi đăng nhập Facebook.');
    }

    // Lưu trữ token vào session để sử dụng sau này
    $_SESSION['facebook_access_token'] = (string) $accessToken;

    // Lấy thông tin người dùng
    $response = $fb->get('/me?fields=id,name,email', $accessToken);
    $user = $response->getGraphUser();

    // Lấy thông tin từ Facebook
    $facebook_id = $user['id'];
    $name = $user['name'];
    $email = isset($user['email']) ? $user['email'] : null;

    // Kết nối cơ sở dữ liệu
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "furniture_store";
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Kiểm tra xem người dùng đã tồn tại trong bảng 'khach_hang' chưa dựa trên 'facebook_id' hoặc 'email'
    $query = "SELECT * FROM khach_hang WHERE facebook_id = ? OR mail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $facebook_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Người dùng đã tồn tại, lấy thông tin
        $row = $result->fetch_assoc();

        // Cập nhật 'facebook_id' nếu chưa có
        if (empty($row['facebook_id'])) {
            $updateQuery = "UPDATE khach_hang SET facebook_id = ? WHERE ma_khach_hang = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('si', $facebook_id, $row['ma_khach_hang']);
            $updateStmt->execute();
            $updateStmt->close();
        }

        // Lấy thông tin tài khoản từ bảng 'account'
        $accountQuery = "SELECT * FROM account WHERE ma_khach_hang = ?";
        $accountStmt = $conn->prepare($accountQuery);
        $accountStmt->bind_param('i', $row['ma_khach_hang']);
        $accountStmt->execute();
        $accountResult = $accountStmt->get_result();

        if ($accountResult->num_rows > 0) {
            $accountRow = $accountResult->fetch_assoc();

            // Thiết lập các biến session giống như đăng nhập bình thường
            $_SESSION['ma_khach_hang'] = $row['ma_khach_hang'];
            $_SESSION['username'] = $accountRow['username']; // Sử dụng username từ bảng 'account'
            $_SESSION['ten_khach_hang'] = $row['ten_khach_hang'];
        } else {
            // Nếu không tìm thấy tài khoản tương ứng, tạo mới
            $username_account = $email ? $email : 'fbuser_' . $facebook_id;
            $random_password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

            $insertAccount = "INSERT INTO account (ma_khach_hang, username, password) VALUES (?, ?, ?)";
            $newAccountStmt = $conn->prepare($insertAccount);
            $newAccountStmt->bind_param('iss', $row['ma_khach_hang'], $username_account, $random_password);
            if ($newAccountStmt->execute()) {
                $_SESSION['username'] = $username_account;
            } else {
                echo "Lỗi khi tạo tài khoản: " . $newAccountStmt->error;
                exit();
            }
            $newAccountStmt->close();
        }
        $accountStmt->close();
    } else {
        // Người dùng chưa tồn tại, tạo mới trong bảng 'khach_hang' và 'account'
        $insertKhachHang = "INSERT INTO khach_hang (ten_khach_hang, mail, so_dien_thoai, facebook_id) VALUES (?, ?, '', ?)";
        $stmt = $conn->prepare($insertKhachHang);
        $stmt->bind_param('sss', $name, $email, $facebook_id);
        if ($stmt->execute()) {
            $last_id = $conn->insert_id;

            // Tạo tài khoản mới trong bảng 'account'
            $username_account = $email ? $email : 'fbuser_' . $facebook_id;
            $random_password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

            $insertAccount = "INSERT INTO account (ma_khach_hang, username, password) VALUES (?, ?, ?)";
            $newAccountStmt = $conn->prepare($insertAccount);
            $newAccountStmt->bind_param('iss', $last_id, $username_account, $random_password);
            if ($newAccountStmt->execute()) {
                // Thiết lập các biến session giống như đăng nhập bình thường
                $_SESSION['ma_khach_hang'] = $last_id;
                $_SESSION['username'] = $username_account;
                $_SESSION['ten_khach_hang'] = $name;
            } else {
                // Xử lý lỗi khi thêm vào bảng 'account'
                echo "Lỗi khi tạo tài khoản: " . $newAccountStmt->error;
                exit();
            }
            $newAccountStmt->close();
        } else {
            // Xử lý lỗi khi thêm vào bảng 'khach_hang'
            echo "Lỗi khi tạo người dùng: " . $stmt->error;
            exit();
        }
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();

    // Chuyển hướng người dùng về trang chính hoặc trang tài khoản
    header("Location: ../index.php");
    
    exit();

} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // Xử lý lỗi khi gọi API Facebook
    echo 'Lỗi từ Facebook Graph API: ' . $e->getMessage();
    exit();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // Xử lý lỗi SDK của Facebook
    echo 'Lỗi SDK Facebook: ' . $e->getMessage();
    exit();
}
?>
