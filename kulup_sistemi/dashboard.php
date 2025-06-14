<?php
// dashboard.php
// Öğrenci rolündeki kullanıcının ana paneli. Katıldığı etkinlikleri ve sertifikalarını gösterir.

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: auth_login.php");
    exit;
}

// Veritabanı bağlantısını ekliyorum
include_once 'veritabani.php';

// Kulübe üye olma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['club_id'])) {
    $club_id = intval($_POST['club_id']);
    $user_id = $_SESSION['user_id'];

    // Zaten üye mi kontrol ediyorum
    $stmt = $baglanti->prepare("SELECT COUNT(*) FROM club_members WHERE club_id = ? AND user_id = ?");
    $stmt->execute([$club_id, $user_id]);
    if ($stmt->fetchColumn() == 0) {
        $ekle = $baglanti->prepare("INSERT INTO club_members (club_id, user_id) VALUES (?, ?)");
        $ekle->execute([$club_id, $user_id]);
    }
    header("Location: dashboard.php");
    exit;
}

// Etkinliğe katılma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $user_id = $_SESSION['user_id'];

    // Zaten katıldı mı kontrol ediyorum
    $stmt = $baglanti->prepare("SELECT COUNT(*) FROM event_participation WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    if ($stmt->fetchColumn() == 0) {
        $ekle = $baglanti->prepare("INSERT INTO event_participation (event_id, user_id) VALUES (?, ?)");
        $ekle->execute([$event_id, $user_id]);
    }
    header("Location: dashboard.php");
    exit;
}

// Kullanıcıyı çekiyorum
$stmt = $baglanti->prepare("SELECT * FROM users WHERE id = ? AND role = ?");
$stmt->execute([$_SESSION['user_id'], 'student']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: auth_login.php");
    exit;
}

// Sayfanın üst kısmını ekliyorum
include_once 'header.php';

$user_id = $_SESSION['user_id'];

// 1. Katıldığı etkinlikleri çekiyorum
$etkinliklerSorgu = $baglanti->prepare(
    "SELECT events.id, events.title, events.event_date, events.description, clubs.name AS club_name
     FROM event_participation
     JOIN events ON event_participation.event_id = events.id
     JOIN clubs ON events.club_id = clubs.id
     WHERE event_participation.user_id = ?
     ORDER BY events.event_date DESC"
);
$etkinliklerSorgu->execute([$user_id]);
$etkinlikler = $etkinliklerSorgu->fetchAll(PDO::FETCH_ASSOC);

// 2. Sahip olduğu sertifikaları çekiyorum
$sertifikaSorgu = $baglanti->prepare(
    "SELECT certificates.file_path, certificates.issued_date, events.title AS event_title
     FROM certificates
     JOIN events ON certificates.event_id = events.id
     WHERE certificates.user_id = ?
     ORDER BY certificates.issued_date DESC"
);
$sertifikaSorgu->execute([$user_id]);
$sertifikalar = $sertifikaSorgu->fetchAll(PDO::FETCH_ASSOC);

// 3. Katılabileceği etkinlikleri çekiyorum
$katilabilecekEtkinliklerSorgu = $baglanti->prepare(
    "SELECT e.id, e.title, e.event_date, e.description, c.name AS club_name
     FROM events e
     JOIN clubs c ON e.club_id = c.id
     WHERE e.id NOT IN (
         SELECT ep.event_id FROM event_participation ep WHERE ep.user_id = ?
     )
     ORDER BY e.event_date ASC"
);
$katilabilecekEtkinliklerSorgu->execute([$user_id]);
$katilabilecekEtkinlikler = $katilabilecekEtkinliklerSorgu->fetchAll(PDO::FETCH_ASSOC);

