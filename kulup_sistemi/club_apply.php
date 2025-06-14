<?php
// club_apply.php
// Öğrencinin kulüp açma başvurusu yapmasını sağlar.

include_once 'session_check.php';
include_once 'veritabani.php';
include_once 'header.php';

// Form gönderildiyse işle
$mesaj = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kulup_adi = trim($_POST['kulup_adi'] ?? '');
    $aciklama = trim($_POST['aciklama'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Aynı gün içinde başvuru yapılıp yapılmadığını kontrol ediyorum
    $today = date('Y-m-d');
    $checkStmt = $baglanti->prepare("SELECT COUNT(*) FROM club_applications WHERE user_id = ? AND DATE(created_at) = ?");
    $checkStmt->execute([$user_id, $today]);
    $basvuruSayisi = $checkStmt->fetchColumn();

    if ($basvuruSayisi > 0) {
        $mesaj = '<div class="alert alert-danger">Bir gün içinde yalnızca bir kulüp başvurusu yapabilirsiniz.</div>';
    } else if ($kulup_adi && $aciklama) {
        $stmt = $baglanti->prepare("INSERT INTO club_applications (user_id, club_name, message, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $kulup_adi, $aciklama]);
        $mesaj = '<div class="alert alert-success">Başvurunuz alındı, admin onayından sonra kulübünüz listelenecek.</div>';
    } else {
        $mesaj = '<div class="alert alert-danger">Lütfen tüm alanları doldurun.</div>';
    }
}
?>

<div class="container mt-4">
    <h3>Kulüp Açma Başvurusu</h3>
    <?php echo $mesaj; ?>
    <form method="post">
        <div class="mb-3">
            <label for="kulup_adi" class="form-label">Kulüp Adı</label>
            <input type="text" class="form-control" id="kulup_adi" name="kulup_adi" required>
        </div>
        <div class="mb-3">
            <label for="aciklama" class="form-label">Açıklama</label>
            <textarea class="form-control" id="aciklama" name="aciklama" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Başvur</button>
        <a href="dashboard.php" class="btn btn-secondary">Panele Dön</a>
    </form>
</div>

<?php
include_once 'footer.php';
?>