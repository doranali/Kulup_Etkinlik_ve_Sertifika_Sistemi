<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth_login.php");
    exit;
}

include_once 'veritabani.php';
include_once 'header.php';

$user_id = $_SESSION['user_id'];

// Öğrencinin sahip olduğu sertifikaları veritabanından çekiyorum.
$sertifikaSorgu = $baglanti->prepare(
    "SELECT certificates.file_path, certificates.issued_date, events.title AS event_title
     FROM certificates
     JOIN events ON certificates.event_id = events.id
     WHERE certificates.user_id = ?
     ORDER BY certificates.issued_date DESC"
);
$sertifikaSorgu->execute([$user_id]);
$sertifikalar = $sertifikaSorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.certificate-list {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    justify-content: center;
    margin-top: 2rem;
}
.certificate-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 2rem 1.5rem;
    min-width: 270px;
    max-width: 340px;
    flex: 1 1 270px;
    transition: transform 0.25s cubic-bezier(.4,2,.6,1), box-shadow 0.25s;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.7s forwards;
}
.certificate-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 8px 32px rgba(74,144,226,0.18);
}
@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.certificate-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.7rem;
    letter-spacing: 0.01em;
}
.certificate-date {
    font-size: 0.98rem;
    color: #4a90e2;
    margin-bottom: 1.2rem;
}
.certificate-download {
    display: inline-block;
    background: #4a90e2;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    font-weight: 500;
    font-size: 1rem;
    text-decoration: none;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(74,144,226,0.08);
}
.certificate-download:hover {
    background: #357ab8;
    color: #fff;
    box-shadow: 0 4px 16px rgba(74,144,226,0.16);
}
@media (max-width: 600px) {
    .certificate-list {
        flex-direction: column;
        gap: 1.2rem;
        align-items: stretch;
    }
    .certificate-card {
        min-width: unset;
        max-width: unset;
        padding: 1.2rem 0.7rem;
    }
}
</style>

<div class="container fade-in">
    <h2 class="mb-4" style="font-weight:700; letter-spacing:0.01em;">Sertifikalarım</h2>
    <?php if (count($sertifikalar) == 0): ?>
        <div class="alert alert-info text-center" style="border-radius:12px;">
            Henüz yüklenmiş bir sertifikanız yok.
        </div>
    <?php else: ?>
        <div class="certificate-list">
            <?php foreach ($sertifikalar as $s): ?>
                <div class="certificate-card">
                    <div class="certificate-title">
                        <?php echo htmlspecialchars($s['event_title']); ?>
                    </div>
                    <?php if (!empty($s['issued_date'])): ?>
                        <div class="certificate-date">
                            <?php echo htmlspecialchars($s['issued_date']); ?> tarihinde verildi
                        </div>
                    <?php endif; ?>
                    <a href="uploads/certificates/<?php echo htmlspecialchars($s['file_path']); ?>" 
                       target="_blank" 
                       class="certificate-download">
                        Sertifikayı Görüntüle / İndir
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
