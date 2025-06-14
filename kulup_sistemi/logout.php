<?php
// logout.php
// Kullanıcının oturumunu güvenli bir şekilde kapatır ve giriş sayfasına yönlendiririm.

session_start();

// Oturumdaki tüm değişkenleri siliyorum
session_unset();

// Oturum çerezini de siliyorum
session_destroy();

// Kullanıcıyı giriş sayfasına yönlendiriyorum
header("Location: auth_login.php");
exit;
?>