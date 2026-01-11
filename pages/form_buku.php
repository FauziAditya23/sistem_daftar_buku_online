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
$nama_lengkap = $_SESSION['nama_lengkap'] ?? 'User';

// --- PERBAIKAN 1: Menambahkan tutup kurung kurawal pada pengecekan level ---
if (!in_array($user_level, ['Administrator', 'Operator'])) {
    echo '<p>Akses ditolak. Halaman ini hanya untuk Administrator dan Operator.</p>';
    exit;
}
// --------------------------------------------------------------------------

require_once __DIR__ . '/../includes/config.php';

// Handle CRUD operations
$message = $_GET['message'] ?? '';
$message_type = $_GET['type'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = db_connect();
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            
            // Pengambilan data input
            $buku_isbn = trim($_POST['buku_isbn'] ?? '');
            $buku_judul = trim($_POST['buku_judul'] ?? '');
            $penerbit_id = trim($_POST['penerbit_id'] ?? '');
            $buku_tglterbit_raw = trim($_POST['buku_tglterbit'] ?? '');
            $buku_jmlhalaman = (int)($_POST['buku_jmlhalaman'] ?? 0);
            $buku_deskripsi = trim($_POST['buku_deskripsi'] ?? '');
            $buku_harga = (float)($_POST['buku_harga'] ?? 0);
            $kategori_id = trim($_POST['kategori_id'] ?? '');
            $pengarang_id = trim($_POST['pengarang_id'] ?? '');

            // Format Tanggal
            $buku_tglterbit = null;
            if (!empty($buku_tglterbit_raw)) {
                $date = date_create($buku_tglterbit_raw);
                if ($date) {
                    $buku_tglterbit = date_format($date, 'Y-m-d');
                }
            }

            if (empty($buku_isbn) || empty($buku_judul) || empty($penerbit_id) || empty($kategori_id) || empty($pengarang_id) || empty($buku_tglterbit)) {
                throw new Exception('Semua kolom wajib (ISBN, Judul, Penerbit, Kategori, Pengarang, Tanggal) harus diisi.');
            }

            if ($action === 'add') {
                // Check if ISBN exists
                $stmt = mysqli_prepare($conn, "SELECT buku_isbn FROM tbl_buku WHERE buku_isbn = ?");
                mysqli_stmt_bind_param($stmt, 's', $buku_isbn);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    throw new Exception('ISBN Buku sudah terdaftar.');
                }
                mysqli_stmt_close($stmt);

                // Insert
                $stmt = mysqli_prepare($conn, "INSERT INTO tbl_buku (buku_isbn, buku_judul, kategori_id, penerbit_id, pengarang_id, buku_tglterbit, buku_jmlhalaman, buku_deskripsi, buku_harga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'ssssssisd', $buku_isbn, $buku_judul, $kategori_id, $penerbit_id, $pengarang_id, $buku_tglterbit, $buku_jmlhalaman, $buku_deskripsi, $buku_harga);
                
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: index.php?page=kelola_buku&message=' . urlencode('Buku berhasil ditambahkan') . '&type=success');
                    exit;
                }
            } elseif ($action === 'edit') {
                $stmt = mysqli_prepare($conn, "UPDATE tbl_buku SET buku_judul = ?, kategori_id = ?, penerbit_id = ?, pengarang_id = ?, buku_tglterbit = ?, buku_jmlhalaman = ?, buku_deskripsi = ?, buku_harga = ? WHERE buku_isbn = ?");
                mysqli_stmt_bind_param($stmt, 'sssssisds', $buku_judul, $kategori_id, $penerbit_id, $pengarang_id, $buku_tglterbit, $buku_jmlhalaman, $buku_deskripsi, $buku_harga, $buku_isbn);
                
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: index.php?page=kelola_buku&message=' . urlencode('Buku berhasil diupdate') . '&type=success');
                    exit;
                }
            }
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
    }
}

