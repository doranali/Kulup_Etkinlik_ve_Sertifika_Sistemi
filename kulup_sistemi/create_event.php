<?php
// create_event.php
// Kulüp yöneticisi olarak kendi kulübüm için yeni etkinlik oluşturabilirim.

include_once 'manager_check.php';
include_once 'veritabani.php';
include_once 'header.php';

$mesaj = "";

// Oturumdaki yöneticinin kulübünü çekiyorum
$kulupSorgu = $baglanti->prepare("SELECT id FROM clubs WHERE manager_id = ?");
$kulupSorgu->execute([$_SESSION['user_id']]);
$kulubum = $kulupSorgu->fetch(PDO::FETCH_ASSOC);

if (!$kulubum) {
    echo '<div class="alert alert-warning">Yetkili olduğunuz bir kulüp bulunamadı. Lütfen önce bir kulüp oluşturduğunuzdan veya başvurunuzun onaylandığından emin olun.</div>';
    include_once 'footer.php';
    exit;
}

// Form gönderildiyse etkinlik ekliyorum
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $event_date = trim($_POST['event_date']);
    $description = trim($_POST['description']);

    if (!empty($title) && !empty($event_date)) {
        $ekle = $baglanti->prepare("INSERT INTO events (title, event_date, club_id, description) VALUES (?, ?, ?, ?)");
        $ekle->execute([$title, $event_date, $kulubum['id'], $description]);
        $mesaj = "Etkinlik başarıyla oluşturuldu!";
    } else {
        $mesaj = "Lütfen başlık ve tarihi giriniz.";
    }
}
?>

<style>
.create-event-container {
    max-width: 420px;
    margin: 2.5rem auto;
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 2.2rem 1.5rem;
    animation: fadeInUp 0.7s forwards;
    opacity: 0;
    transform: translateY(30px);
}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0);}}
.create-event-container h3 {
    font-weight: 700;
    letter-spacing: 0.01em;
    margin-bottom: 1.5rem;
    text-align: center;
}
.create-event-container label {
    font-weight: 500;
    color: #2c3e50;
}
.create-event-container .form-control {
    padding: 1rem 1.2rem;
    font-size: 1.05rem;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.1);
    background: rgba(255,255,255,0.9);
}
.create-event-container .form-control:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74,144,226,0.15);
}
.create-event-container .btn-primary {
    width: 100%;
    padding: 0.8rem;
    font-weight: 500;
    font-size: 1.05rem;
    border-radius: 10px;
    background: #4a90e2;
    border: none;
    transition: all 0.3s ease;
}
.create-event-container .btn-primary:hover {
    background: #357abd;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(74,144,226,0.2);
}
.create-event-container .btn-secondary {
    border-radius: 10px;
    width: 100%;
    margin-top: 0.7rem;
}
@media (max-width: 480px) {
    .create-event-container {
        margin: 1rem;
        padding: 1.5rem 1rem;
    }
    .create-event-container h3 {font-size: 1.2rem;}
}
</style>

<div class="create-event-container">
    <h3>Etkinlik Oluştur</h3>
    <?php 
    if (!empty($mesaj)): 
    ?>
        <div class="alert alert-<?php echo strpos($mesaj, 'başarıyla') !== false ? 'success' : 'danger'; ?> text-center" style="border-radius:12px;"><?php echo htmlspecialchars($mesaj); ?></div>
    <?php 
    endif; 
    ?>
    <form method="post">
        <div class="mb-3">
            <label for="title">Etkinlik Adı</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="event_date">Tarih</label>
            <input type="date" name="event_date" id="event_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description">Açıklama</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Oluştur</button>
    </form>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Panele Dön</a>
</div>

<?php 
// Sayfanın alt kısmını ekliyorum
include_once 'footer.php'; 
?>