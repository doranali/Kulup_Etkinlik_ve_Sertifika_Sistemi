<?php
// veritabani.php
// Veritabanı bağlantımızı kurduğumuz yer. Her yerden burayı dahil edip bağlantıyı kullanacağız.
// güvenlik amacıyla veritabanı bilgilerimi sildim.
$host = 'localhost';
$veritabani = '';
$kullanici = '';
$sifre = '';
$charset = 'utf8mb4';
v
try {
    // PDO ile yeni bir veritabanı bağlantısı oluşturuyorum.
    $baglanti = new PDO("mysql:host=$host;dbname=$veritabani;charset=$charset", $kullanici, $sifre);
    $baglanti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>