<?php
// logout.php

// Bắt đầu session
session_start();

// Xóa tất cả các biến session
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng người dùng về trang đăng nhập
header("Location: loginadmin.php");
exit;
?>
