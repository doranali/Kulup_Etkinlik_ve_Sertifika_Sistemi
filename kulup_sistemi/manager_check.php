<?php
// manager_check.php
// Sadece 'manager' (kulüp yöneticisi) rolüne sahip kullanıcıların erişebilmesini sağlar.

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: auth_login.php");
    exit;
}
?>