
<?php
include '../includes/header.php';
include '../connect.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập hay chưa
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($user_id > 0) {
    // Truy vấn thông tin người dùng từ bảng `users`
    $stmt = $conn->prepare("SELECT name, email, phone, address, city, district, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_name = htmlspecialchars($user['name']);
        $user_email = htmlspecialchars($user['email']);
        $user_phone = htmlspecialchars($user['phone']);
        $user_address = htmlspecialchars($user['address']);
        $user_city = htmlspecialchars($user['city']);
        $user_district = htmlspecialchars($user['district']);
        $user_created_at = htmlspecialchars($user['created_at']);
    } else {
        echo "Không tìm thấy thông tin người dùng.";
    }

    $stmt->close();
} else {
    echo "Bạn chưa đăng nhập.";
    exit();
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $fullname = htmlspecialchars(trim($_POST['fullname']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));
        $city = htmlspecialchars(trim($_POST['city']));
        $district = htmlspecialchars(trim($_POST['district']));

        if (!empty($fullname) && !empty($phone) && !empty($address) && !empty($city) && !empty($district)) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, city = ?, district = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $fullname, $phone, $address, $city, $district, $user_id);

            if ($stmt->execute()) {
                echo "<p class='success-msg'>Thông tin đã được cập nhật.</p>";
            } else {
                echo "<p class='error-msg'>Có lỗi xảy ra khi cập nhật thông tin.</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='error-msg'>Vui lòng điền đầy đủ thông tin.</p>";
        }
    }

    // Xử lý đổi mật khẩu
    if ($_POST['action'] === 'change_password') {
        $old_password = htmlspecialchars(trim($_POST['old-password']));
        $new_password = htmlspecialchars(trim($_POST['new-password']));
        $confirm_password = htmlspecialchars(trim($_POST['confirm-password']));

        if ($new_password === $confirm_password) {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($old_password, $user['password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt_update->bind_param("si", $hashed_password, $user_id);

                    if ($stmt_update->execute()) {
                        echo "<p class='success-msg'>Mật khẩu đã được thay đổi.</p>";
                    } else {
                        echo "<p class='error-msg'>Có lỗi xảy ra khi đổi mật khẩu.</p>";
                    }

                    $stmt_update->close();
                } else {
                    echo "<p class='error-msg'>Mật khẩu hiện tại không đúng.</p>";
                }
            } else {
                echo "<p class='error-msg'>Không tìm thấy thông tin người dùng.</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='error-msg'>Mật khẩu mới không khớp.</p>";
        }
    }
}

$conn->close();
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
  /* Khung chính */
  .member-container {
    display: flex;
    gap: 16px;
  }

  /* Sidebar Tabs */
  .member-sidebar {
    flex: 0 0 250px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 16px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .member-tabs {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .tab-btn {
    padding: 12px;
    border: none;
    background-color: #fff;
    border-radius: 4px;
    cursor: pointer;
    text-align: left;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s, color 0.3s;
    color: #333; /* Màu chữ tối */
  }

  .tab-btn.active {
    background-color: #e0e7ff; /* Nền sáng */
    color: #333; /* Màu chữ tối */
    font-weight: bold;
  }

  /* Nội dung Tabs */
  .tab-content {
    flex: 1;
    padding: 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    color: #333; /* Màu chữ tối */
  }

  /* Thẻ tiêu đề */
  h2 {
    font-size: 20px;
    margin-bottom: 16px;
    color: #333;
  }

  /* Avatar */
  .member-avatar-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
  }

  .member-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
  }

  .avatar-edit label {
    cursor: pointer;
    color: #4caf50;
    font-size: 24px;
  }

  /* Chi tiết thành viên */
  .member-detail-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .member-detail {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    color: #333;
  }

  .member-detail i {
    color: #4caf50;
  }

  .edit-group {
    margin-bottom: 16px;
  }

  .edit-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
  }

  .edit-group input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box;
  }

  .success-msg {
    color: #4caf50;
    font-size: 14px;
    margin-bottom: 16px;
  }

  .error-msg {
    color: #e53935;
    font-size: 14px;
    margin-bottom: 16px;
  }
</style>