// Onaylı kulüpleri çekiyorum
$kulupSorgu = $baglanti->prepare("SELECT * FROM clubs WHERE status = 'approved'");
$kulupSorgu->execute();
$onayliKulüpler = $kulupSorgu->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
.dashboard-section {margin-bottom:2.5rem;}
.simple-list {display:flex;flex-wrap:wrap;gap:1.5rem;}
.simple-card {background:linear-gradient(135deg,#f8fafc 0%,#e3e9f7 100%);border-radius:18px;box-shadow:0 4px 24px rgba(74,144,226,0.10);padding:1.5rem 1.2rem;min-width:220px;max-width:340px;flex:1 1 220px;transition:transform 0.25s cubic-bezier(.4,2,.6,1),box-shadow 0.25s;opacity:0;transform:translateY(30px);animation:fadeInUp 0.7s forwards;}
.simple-card:hover{transform:translateY(-8px) scale(1.03);box-shadow:0 8px 32px rgba(74,144,226,0.18);}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0);}}
.simple-title{font-size:1.1rem;font-weight:600;color:#2c3e50;margin-bottom:0.5rem;letter-spacing:0.01em;}
.simple-date{font-size:0.98rem;color:#4a90e2;margin-bottom:0.7rem;}
.simple-btn{display:inline-block;background:#4a90e2;color:#fff;border:none;border-radius:8px;padding:0.5rem 1.1rem;font-weight:500;font-size:1rem;text-decoration:none;transition:background 0.2s,box-shadow 0.2s;box-shadow:0 2px 8px rgba(74,144,226,0.08);margin-top:0.7rem;}
.simple-btn:hover{background:#357ab8;color:#fff;box-shadow:0 4px 16px rgba(74,144,226,0.16);}
@media (max-width:600px){.simple-list{flex-direction:column;gap:1.2rem;align-items:stretch;}.simple-card{min-width:unset;max-width:unset;padding:1.1rem 0.7rem;}}
</style>

<div class="container fade-in">
    <div class="dashboard-section">
        <h4 class="mb-3">Katıldığım Etkinlikler</h4>
        <?php if(count($etkinlikler)==0): ?>
            <div class="alert alert-info text-center" style="border-radius:12px;">Henüz katıldığınız bir etkinlik yok.</div>
        <?php else: ?>
            <div class="simple-list">
                <?php foreach($etkinlikler as $etk): ?>
                    <div class="simple-card">
                        <div class="simple-title"><?php echo htmlspecialchars($etk['title']); ?></div>
                        <div class="simple-date"><?php echo htmlspecialchars($etk['event_date']); ?></div>
                        <div style="color:#888;font-size:0.97rem;"><?php echo htmlspecialchars($etk['club_name']); ?></div>
                        <?php if(!empty($etk['description'])): ?><div style="color:#666;font-size:0.95rem;margin-top:0.5rem;"><?php echo htmlspecialchars($etk['description']); ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="dashboard-section">
        <h4 class="mb-3">Katılabileceğiniz Etkinlikler</h4>
        <?php if(count($katilabilecekEtkinlikler)==0): ?>
            <div class="alert alert-info text-center" style="border-radius:12px;">Şu anda katılabileceğiniz yeni bir etkinlik yok.</div>
        <?php else: ?>
            <div class="simple-list">
                <?php foreach($katilabilecekEtkinlikler as $etk): ?>
                    <div class="simple-card">
                        <div class="simple-title"><?php echo htmlspecialchars($etk['title']); ?></div>
                        <div class="simple-date"><?php echo htmlspecialchars($etk['event_date']); ?></div>
                        <div style="color:#888;font-size:0.97rem;"><?php echo htmlspecialchars($etk['club_name']); ?></div>
                        <?php if(!empty($etk['description'])): ?><div style="color:#666;font-size:0.95rem;margin-top:0.5rem;"><?php echo htmlspecialchars($etk['description']); ?></div><?php endif; ?>
                        <form action="dashboard.php" method="POST" style="margin-top:0.7rem;">
                            <input type="hidden" name="event_id" value="<?php echo $etk['id']; ?>">
                            <button type="submit" class="simple-btn">Katıl</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="dashboard-section">
        <h4 class="mb-3">Kulüpler</h4>
        <?php if(count($onayliKulüpler)==0): ?>
            <div class="alert alert-info text-center" style="border-radius:12px;">Henüz katılabileceğiniz bir kulüp yok.</div>
        <?php else: ?>
            <div class="simple-list">
                <?php foreach($onayliKulüpler as $kulup): ?>
                    <?php $uyeMi = $baglanti->prepare("SELECT COUNT(*) FROM club_members WHERE club_id = ? AND user_id = ?"); $uyeMi->execute([$kulup['id'], $_SESSION['user_id']]); $uye = $uyeMi->fetchColumn(); ?>
                    <?php if(!$uye): ?>
                        <div class="simple-card">
                            <div class="simple-title"><?php echo htmlspecialchars($kulup['name']); ?></div>
                            <form action="dashboard.php" method="POST" style="margin-top:0.7rem;">
                                <input type="hidden" name="club_id" value="<?php echo $kulup['id']; ?>">
                                <button type="submit" class="simple-btn">Üye Ol</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="dashboard-section">
        <h4 class="mb-3">Üyesi Olduğum Kulüpler</h4>
        <?php
        $uyelikler = $baglanti->prepare("SELECT c.* FROM clubs c JOIN club_members m ON c.id = m.club_id WHERE m.user_id = ? AND c.status = 'approved'");
        $uyelikler->execute([$user_id]);
        $uyelikKulup = $uyelikler->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if(count($uyelikKulup)==0): ?>
            <div class="alert alert-info text-center" style="border-radius:12px;">Henüz üye olduğunuz bir kulüp yok.</div>
        <?php else: ?>
            <div class="simple-list">
                <?php foreach($uyelikKulup as $kulup): ?>
                    <div class="simple-card">
                        <div class="simple-title"><?php echo htmlspecialchars($kulup['name']); ?></div>
                        <span class="badge bg-success" style="font-size:1rem;padding:0.5em 1em;border-radius:8px;">Üyesin</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="text-center" style="margin-top:2.5rem;">
        <a href="club_apply.php" class="simple-btn" style="font-size:1.1rem;">Kulüp Başvurusu Yap</a>
    </div>
</div>

<?php include_once 'footer.php'; ?>