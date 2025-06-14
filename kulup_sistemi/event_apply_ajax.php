<?php
// event_apply_ajax.php
// AJAX ile etkinliğe katılım işlemini yapar.

include_once 'session_check.php';
include_once 'veritabani.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
    exit;
}

$event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($event_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz etkinlik ID.']);
    exit;
}

// Etkinlik var mı kontrol ediyorum
$eventCheck = $baglanti->prepare('SELECT id, title FROM events WHERE id = ?');
$eventCheck->execute([$event_id]);
$event = $eventCheck->fetch(PDO::FETCH_ASSOC);
if (!$event) {
    echo json_encode(['success' => false, 'message' => 'Etkinlik bulunamadı.']);
    exit;
}

// Zaten katıldı mı kontrol ediyorum
$check = $baglanti->prepare('SELECT id FROM event_participation WHERE event_id = ? AND user_id = ?');
$check->execute([$event_id, $user_id]);
if ($check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Bu etkinliğe zaten katıldınız.']);
    exit;
}

// Katılımı ekliyorum
$stmt = $baglanti->prepare('INSERT INTO event_participation (event_id, user_id) VALUES (?, ?)');
if ($stmt->execute([$event_id, $user_id])) {
    echo json_encode(['success' => true, 'message' => 'Etkinliğe başarıyla katıldınız!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Katılım sırasında hata oluştu.']);
}