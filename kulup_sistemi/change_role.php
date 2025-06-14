<?php
// change_role.php
// Admin olarak kullanıcı rollerini değiştirmemi sağlar.

include_once 'admin_check.php';
include_once 'veritabani.php';

// POST verilerini kontrol ediyorum
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    // Geçerli roller
    $valid_roles = ['student', 'manager', 'admin'];
    
    // Rol geçerli mi kontrol ediyorum
    if (in_array($new_role, $valid_roles)) {
        try {
            // Kullanıcının rolünü güncelliyorum
            $update = $baglanti->prepare("UPDATE users SET role = ? WHERE id = ?");
            $update->execute([$new_role, $user_id]);
            
            // Başarılı mesajı ile yönlendirme
            $_SESSION['success_message'] = "Kullanıcı rolü başarıyla güncellendi.";
        } catch (PDOException $e) {
            // Hata mesajı ile yönlendirme
            $_SESSION['error_message'] = "Rol güncellenirken bir hata oluştu.";
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz rol seçimi.";
    }
}

// Kullanıcıları yönetme sayfasına geri dönüyorum
header("Location: manage_users.php");
exit;