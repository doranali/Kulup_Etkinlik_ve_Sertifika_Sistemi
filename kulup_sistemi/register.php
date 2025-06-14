<?php
// register.php
// Yeni kullanıcıların sisteme kayıt olmasını sağlar ve kayıt sonrası giriş sayfasına yönlendirir.

include_once 'veritabani.php';
session_start();

$mesaj = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = trim($_POST['ad']);
    $soyad = trim($_POST['soyad']);
    $email = trim($_POST['email']);
    $sifre = $_POST['sifre'];

    if (!empty($ad) && !empty($soyad) && !empty($email) && !empty($sifre)) {
        $sorgu = $baglanti->prepare("SELECT id FROM users WHERE email = ?");
        $sorgu->execute([$email]);
        if ($sorgu->rowCount() > 0) {
            $mesaj = "Bu e-posta ile daha önce kayıt olunmuş.";
        } else {
            $hashliSifre = password_hash($sifre, PASSWORD_DEFAULT);
            $ekle = $baglanti->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $ekle->execute([$ad, $soyad, $email, $hashliSifre]);
            $mesaj = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
            // header("Location: login.php");
            // exit;
        }
    } else {
        $mesaj = "Tüm alanları doldurun.";
    }
}

include_once 'header.php';
?>

<style>
.register-container {
    max-width: 800px;
    margin: 2.5rem auto;
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 2.2rem 1.5rem;
    animation: fadeInUp 0.7s forwards;
    opacity: 0;
    transform: translateY(30px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.register-container h3 {
    font-weight: 700;
    letter-spacing: 0.01em;
    margin-bottom: 1.5rem;
    text-align: center;
}

.register-container label {
    font-weight: 500;
    color: #2c3e50;
}

.register-container .form-control {
    padding: 1rem 1.2rem;
    font-size: 1.05rem;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.1);
    background: rgba(255,255,255,0.9);
}

.register-container .form-control:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74,144,226,0.15);
}
</style>

<div class="register-container">
    <h3>Kayıt Ol</h3>
    <?php if (!empty($mesaj)): ?>
        <div class="alert alert-<?php echo strpos($mesaj, 'başarılı') !== false ? 'success' : 'danger'; ?>"><?php echo htmlspecialchars($mesaj); ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Ad</label>
                <input type="text" name="ad" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Soyad</label>
                <input type="text" name="soyad" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label>E-posta</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Şifre</label>
            <input type="password" name="sifre" class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Kayıt Ol</button>
        </div>
    </form>
</div>

<p class="mt-3">
    <a href="auth_login.php">Zaten bir hesabınız var mı? Giriş Yapın</a>
</p>

<?php 
include_once 'footer.php'; 
?>