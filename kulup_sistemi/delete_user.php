<?php
// delete_user.php
// Admin olarak kullanıcıları silmemi sağlar.

include_once 'admin_check.php';
include_once 'veritabani.php';

// POST verilerini kontrol ediyorum
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    try {
        // Kullanıcı bir kulübün yöneticisi mi kontrol ediyorum
        $check_manager = $baglanti->prepare("SELECT id FROM clubs WHERE manager_id = ?");
        $check_manager->execute([$user_id]);
        
        if ($check_manager->rowCount() > 0) {
            $_SESSION['error_message'] = "Bu kullanıcı bir kulübün yöneticisi olduğu için silinemez. Önce kulüp yöneticiliğini kaldırın.";
        } else {
            // Kullanıcıyı silmeden önce ilişkili kayıtları siliyorum
            // Event participation kayıtlarını siliyorum
            $delete_participation = $baglanti->prepare("DELETE FROM event_participation WHERE user_id = ?");
            $delete_participation->execute([$user_id]);
            
            // Club members kayıtlarını siliyorum
            $delete_membership = $baglanti->prepare("DELETE FROM club_members WHERE user_id = ?");
            $delete_membership->execute([$user_id]);
            
            // Son olarak kullanıcıyı siliyorum
            $delete_user = $baglanti->prepare("DELETE FROM users WHERE id = ?");
            $delete_user->execute([$user_id]);
            
            $_SESSION['success_message'] = "Kullanıcı başarıyla silindi.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Kullanıcı silinirken bir hata oluştu.";
    }
}

// Kullanıcıları yönetme sayfasına geri dönüyorum
header("Location: manage_users.php");
exit;