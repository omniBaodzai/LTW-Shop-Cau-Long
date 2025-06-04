<?php
// Thông tin kết nối
$servername = "localhost";  // thường là localhost nếu bạn chạy local
$username = "root";         // tên user MySQL của bạn
$password = "";             // mật khẩu user MySQL (nếu có)
$dbname = "caulongvn";      // tên database bạn vừa tạo

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nếu kết nối thành công, bạn có thể chạy các truy vấn sau
// Ví dụ:
// $sql = "SELECT * FROM banner";
// $result = $conn->query($sql);

?>
