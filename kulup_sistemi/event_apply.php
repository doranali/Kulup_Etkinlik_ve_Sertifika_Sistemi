<?php
// event_apply.php
// Öğrencinin bir etkinliğe katılma/kayıt olma işlemini yönetir.

include_once 'session_check.php';
include_once 'veritabani.php';

// Sadece POST isteği ile çalışır, aksi halde hata verip yönlendiririm
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message_type'] = 'danger';
    $_SESSION['message_content'] = "Geçersiz istek. Lütfen bir etkinlik seçarak tekrar deneyin.";
    header("Location: clubs.php");
    exit;
}

// POST ile gelen etkinlik ID'sini alıyorum
$event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($event_id <= 0) {
    $_SESSION['message_type'] = 'danger';
    $_SESSION['message_content'] = "Geçersiz etkinlik ID'si.";
    header("Location: clubs.php");
    exit;
}

// Etkinliğin varlığını kontrol ediyorum
$eventCheckSorgu = $baglanti->prepare("SELECT id, title FROM events WHERE id = ?");
$eventCheckSorgu->execute([$event_id]);
$event = $eventCheckSorgu->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    $_SESSION['message_type'] = 'danger';
    $_SESSION['message_content'] = "Başvurmaya çalıştığınız etkinlik bulunamadı.";
    header("Location: clubs.php");
    exit;
}

// Kullanıcı zaten kayıtlı mı kontrol ediyorum
$participationCheckSorgu = $baglanti->prepare("SELECT id FROM event_participation WHERE event_id = ? AND user_id = ?");
$participationCheckSorgu->execute([$event_id, $user_id]);
$already_participating = $participationCheckSorgu->fetch();

if ($already_participating) {
    $_SESSION['message_type'] = 'warning';
    $_SESSION['message_content'] = "'" . htmlspecialchars($event['title']) . "' etkinliğine zaten kayıtlısınız.";
    header("Location: clubs.php");
    exit;
}

// Katılımı ekliyorum
$stmt = $baglanti->prepare("INSERT INTO event_participation (event_id, user_id) VALUES (?, ?)");
if ($stmt->execute([$event_id, $user_id])) {
    $_SESSION['message_type'] = 'success';
    $_SESSION['message_content'] = "'" . htmlspecialchars($event['title']) . "' etkinliğine başarıyla kayıt oldunuz!";
} else {
    $_SESSION['message_type'] = 'danger';
    $_SESSION['message_content'] = "'" . htmlspecialchars($event['title']) . "' etkinliğine kayıt olurken bir sorun oluştu.";
}

header("Location: clubs.php");
exit;
?>
<form action="event_apply.php" method="post" class="d-inline">
    <input type="hidden" name="event_id" value="<?php echo $etk['id']; ?>">
    <button type="submit" class="btn btn-sm btn-success">Katıl</button>
</form>