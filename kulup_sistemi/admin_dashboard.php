<?php
// admin_dashboard.php
// Bu sayfa sadece adminlerin erişebileceği yönetim panelini gösterir.

include_once 'admin_check.php';
include_once 'veritabani.php';
include_once 'header.php';
?>

<style>
.admin-panel-container {
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
.admin-panel-title {
    font-weight: 700;
    font-size: 2.2rem;
    letter-spacing: 0.01em;
    margin-bottom: 0.7rem;
    text-align: center;
}
.admin-panel-welcome {
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}
.admin-panel-menu {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.1rem;
}
@media (min-width: 600px) {
    .admin-panel-menu {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 600px) {
    .admin-panel-container {padding: 1.2rem 0.5rem;}
    .admin-panel-title {font-size: 1.3rem;}
}
.admin-panel-card {
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
.admin-panel-card:hover {
    box-shadow: 0 6px 24px rgba(74,144,226,0.13);
    color: #4a90e2;
    transform: translateY(-2px) scale(1.02);
    text-decoration: none;
}
.admin-panel-card.logout {
    color: #e74c3c;
    border: 1px solid #ffeaea;
    background: #fff6f6;
    justify-content: center;
}
.admin-panel-card.logout:hover {
    background: #ffeaea;
    color: #c0392b;
}
</style>

<div class="admin-panel-container">
    <div class="admin-panel-title">Admin Paneli</div>
    <div class="admin-panel-welcome">Hoş geldiniz, admin!</div>
    <div class="admin-panel-menu">
        <a href="statistics.php" class="admin-panel-card"><i class="fas fa-chart-bar me-2"></i> Sistem İstatistikleri</a>
        <a href="manage_users.php" class="admin-panel-card"><i class="fas fa-users-cog me-2"></i> Kullanıcıları Yönet</a>
        <a href="manage_clubs.php" class="admin-panel-card"><i class="fas fa-university me-2"></i> Kulüpleri Yönet</a>
        <a href="club_application_approval.php" class="admin-panel-card"><i class="fas fa-clipboard-check me-2"></i> Kulüp Başvurularını Onayla</a>
        <a href="edit_profile.php" class="admin-panel-card"><i class="fas fa-user-edit me-2"></i> Profilimi Düzenle</a>
        <a href="logout.php" class="admin-panel-card logout"><i class="fas fa-sign-out-alt me-2"></i> Çıkış Yap</a>
    </div>
</div>

<?php 
// Sayfanın alt kısmını ekliyorum.
include_once 'footer.php'; 
?>