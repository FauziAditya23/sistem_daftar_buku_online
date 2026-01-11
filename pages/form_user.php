<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Prevent caching
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$user_level = $_SESSION['user_level'] ?? 'Operator';
$nama_lengkap_session = $_SESSION['nama_lengkap'] ?? 'User';

// Only Administrator can access this page
if ($user_level !== 'Administrator') {
    echo '<p>Akses ditolak. Halaman ini hanya untuk Administrator.</p>';
    exit;
}

require_once __DIR__ . '/../includes/config.php';

// Determine if this is add or edit mode
$is_edit = isset($_GET['id']) && !empty($_GET['id']);
$user_data = null;

if ($is_edit) {
    $user_id = (int)$_GET['id'];
    try {
        $conn = db_connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_user WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_close($conn);
        if (!$user_data) {
            header('Location: index.php?page=kelola_user&error=User tidak ditemukan');
            exit;
        }
    } catch (Exception $e) {
        header('Location: index.php?page=kelola_user&error=Gagal memuat data user');
        exit;
    }
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = db_connect(); // Buka koneksi di awal proses POST
    try {
        // Handle file upload
        $user_foto = '';
        if (isset($_FILES['user_foto']) && $_FILES['user_foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['user_foto'];
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Format file tidak didukung.');
            }
            if ($file['size'] > 2 * 1024 * 1024) {
                throw new Exception('Ukuran file terlalu besar. Maksimal 2MB.');
            }
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'user_' . uniqid() . '.' . $file_extension;
            $upload_path = __DIR__ . '/../uploads/user_photos/' . $new_filename;
            
            $upload_dir = dirname($upload_path);
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $user_foto = 'uploads/user_photos/' . $new_filename;
            } else {
                throw new Exception('Gagal mengupload foto.');
            }
        }

        if ($is_edit) {
            $user_id = (int)$_POST['user_id'];
            $username = trim($_POST['username'] ?? '');
            $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
            $user_email = trim($_POST['user_email'] ?? '');
            $user_level_input = trim($_POST['user_level'] ?? 'Operator');
            $change_password = isset($_POST['change_password']);
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($nama_lengkap) || empty($user_email)) {
                throw new Exception('Username, nama lengkap, dan email harus diisi.');
            }

            // Check username duplikat
            $stmt = mysqli_prepare($conn, "SELECT user_id FROM tbl_user WHERE username = ? AND user_id != ?");
            mysqli_stmt_bind_param($stmt, 'si', $username, $user_id);
            mysqli_stmt_execute($stmt);
            if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
                throw new Exception('Username sudah digunakan.');
            }
            // PERBAIKAN: Menambahkan penutup } yang hilang di sini

            // Check email duplikat
            $stmt = mysqli_prepare($conn, "SELECT user_id FROM tbl_user WHERE user_email = ? AND user_id != ?");
            mysqli_stmt_bind_param($stmt, 'si', $user_email, $user_id);
            mysqli_stmt_execute($stmt);
            if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
                throw new Exception('Email sudah digunakan.');
            }

            $photo_update_sql = '';
            $params = [$username, $nama_lengkap, $user_email, $user_level_input];
            $types = 'ssss';

            if (!empty($user_foto)) {
                if (!empty($user_data['user_foto']) && file_exists(__DIR__ . '/../' . $user_data['user_foto'])) {
                    unlink(__DIR__ . '/../' . $user_data['user_foto']);
                }
                $photo_update_sql = ', user_foto = ?';
                $params[] = $user_foto;
                $types .= 's';
            }

            if ($change_password && !empty($password)) {
                if (strlen($password) < 6) throw new Exception('Password minimal 6 karakter.');
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE tbl_user SET username = ?, nama_lengkap = ?, user_email = ?, user_level = ?, password = ?" . $photo_update_sql . " WHERE user_id = ?");
                $params_final = array_merge([$username, $nama_lengkap, $user_email, $user_level_input, $hashed_password], (!empty($user_foto) ? [$user_foto] : []), [$user_id]);
                $types_final = 'sssss' . (!empty($user_foto) ? 's' : '') . 'i';
                mysqli_stmt_bind_param($stmt, $types_final, ...$params_final);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE tbl_user SET username = ?, nama_lengkap = ?, user_email = ?, user_level = ?" . $photo_update_sql . " WHERE user_id = ?");
                $params_final = array_merge($params, [$user_id]);
                $types_final = $types . 'i';
                mysqli_stmt_bind_param($stmt, $types_final, ...$params_final);
            }
            mysqli_stmt_execute($stmt);
            $message = 'User berhasil diupdate.';
        } else {
            // Add new user (Logika sudah benar, tinggal pastikan variabel sesuai)
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
            $user_email = trim($_POST['user_email'] ?? '');
            $user_level_input = trim($_POST['user_level'] ?? 'Operator');

            if (empty($username) || empty($password) || empty($nama_lengkap) || empty($user_email)) throw new Exception('Semua field harus diisi.');
            
            $stmt = mysqli_prepare($conn, "SELECT user_id FROM tbl_user WHERE username = ?");
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) throw new Exception('Username sudah digunakan.');

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_user (username, password, nama_lengkap, user_email, user_level, user_foto) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssssss', $username, $hashed_password, $nama_lengkap, $user_email, $user_level_input, $user_foto);
            mysqli_stmt_execute($stmt);
            $message = 'User berhasil ditambahkan.';
        }
        
        mysqli_close($conn);
        header('Location: index.php?page=kelola_user&message=' . urlencode($message) . '&type=success');
        exit;
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
        if(isset($conn)) mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit' : 'Tambah'; ?> User</title>
    <link rel="stylesheet" href="assets/form_user.css">
</head>
<body>
<div class="form-container">
    <h2><?php echo $is_edit ? 'Edit' : 'Tambah'; ?> User</h2>
    <a href="index.php?page=kelola_user" class="btn btn-secondary" style="margin-bottom: 20px;">← Kembali</a>
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_data['user_id']); ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $is_edit ? htmlspecialchars($user_data['username']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo $is_edit ? htmlspecialchars($user_data['nama_lengkap']) : ''; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_email">Email:</label>
                <input type="email" id="user_email" name="user_email" value="<?php echo $is_edit ? htmlspecialchars($user_data['user_email']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="user_level">Level:</label>
                <select id="user_level" name="user_level">
                    <option value="Operator" <?php echo ($is_edit && $user_data['user_level'] === 'Operator') ? 'selected' : ''; ?>>Operator</option>
                    <option value="Administrator" <?php echo ($is_edit && $user_data['user_level'] === 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password <?php echo $is_edit ? 'Baru (Kosongkan jika tidak ubah)' : ''; ?>:</label>
            <input type="password" id="password" name="password" <?php echo !$is_edit ? 'required' : ''; ?>>
            <?php if ($is_edit): ?>
                <label><input type="checkbox" name="change_password"> Konfirmasi ubah password</label>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="user_foto">Foto Profil:</label>
            <input type="file" id="user_foto" name="user_foto" accept="image/*" onchange="previewImage(this)">
            <div id="image-preview" style="margin-top: 10px;">
                <img id="preview-img" src="<?php echo ($is_edit && !empty($user_data['user_foto'])) ? htmlspecialchars($user_data['user_foto']) : ''; ?>" 
                     style="max-width: 150px; display: <?php echo ($is_edit && !empty($user_data['user_foto'])) ? 'block' : 'none'; ?>;">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Update' : 'Tambah'; ?></button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>