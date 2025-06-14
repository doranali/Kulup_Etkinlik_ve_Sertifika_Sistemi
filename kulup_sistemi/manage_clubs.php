<?php
// manage_clubs.php
// Admin olarak sistemdeki tüm kulüpleri ve ilgili yöneticilerini görüntülememi sağlar.

include_once 'admin_check.php';
include_once 'veritabani.php';
include_once 'header.php';

// Tüm kulüpleri, kulüp yöneticisinin adı ve soyadıyla birlikte veritabanından çekiyorum.
// En yeni oluşturulan kulüpler üstte olacak şekilde sıralıyorum.
$sorgu = $baglanti->query("
    SELECT clubs.id, clubs.name, clubs.description, clubs.created_at, clubs.status,
           users.first_name, users.last_name
    FROM clubs
    JOIN users ON clubs.manager_id = users.id
    ORDER BY clubs.created_at DESC
");
$kulupler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.club-table-container {
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
.club-table-title {font-weight:700;font-size:2rem;text-align:center;margin-bottom:1.5rem;}
.club-table th, .club-table td {vertical-align:middle;}
.club-action-btn {margin-right:0.5rem;}
@media (max-width: 700px) {
    .club-table-container {padding: 1.1rem 0.3rem;}
    .club-table-title {font-size: 1.2rem;}
    .club-table {font-size: 0.95rem;}
    .club-table th, .club-table td {padding: 0.4rem 0.3rem;}
}
</style>

<div class="club-table-container">
    <div class="club-table-title">Kulüpleri Yönet</div>
    <?php if (count($kulupler) == 0): ?>
        <div class="alert alert-info">Sistemde kayıtlı kulüp bulunmamaktadır.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover club-table">
            <thead>
                <tr>
                    <th>Kulüp Adı</th>
                    <th>Açıklama</th>
                    <th>Yönetici</th>
                    <th>Oluşturulma Tarihi</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kulupler as $k): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($k['name']); ?></td>
                        <td><?php echo htmlspecialchars($k['description']); ?></td>
                        <td><?php echo htmlspecialchars($k['first_name'] . ' ' . $k['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($k['created_at']); ?></td>
                        <td>
                            <form method="post" action="change_club_status.php" style="display:inline-block;">
                                <input type="hidden" name="club_id" value="<?php echo $k['id']; ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="approved" <?php if(isset($k['status']) && $k['status']==='approved') echo 'selected'; ?>>Onaylı</option>
                                    <option value="pending" <?php if(isset($k['status']) && $k['status']==='pending') echo 'selected'; ?>>Beklemede</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="delete_club.php" style="display:inline-block;" onsubmit="return confirm('Bu kulübü silmek istediğinize emin misiniz?');">
                                <input type="hidden" name="club_id" value="<?php echo $k['id']; ?>">
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