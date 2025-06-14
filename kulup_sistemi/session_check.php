<?php
// session_check.php
// Sadece sisteme giriş yapmış (oturum açmış) kullanıcıların sayfayı görmesini sağlar.

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth_login.php");
    exit;
}
?>