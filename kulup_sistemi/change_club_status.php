<?php
// change_club_status.php
// Kulüp durumunu güncellemek için kullanılır. Sadece admin erişebilir.

include_once 'admin_check.php';
include_once 'veritabani.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_id = intval($_POST['club_id']);
    $status = $_POST['status'];
    
    // Geçerli durum değerlerini kontrol ediyorum
    if (!in_array($status, ['approved', 'pending', 'rejected'])) {
        header("Location: manage_clubs.php?error=invalid_status");
        exit;
    }
    
    // Kulüp durumunu güncelliyorum
    $stmt = $baglanti->prepare("UPDATE clubs SET status = ? WHERE id = ?");
    $stmt->execute([$status, $club_id]);
    
    header("Location: manage_clubs.php?success=status_updated");
    exit;
} else {
    header("Location: manage_clubs.php");
    exit;
}
?>