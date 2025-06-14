<?php
// delete_club.php
// Admin olarak kulüpleri silmemi sağlar.

include_once 'admin_check.php';
include_once 'veritabani.php';

// POST verilerini kontrol ediyorum
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['club_id'])) {
    $club_id = $_POST['club_id'];
    
    try {
        // Kulübe ait tüm kayıtları siliyorum
        // Event participation kayıtlarını siliyorum
        $delete_participation = $baglanti->prepare("DELETE FROM event_participation WHERE event_id IN (SELECT id FROM events WHERE club_id = ?)");
        $delete_participation->execute([$club_id]);
        
        // Events kayıtlarını siliyorum
        $delete_events = $baglanti->prepare("DELETE FROM events WHERE club_id = ?");
        $delete_events->execute([$club_id]);
        
        // Club members kayıtlarını siliyorum
        $delete_members = $baglanti->prepare("DELETE FROM club_members WHERE club_id = ?");
        $delete_members->execute([$club_id]);
        
        // Son olarak kulübü siliyorum
        $delete_club = $baglanti->prepare("DELETE FROM clubs WHERE id = ?");
        $delete_club->execute([$club_id]);
        
        $_SESSION['success_message'] = "Kulüp başarıyla silindi.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Kulüp silinirken bir hata oluştu.";
    }
}

// Kulüpleri yönetme sayfasına geri dönüyorum
header("Location: manage_clubs.php");
exit;