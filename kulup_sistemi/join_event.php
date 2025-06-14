<?php
// join_event.php
// Öğrencinin bir etkinliğe katılmasını sağlar.

include_once 'session_check.php';
include_once 'veritabani.php';

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Daha önce bu etkinliğe katılıp katılmadığını kontrol ediyorum
        $check = $baglanti->prepare("SELECT id FROM event_participation WHERE user_id = ? AND event_id = ?");
        $check->execute([$user_id, $event_id]);
        
        if ($check->rowCount() == 0) {
            // Etkinliğe katılım kaydı oluşturuyorum
            $insert = $baglanti->prepare("INSERT INTO event_participation (user_id, event_id) VALUES (?, ?)");
            $insert->execute([$user_id, $event_id]);
            
            $_SESSION['success_message'] = "Etkinliğe başarıyla katıldınız!";
        } else {
            $_SESSION['error_message'] = "Bu etkinliğe zaten katılmışsınız.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Etkinliğe katılırken bir hata oluştu.";
    }
}

// Öğrenci paneline geri dönüyorum
header("Location: clubs.php");
exit;
?>