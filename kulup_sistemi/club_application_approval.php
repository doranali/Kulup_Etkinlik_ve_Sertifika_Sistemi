<?php
// club_application_approval.php
// Kulüp yöneticiliği başvurularını admin olarak onaylamamı veya reddetmemi sağlar.

include_once 'admin_check.php';
include_once 'veritabani.php';
include_once 'header.php';

$mesaj = "";
$mesaj_tipi = "";

// Başvuru onaylama işlemi
if (isset($_GET['onayla'])) {
    $id = intval($_GET['onayla']);

    $sorgu = $baglanti->prepare("SELECT * FROM club_applications WHERE id = ?");
    $sorgu->execute([$id]);
    $basvuru = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($basvuru && $basvuru['status'] == 'pending') {
        try {
            $baglanti->beginTransaction();

            $guncelle_basvuru = $baglanti->prepare("UPDATE club_applications SET status = 'approved' WHERE id = ?");
            $guncelle_basvuru->execute([$id]);

            $guncelle_kullanici_rolu = $baglanti->prepare("UPDATE users SET role = 'manager' WHERE id = ?");
            $guncelle_kullanici_rolu->execute([$basvuru['user_id']]);

            $kulup_ekle = $baglanti->prepare("INSERT INTO clubs (name, description, manager_id, status) VALUES (?, ?, ?, 'approved')");
            $kulup_ekle->execute([$basvuru['club_name'], $basvuru['message'], $basvuru['user_id']]);

            $baglanti->commit();

            $_SESSION['mesaj'] = "Başvuru başarıyla onaylandı ve kulüp oluşturuldu!";
            $_SESSION['mesaj_tipi'] = "success";
        } catch (PDOException $e) {
            $baglanti->rollBack();
            $_SESSION['mesaj'] = "Başvuru onaylanırken bir hata oluştu: " . $e->getMessage();
            $_SESSION['mesaj_tipi'] = "danger";
        }
    } else {
        $_SESSION['mesaj'] = "Onaylanacak geçerli bir başvuru bulunamadı veya zaten işlenmiş.";
        $_SESSION['mesaj_tipi'] = "warning";
    }
    header("Location: club_application_approval.php");
    exit;
}

// Başvuru reddetme işlemi
if (isset($_GET['reddet'])) {
    $id = intval($_GET['reddet']);

    $reddet = $baglanti->prepare("UPDATE club_applications SET status = 'rejected' WHERE id = ?");
    if ($reddet->execute([$id])) {
        $_SESSION['mesaj'] = "Başvuru başarıyla reddedildi.";
        $_SESSION['mesaj_tipi'] = "info";
    } else {
        $_SESSION['mesaj'] = "Başvuru reddedilirken bir hata oluştu.";
        $_SESSION['mesaj_tipi'] = "danger";
    }
    header("Location: club_application_approval.php");
    exit;
}

// Mesajı ekranda göstermek için
if (isset($_SESSION['mesaj'])) {
    $mesaj = $_SESSION['mesaj'];
    $mesaj_tipi = $_SESSION['mesaj_tipi'];
    unset($_SESSION['mesaj']);
    unset($_SESSION['mesaj_tipi']);
}

// Bekleyen başvuruları çekiyorum
$stmt = $baglanti->prepare("SELECT * FROM club_applications WHERE status = 'pending'");
$stmt->execute();
$basvurular = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.club-approval-container {
    max-width: 900px;
    margin: 2.5rem auto;
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 2.5rem 2rem 2rem 2rem;
    animation: fadeInUp 0.7s forwards;
    opacity: 0;
    transform: translateY(30px);
    overflow-x: auto;
}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0);}}
.club-approval-title {
    font-weight: 700;
    font-size: 2rem;
    letter-spacing: 0.01em;
    margin-bottom: 1.5rem;
    text-align: center;
}
@media (max-width: 700px) {
    .club-approval-container {padding: 1.1rem 0.3rem;}
    .club-approval-title {font-size: 1.2rem;}
    table {font-size: 0.95rem;}
    th, td {padding: 0.4rem 0.3rem;}
}
</style>

<div class="club-approval-container">
    <div class="club-approval-title">Kulüp Başvuruları (Onay/Reddet)</div>
    <?php if ($mesaj != ""): ?>
        <div class="alert alert-<?php echo htmlspecialchars($mesaj_tipi); ?> alert-dismissible fade show" role="alert" data-autoclose="4000">
            <?php echo htmlspecialchars($mesaj); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (count($basvurular) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Kulüp Adı</th>
                    <th>Açıklama</th>
                    <th>Başvuran Kullanıcı</th>
                    <th>Başvuru Tarihi</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($basvurular as $basvuru): ?>
                <tr>
                    <td><?php echo htmlspecialchars($basvuru['club_name']); ?></td>
                    <td><?php echo htmlspecialchars($basvuru['message']); ?></td>
                    <td>
                        <?php
                        $userStmt = $baglanti->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                        $userStmt->execute([$basvuru['user_id']]);
                        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                        echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($basvuru['created_at']); ?></td>
                    <td>
                        <a href="club_application_approval.php?onayla=<?php echo $basvuru['id']; ?>" class="btn btn-success btn-sm">Onayla</a>
                        <a href="club_application_approval.php?reddet=<?php echo $basvuru['id']; ?>" class="btn btn-danger btn-sm">Reddet</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Bekleyen başvuru yok.</div>
    <?php endif; ?>
    <p class="mt-3 text-center">
        <a href="dashboard.php" class="btn btn-secondary">Admin Paneline Dön</a>
    </p>
</div>

<?php 
// Sayfanın alt kısmını ekliyorum
include_once 'footer.php'; 
?>