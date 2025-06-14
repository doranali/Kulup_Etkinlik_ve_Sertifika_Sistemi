<?php
// certificates.php
// Giriş yapan öğrencinin tüm sertifikalarını listelerim.

// Oturum kontrolünü dahil ediyorum. Sadece giriş yapmış öğrenciler bu sayfaya erişebilir.
// İki klasör yukarı çıkıp 'includes' klasörüne gidiyorum.
include_once 'session_check.php';
// Veritabanı bağlantımı dahil ediyorum.
include_once 'veritabani.php';
// Sayfanın üst kısmını (HTML head, navigasyon vb.) dahil ediyorum.
include_once 'header.php';

$user_id = $_SESSION['user_id']; // Oturumdaki öğrencinin ID'si

// Öğrencinin sertifikalarını veritabanından çekiyorum.
$sorgu = $baglanti->prepare(
    "SELECT certificates.file_path, certificates.issued_date, events.title AS event_title
     FROM certificates
     JOIN events ON certificates.event_id = events.id
     WHERE certificates.user_id = ?
     ORDER BY certificates.issued_date DESC" // Sertifikaları veriliş tarihine göre sıralıyorum.
);
$sorgu->execute([$user_id]);
$sertifikalar = $sorgu->fetchAll(PDO::FETCH_ASSOC); // İlişkisel dizi olarak çekiyorum.
?>

<h3 class="mb-4">Sertifikalarım</h3>

<?php
// Eğer bir mesaj varsa ekranda gösteriyorum
if (isset($_SESSION['message_type']) && isset($_SESSION['message_content'])) {
    echo '<div class="alert alert-' . $_SESSION['message_type'] . '">' . htmlspecialchars($_SESSION['message_content']) . '</div>';
    unset($_SESSION['message_type']);
    unset($_SESSION['message_content']);
}
?>

<?php if (count($sertifikalar) == 0): ?>
    <div class="alert alert-info">Henüz yüklenmiş sertifikanız bulunmamaktadır.</div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php foreach ($sertifikalar as $s): ?>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($s['event_title']); ?></h5>
                    <p class="card-text">
                        <small class="text-muted">Veriliş Tarihi: <?php echo htmlspecialchars($s['issued_date']); ?></small>
                    </p>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="/kulup_sistemi/uploads/certificates/<?php echo htmlspecialchars($s['file_path']); ?>" target="_blank" class="btn btn-sm btn-primary">Görüntüle/İndir</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<p class="mt-4">
    <a href="dashboard.php" class="btn btn-secondary">Öğrenci Paneline Dön</a>
</p>

<?php
// Sayfanın alt kısmını ekliyorum
include_once 'footer.php';
?>