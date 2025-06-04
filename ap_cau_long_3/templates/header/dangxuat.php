<?php
session_start();

// Hủy tất cả session để đăng xuất
$_SESSION = [];
session_destroy();

// Trả về status 200 để báo đăng xuất thành công
http_response_code(200);
exit;
