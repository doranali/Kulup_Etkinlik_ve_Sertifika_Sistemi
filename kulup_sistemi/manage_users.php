<?php
// manage_users.php
// Adminin sistemdeki tüm kullanıcıları (öğrenci, yönetici, admin) ve rollerini görüntülemesini sağlar.

include_once 'admin_check.php';
include_once 'veritabani.php';
include_once 'header.php';

// Tüm kullanıcıları (id, ad, soyad, e-posta, rol, kayıt tarihi) veritabanından çekiyorum
$sorgu = $baglanti->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC");
$kullanicilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.user-table-container {
    max-width: 1000px;
    margin: 2.5rem auto;
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 2.2rem 1.5rem;
    animation: fadeInUp 0.7s forwards;
    opacity: 0;
    transform: translateY(30px);
    overflow-x: auto;
}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0);}}
.user-table-title {font-weight:700;font-size:2rem;text-align:center;margin-bottom:1.5rem;}
.user-table th, .user-table td {vertical-align:middle;}
.user-action-btn {margin-right:0.5rem;}
@media (max-width: 700px) {
    .user-table-container {padding: 1.1rem 0.3rem;}
    .user-table-title {font-size: 1.2rem;}
    .user-table {font-size: 0.95rem;}
    .user-table th, .user-table td {padding: 0.4rem 0.3rem;}
}
</style>

<div class="user-table-container">
    <div class="user-table-title">Kullanıcıları Yönet</div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (count($kullanicilar) == 0): ?>
        <div class="alert alert-info">Sistemde kayıtlı kullanıcı bulunmamaktadır.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover user-table">
            <thead>
                <tr>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    <th>Kayıt Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kullanicilar as $k): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($k['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($k['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($k['email']); ?></td>
                        <td>
                            <form method="post" action="change_role.php" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?php echo $k['id']; ?>">
                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="student" <?php if($k['role']==='student') echo 'selected'; ?>>Öğrenci</option>
                                    <option value="manager" <?php if($k['role']==='manager') echo 'selected'; ?>>Yönetici</option>
                                    <option value="admin" <?php if($k['role']==='admin') echo 'selected'; ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($k['created_at']); ?></td>
                        <td>
                            <form method="post" action="delete_user.php" style="display:inline-block;" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                <input type="hidden" name="user_id" value="<?php echo $k['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p class="mt-3 text-center">
        <a href="dashboard.php" class="btn btn-secondary">Admin Paneline Dön</a>
    </p>
</div>

<?php 
// Sayfanın alt kısmını ekliyorum
include_once 'footer.php'; 
?>