<main class="member-main">
  <!-- Khung chứa Tabs và Nội dung -->
  <div class="member-container">
    <!-- Tabs -->
    <aside class="member-sidebar">
      <nav class="member-tabs">
        <button class="tab-btn active" onclick="showTab('profile')">
          <i class="ri-user-line"></i> Thông tin thành viên
        </button>
        <button class="tab-btn" onclick="showTab('edit')">
          <i class="ri-edit-line"></i> Chỉnh sửa thông tin
        </button>
        <button class="tab-btn" onclick="showTab('change-password')">
          <i class="ri-lock-password-line"></i> Đổi mật khẩu
        </button>
      </nav>
    </aside>

    <!-- Tab Content -->
    <section class="tab-content">
      <!-- Thông tin thành viên -->
      <div class="tab-pane active" id="profile">
        <section class="member-card">
          <div class="member-avatar-wrap">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Avatar" class="member-avatar" />
            <div class="avatar-edit">
              <label for="avatar-upload" title="Đổi ảnh đại diện">
                <i class="ri-camera-line"></i>
              </label>
              <input type="file" id="avatar-upload" style="display: none" />
            </div>
          </div>
          <div class="member-info">
            <h2 class="member-name"><?= $user_name ?></h2>
            <div class="member-detail-list">
              <div class="member-detail">
                <i class="ri-mail-line"></i> Email:
                <span><?= $user_email ?></span>
              </div>
              <div class="member-detail">
                <i class="ri-phone-line"></i> SĐT: <span><?= $user_phone ?></span>
              </div>
              <div class="member-detail">
                <i class="ri-calendar-line"></i> Ngày tham gia:
                <span><?= $user_created_at ?></span>
              </div>
              <div class="member-detail">
                <i class="ri-map-pin-line"></i> Địa chỉ:
                <span><?= $user_address ?>, <?= $user_district ?>, <?= $user_city ?></span>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Chỉnh sửa thông tin -->
      <div class="tab-pane" id="edit">
        <section class="edit-card">
          <form class="edit-form" method="POST" autocomplete="off">
            <h2 class="edit-title"><i class="ri-edit-line"></i> Chỉnh sửa thông tin</h2>
            <div class="edit-group">
              <label for="fullname">
                <i class="ri-user-3-line"></i> Họ và tên
              </label>
              <input type="text" id="fullname" name="fullname" value="<?= $user_name ?>" required>
            </div>
            <div class="edit-group">
              <label for="phone">
                <i class="ri-phone-line"></i> Số điện thoại
              </label>
              <input type="tel" id="phone" name="phone" value="<?= $user_phone ?>" required>
            </div>
            <div class="edit-group">
              <label for="address">
                <i class="ri-map-pin-line"></i> Địa chỉ
              </label>
              <input type="text" id="address" name="address" value="<?= $user_address ?>" required>
            </div>
            <div class="edit-group">
              <label for="city">
                <i class="ri-building-line"></i> Thành phố
              </label>
              <input type="text" id="city" name="city" value="<?= $user_city ?>" required>
            </div>
            <div class="edit-group">
              <label for="district">
                <i class="ri-community-line"></i> Quận/Huyện
              </label>
              <input type="text" id="district" name="district" value="<?= $user_district ?>" required>
            </div>
            <div class="edit-actions-row">
              <button type="submit" class="edit-btn save" name="action" value="update_profile">
                <i class="ri-save-3-line"></i> Lưu thay đổi
              </button>
              <button type="button" class="edit-btn cancel" id="btn-cancel-edit">
                <i class="ri-arrow-go-back-line"></i> Hủy
              </button>
            </div>
          </form>
        </section>
      </div>

      <!-- Đổi mật khẩu -->
      <div class="tab-pane" id="change-password">
        <section class="change-password-card">
          <form class="change-password-form" method="POST" autocomplete="off">
            <h2 class="change-password-title">
              <i class="ri-lock-password-line"></i> Đổi mật khẩu
            </h2>
            <div class="change-password-group">
              <label for="old-password">
                <i class="ri-lock-2-line"></i> Mật khẩu hiện tại
              </label>
              <input type="password" id="old-password" name="old-password" required>
            </div>
            <div class="change-password-group">
              <label for="new-password">
                <i class="ri-key-2-line"></i> Mật khẩu mới
              </label>
              <input type="password" id="new-password" name="new-password" required>
            </div>
            <div class="change-password-group">
              <label for="confirm-password">
                <i class="ri-key-line"></i> Xác nhận mật khẩu mới
              </label>
              <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <div class="change-password-actions">
              <button type="submit" class="change-btn save" name="action" value="change_password">
                <i class="ri-save-3-line"></i> Lưu thay đổi
              </button>
              <button type="button" class="change-btn cancel" id="btn-cancel-change-password">
                <i class="ri-arrow-go-back-line"></i> Hủy
              </button>
            </div>
          </form>
        </section>
      </div>
    </section>
  </div>
</main>

<script>
  function showTab(tabId) {
    const tabs = document.querySelectorAll('.tab-pane');
    tabs.forEach(tab => tab.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(button => button.classList.remove('active'));
    document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
  }
</script>

<?php include '../includes/footer.php'; ?>