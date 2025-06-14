<?php
// manager_dashboard.php
// Kulüp yöneticisinin ana paneli. Yönettiği kulübün bilgilerini ve ilgili işlemleri sunar.

include_once 'manager_check.php';
include_once 'veritabani.php';
include_once 'header.php';

// Mevcut yöneticinin yönettiği kulübü veritabanından çekiyorum.
$kulupSorgu = $baglanti->prepare("SELECT id, name, description FROM clubs WHERE manager_id = ?");
$kulupSorgu->execute([$_SESSION['user_id']]);
$kulubum = $kulupSorgu->fetch(PDO::FETCH_ASSOC);
?>

<style>
.manager-panel-container {
    max-width: 600px;
    margin: 2.5rem auto;
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 2.5rem 2rem 2rem 2rem;
    animation: fadeInUp 0.7s forwards;
    opacity: 0;
    transform: translateY(30px);
}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0);}}
.manager-panel-title {
    font-weight: 700;
    font-size: 2.1rem;
    letter-spacing: 0.01em;
    margin-bottom: 0.7rem;
    text-align: center;
}
.manager-panel-welcome {
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}
.manager-panel-menu {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.1rem;
}
@media (min-width: 600px) {
    .manager-panel-menu {
        grid-template-columns: 1fr 1fr;
    }
}
.manager-panel-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(74,144,226,0.07);
    padding: 1.1rem 1rem;
    font-weight: 500;
    font-size: 1.08rem;
    color: #2c3e50;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: box-shadow 0.2s, transform 0.2s, color 0.2s;
    border: none;
}
.manager-panel-card:hover {
    box-shadow: 0 6px 24px rgba(74,144,226,0.13);
    color: #4a90e2;
    transform: translateY(-2px) scale(1.02);
    text-decoration: none;
}
.manager-panel-card.logout {
    color: #e74c3c;
    border: 1px solid #ffeaea;
    background: #fff6f6;
    justify-content: center;
}
.manager-panel-card.logout:hover {
    background: #ffeaea;
    color: #c0392b;
}
.manager-panel-club {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(74,144,226,0.07);
    padding: 1.1rem 1rem;
    margin-bottom: 1.2rem;
    text-align: center;
}
.manager-panel-club h5 {font-weight:600;}
.manager-panel-club p {color:#555;}
@media (max-width: 600px) {
    .manager-panel-container {padding: 1.2rem 0.5rem;}
    .manager-panel-title {font-size: 1.3rem;}
}
</style>

<div class="manager-panel-container">
    <div class="manager-panel-title">Kulüp Yöneticisi Paneli</div>
    <div class="manager-panel-welcome">Hoş geldiniz, yönetici!</div>
    <?php if ($kulubum): ?>
        <div class="manager-panel-club">
            <h5><?php echo htmlspecialchars($kulubum['name']); ?></h5>
            <p><?php echo htmlspecialchars($kulubum['description']); ?></p>
        </div>
        <div class="manager-panel-menu">
            <a href="create_event.php" class="manager-panel-card"><i class="fas fa-calendar-plus me-2"></i> Etkinlik Oluştur</a>
            <a href="manage_members.php" class="manager-panel-card"><i class="fas fa-users me-2"></i> Kulüp Üyeleri</a>
            <a href="upload_certificate.php" class="manager-panel-card"><i class="fas fa-certificate me-2"></i> Sertifika Yükle</a>
            <a href="edit_profile.php" class="manager-panel-card"><i class="fas fa-user-edit me-2"></i> Profilimi Düzenle</a>
            <a href="logout.php" class="manager-panel-card logout"><i class="fas fa-sign-out-alt me-2"></i> Çıkış Yap</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center" style="border-radius:12px;">Bu hesabın yönettiği bir kulüp bulunamadı. Lütfen bir kulüp başvurusu yapın veya admin ile iletişime geçin.</div>
        <div class="manager-panel-menu" style="margin-top:1.5rem;">
            <a href="club_application.php" class="manager-panel-card"><i class="fas fa-plus-circle me-2"></i> Kulüp Başvurusu Yap</a>
            <a href="logout.php" class="manager-panel-card logout"><i class="fas fa-sign-out-alt me-2"></i> Çıkış Yap</a>
        </div>
    <?php endif; ?>
</div>

<?php 
// Sayfanın alt kısmını ekliyorum
include_once 'footer.php'; 
?>