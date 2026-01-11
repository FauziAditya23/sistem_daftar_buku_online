<?php
// Koneksi tetap sama
require_once __DIR__ . '/../includes/config.php';
$conn = db_connect();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = !empty($search) ? "WHERE b.buku_judul LIKE '%$search%' OR b.buku_isbn LIKE '%$search%'" : "";

$sql = "SELECT b.*, k.kategori_nama FROM tbl_buku b 
        LEFT JOIN tbl_kategori k ON b.kategori_id = k.kategori_id 
        $where_clause ORDER BY b.buku_isbn DESC LIMIT 4";
$query_buku = mysqli_query($conn, $sql);
?>

<div class="modern-wrapper" style="background-color: #f4f9ff; font-family: 'Inter', system-ui, -apple-system, sans-serif; padding-bottom: 50px;">
    
    <div style="max-width: 1300px; margin: 0 auto; padding: 40px 20px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 40px; background: white; border-radius: 40px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 123, 255, 0.1);">
            
            <div style="flex: 1; min-width: 300px;">
                <div style="display: inline-block; background: #e6f0ff; color: #007bff; padding: 8px 20px; border-radius: 100px; font-size: 13px; font-weight: 700; margin-bottom: 20px;">
                    🚀 SMART LIBRARY 2.0
                </div>
                <h1 style="font-size: clamp(32px, 4vw, 48px); color: #1a202c; line-height: 1.1; font-weight: 850; margin-bottom: 20px;">
                    Eksplorasi Literasi dalam <span style="color: #007bff;">Genggaman.</span>
                </h1>
                <p style="color: #64748b; font-size: 17px; line-height: 1.6; margin-bottom: 30px; max-width: 500px;">
                    Akses ribuan referensi akademik dan umum dengan sistem pencarian cerdas yang dirancang untuk mempercepat riset Anda.
                </p>
                
                <form action="" method="GET" style="display: flex; background: #f1f5f9; padding: 6px; border-radius: 20px; border: 1px solid #e2e8f0;">
                    <input type="hidden" name="page" value="home">
                    <input type="text" name="search" placeholder="Cari judul buku atau ISBN..." 
                           style="flex: 1; background: transparent; border: none; padding: 12px 20px; outline: none; font-size: 15px;">
                    <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 15px; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,123,255,0.3);">
                        Cari
                    </button>
                </form>
            </div>

            <div style="flex: 1; min-width: 300px; display: flex; justify-content: center; position: relative;">
                <div style="width: 300px; height: 300px; background: linear-gradient(135deg, #007bff, #60a5fa); border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; animate: morph 8s ease-in-out infinite; display: flex; align-items: center; justify-content: center; font-size: 120px;">
                    📚
                </div>
                <div style="position: absolute; bottom: 20px; left: 20px; background: white; padding: 15px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 12px;">
                    <div style="background: #ffd700; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">⭐</div>
                    <div>
                        <div style="font-weight: 800; font-size: 14px; color: #1a202c;">Top Rated</div>
                        <div style="font-size: 12px; color: #718096;">Koleksi Unggulan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="max-width: 1300px; margin: 0 auto; padding: 0 20px 60px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <div class="stat-card" style="background: #007bff; color: white; padding: 30px; border-radius: 30px;">
            <div style="font-size: 32px; font-weight: 800;">10k+</div>
            <div style="opacity: 0.8; font-size: 14px;">Total Koleksi Buku</div>
        </div>
        <div class="stat-card" style="background: white; color: #1a202c; padding: 30px; border-radius: 30px; border: 1px solid #e2e8f0;">
            <div style="font-size: 32px; font-weight: 800; color: #007bff;">24/7</div>
            <div style="color: #718096; font-size: 14px;">Akses Digital Online</div>
        </div>
        <div class="stat-card" style="background: #ffd700; color: #1a202c; padding: 30px; border-radius: 30px;">
            <div style="font-size: 32px; font-weight: 800;">Free</div>
            <div style="opacity: 0.8; font-size: 14px;">Pendaftaran Anggota</div>
        </div>
    </div>

    <div style="max-width: 1300px; margin: 0 auto; padding: 0 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-weight: 850; color: #1a202c; font-size: 24px; display: flex; align-items: center; gap: 10px;">
                <span style="width: 8px; height: 24px; background: #007bff; border-radius: 4px;"></span>
                Baru Saja Ditambahkan
            </h2>
            <a href="?page=kelola_buku" style="color: #007bff; font-weight: 700; text-decoration: none; font-size: 14px;">Lihat Katalog Lengkap →</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            <?php if (mysqli_num_rows($query_buku) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query_buku)): ?>
                    <div class="book-item" style="background: white; border-radius: 25px; padding: 15px; border: 1px solid #edf2f7; transition: 0.3s;">
                        <div style="height: 300px; background: #f8fafc; border-radius: 20px; margin-bottom: 15px; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 80px;">📖</span>
                            <div style="position: absolute; top: 15px; left: 15px; background: white; padding: 5px 15px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #007bff; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                <?php echo htmlspecialchars($row['kategori_nama'] ?? 'Umum'); ?>
                            </div>
                        </div>
                        <div style="padding: 0 10px 10px;">
                            <h3 style="font-size: 16px; font-weight: 800; color: #1a202c; margin-bottom: 5px; height: 44px; overflow: hidden; line-height: 1.4;">
                                <?php echo htmlspecialchars($row['buku_judul']); ?>
                            </h3>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                <div>
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">HARGA</div>
                                    <div style="font-weight: 850; color: #007bff; font-size: 18px;">Rp<?php echo number_format($row['buku_harga'], 0, ',', '.'); ?></div>
                                </div>
                                <a href="?page=kelola_buku" style="background: #1a202c; color: white; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s;">
                                    →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; background: white; padding: 60px; border-radius: 30px; text-align: center; border: 2px dashed #e2e8f0;">
                    <div style="font-size: 40px; margin-bottom: 15px;">🔍</div>
                    <h3 style="color: #4a5568; font-weight: 700;">Tidak Menemukan Apapun</h3>
                    <p style="color: #94a3b8;">Coba kata kunci lain atau periksa koneksi Anda.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .book-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: #007bff;
    }
    .book-item:hover a[style*="background: #1a202c"] {
        background: #007bff;
        transform: scale(1.1);
    }
    .stat-card {
        transition: 0.3s;
        cursor: default;
    }
    .stat-card:hover {
        transform: scale(1.02);
    }
    
    @media (max-width: 768px) {
        div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: 1fr;
        }
    }
</style>