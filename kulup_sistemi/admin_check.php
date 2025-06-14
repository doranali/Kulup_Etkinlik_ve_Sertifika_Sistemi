<?php
// admin_check.php
// Sadece admin yetkisine sahip kullanıcıların erişimini kontrol ederim.

session_start();

// Kullanıcı giriş yapmamışsa veya admin değilse giriş sayfasına yönlendiririm.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>