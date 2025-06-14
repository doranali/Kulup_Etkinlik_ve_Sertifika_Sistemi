<?php
// statistics.php
// Adminin sistemin genel istatistiklerini (kullanıcı, kulüp, etkinlik vb. sayıları) görüntülemesini sağlar.

include_once 'admin_check.php';
include_once 'veritabani.php';
include_once 'header.php';

// Sistemdeki çeşitli varlıkların sayılarını veritabanından çekiyorum.
// fetchColumn() metodu, sorgudan dönen ilk sütunun ilk satırındaki değeri alır.
$kullanici_sayisi = $baglanti->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$yonetici_sayisi  = $baglanti->query("SELECT COUNT(*) FROM users WHERE role = 'manager'")->fetchColumn();
$admin_sayisi     = $baglanti->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$kulup_sayisi     = $baglanti->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
$etkinlik_sayisi  = $baglanti->query("SELECT COUNT(*) FROM events")->fetchColumn();
$katilim_sayisi   = $baglanti->query("SELECT COUNT(*) FROM event_participation")->fetchColumn();
$sertifika_sayisi = $baglanti->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
?>

<style>
.stats-container {
    max-width: 520px;
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
.stats-title {
    font-weight: 700;
    font-size: 2rem;
    letter-spacing: 0.01em;
    margin-bottom: 0.7rem;
    text-align: center;
}
.stats-desc {
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}
.stats-list {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    margin-bottom: 2rem;
}
.stats-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(74,144,226,0.07);
    padding: 1.1rem 1.2rem;
    font-weight: 500;
    font-size: 1.08rem;
    color: #2c3e50;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: box-shadow 0.2s, transform 0.2s, color 0.2s;
    border: none;
}
.stats-badge {
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    padding: 0.5em 1.2em;
}
.stats-badge.student {background:#4a90e2;color:#fff;}
.stats-badge.manager {background:#2ecc71;color:#fff;}
.stats-badge.admin {background:#e67e22;color:#fff;}
.stats-badge.club {background:#00bcd4;color:#fff;}
.stats-badge.event {background:#27ae60;color:#fff;}
.stats-badge.participation {background:#7b8a8b;color:#fff;}
.stats-badge.certificate {background:#f1c40f;color:#fff;}
@media (max-width:600px){.stats-container{padding:1.2rem 0.7rem;}.stats-title{font-size:1.1rem;}.stats-card{font-size:0.97rem;padding:0.7rem 0.5rem;}}
</style>

<div class="stats-container">
    <div class="stats-title">Sistem İstatistikleri</div>
    <div class="stats-desc">Bu bölümde sistemdeki genel verileri hızlıca görebilirsiniz.</div>
    <div class="stats-list">
        <div class="stats-card">Toplam Öğrenci Sayısı: <span class="stats-badge student"><?php echo $kullanici_sayisi; ?></span></div>
        <div class="stats-card">Toplam Kulüp Yöneticisi Sayısı: <span class="stats-badge manager"><?php echo $yonetici_sayisi; ?></span></div>
        <div class="stats-card">Toplam Admin Sayısı: <span class="stats-badge admin"><?php echo $admin_sayisi; ?></span></div>
        <div class="stats-card">Toplam Kulüp Sayısı: <span class="stats-badge club"><?php echo $kulup_sayisi; ?></span></div>
        <div class="stats-card">Toplam Etkinlik Sayısı: <span class="stats-badge event"><?php echo $etkinlik_sayisi; ?></span></div>
        <div class="stats-card">Toplam Etkinlik Katılımı: <span class="stats-badge participation"><?php echo $katilim_sayisi; ?></span></div>
        <div class="stats-card">Toplam Sertifika Sayısı: <span class="stats-badge certificate"><?php echo $sertifika_sayisi; ?></span></div>
    </div>
    <div class="text-center">
        <a href="dashboard.php" class="btn btn-secondary">Admin Paneline Dön</a>
    </div>
</div>

<?php 
// Sayfanın alt kısmını (JavaScript, kapanış etiketleri vb.) dahil ediyorum.
include_once 'footer.php'; 
?>