// Get action & data for edit
$action = $_GET['action'] ?? 'add';
$edit_id = $_GET['id'] ?? '';
$edit_data = null;

$conn = db_connect();
if ($action === 'edit' && !empty($edit_id)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_buku WHERE buku_isbn = ?");
    mysqli_stmt_bind_param($stmt, 's', $edit_id);
    mysqli_stmt_execute($stmt);
    $edit_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

// Load dropdowns
$publishers = mysqli_query($conn, "SELECT * FROM tbl_penerbit ORDER BY penerbit_nama");
$categories = mysqli_query($conn, "SELECT * FROM tbl_kategori ORDER BY kategori_nama");
$authors = mysqli_query($conn, "SELECT * FROM tbl_pengarang ORDER BY pengarang_nama");
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($action === 'edit' ? 'Edit' : 'Tambah'); ?> Buku</title>
    <link rel="stylesheet" href="assets/form_buku.css">
</head>
<body>
<div class="form-container">
    <h2><?php echo ($action === 'edit' ? 'Edit' : 'Tambah'); ?> Buku</h2>
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" onsubmit="return validateForm()">
        <input type="hidden" name="action" value="<?php echo $action; ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label>ISBN:</label>
                <input type="text" name="buku_isbn" value="<?php echo htmlspecialchars($edit_data['buku_isbn'] ?? ''); ?>" <?php echo ($action === 'edit' ? 'readonly' : ''); ?> required maxlength="20">
            </div>
            <div class="form-group">
                <label>Judul Buku:</label>
                <input type="text" name="buku_judul" value="<?php echo htmlspecialchars($edit_data['buku_judul'] ?? ''); ?>" required maxlength="200">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Penerbit:</label>
                <select name="penerbit_id" required>
                    <option value="">Pilih Penerbit</option>
                    <?php while ($p = mysqli_fetch_assoc($publishers)): ?>
                        <option value="<?php echo $p['penerbit_id']; ?>" <?php echo (isset($edit_data) && $edit_data['penerbit_id'] == $p['penerbit_id'] ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($p['penerbit_nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal Terbit:</label>
                <input type="date" name="buku_tglterbit" value="<?php echo htmlspecialchars($edit_data['buku_tglterbit'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Jumlah Halaman:</label>
                <input type="number" name="buku_jmlhalaman" value="<?php echo htmlspecialchars($edit_data['buku_jmlhalaman'] ?? ''); ?>" min="1">
            </div>
            <div class="form-group">
                <label>Harga:</label>
                <input type="number" name="buku_harga" step="0.01" value="<?php echo htmlspecialchars($edit_data['buku_harga'] ?? ''); ?>" min="0">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Kategori:</label>
                <select name="kategori_id" id="kategori_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $c['kategori_id']; ?>" <?php echo (isset($edit_data) && $edit_data['kategori_id'] == $c['kategori_id'] ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($c['kategori_nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Pengarang:</label>
                <select name="pengarang_id" id="pengarang_id" required>
                    <option value="">Pilih Pengarang</option>
                    <?php while ($a = mysqli_fetch_assoc($authors)): ?>
                        <option value="<?php echo $a['pengarang_id']; ?>" <?php echo (isset($edit_data) && $edit_data['pengarang_id'] == $a['pengarang_id'] ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($a['pengarang_nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi:</label>
            <textarea name="buku_deskripsi" maxlength="1000"><?php echo htmlspecialchars($edit_data['buku_deskripsi'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo ($action === 'edit' ? 'Update' : 'Tambah'); ?></button>
        <a href="index.php?page=kelola_buku" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
function validateForm() {
    if (document.getElementById('kategori_id').value === '') {
        alert('Kategori harus dipilih.');
        return false;
    }
    if (document.getElementById('pengarang_id').value === '') {
        alert('Pengarang harus dipilih.');
        return false;
    }
    return true;
}
</script>
</body>
</html>