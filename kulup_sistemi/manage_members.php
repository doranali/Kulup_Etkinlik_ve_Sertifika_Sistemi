<?php
// manage_members.php
// Kulüp yöneticisinin yönettiği kulübün üyelerini listelerim.

include_once 'manager_check.php';
include_once 'veritabani.php';
include_once 'header.php';

// Mevcut yöneticinin yönettiği kulübün bilgilerini çekiyorum
$kulupSorgu = $baglanti->prepare("SELECT id, name FROM clubs WHERE manager_id = ?");
$kulupSorgu->execute([$_SESSION['user_id']]);
$kulubum = $kulupSorgu->fetch(PDO::FETCH_ASSOC);

if (!$kulubum) {
    echo '<div class="alert alert-warning">Yetkili olduğunuz bir kulüp bulunamadı. Lütfen önce bir kulüp oluşturduğunuzdan veya başvurunuzun onaylandığından emin olun.</div>';
    include_once 'footer.php';
    exit;
}

// Yöneticinin kulübüne üye olan öğrencileri çekiyorum
$uyeSorgu = $baglanti->prepare(
    "SELECT users.first_name, users.last_name, users.email
     FROM club_members
     JOIN users ON club_members.user_id = users.id
     WHERE club_members.club_id = ?"
);
$uyeSorgu->execute([$kulubum['id']]);
$uyeler = $uyeSorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<h3><?php echo htmlspecialchars($kulubum['name']); ?> Kulübü Üyeleri</h3>

<style>
.table-responsive {overflow-x:auto;}
@media (max-width: 700px) {
    table {font-size: 0.95rem;}
    th, td {padding: 0.4rem 0.3rem;}
}
</style>

<?php 
if (count($uyeler) == 0): 
?>
    <div class="alert alert-info">Henüz bu kulübe üye bulunmamaktadır.</div>
<?php 
else: 
?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>E-posta</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($uyeler as $uye): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($uye['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($uye['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($uye['email']); ?></td>
                    </tr>
                <?php 
                endforeach; 
                ?>
            </tbody>
        </table>
    </div>
<?php 
endif; 
?>

<p class="mt-3">
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Panele Dön</a>
</p>

<?php 
// Sayfanın alt kısmını ekliyorum
include_once 'footer.php'; 
?>