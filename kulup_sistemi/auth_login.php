<?php
// auth_login.php
// Kullanıcıların giriş yapmasını sağlar ve rolüne göre yönlendiririm.

// Veritabanı bağlantımı dahil ediyorum.
// İki klasör yukarı çıkıp 'config' klasörüne gidiyorum.
include_once 'veritabani.php';
// Oturum başlatıyorum.
session_start();

$mesaj = ""; // Kullanıcıya gösterilecek mesaj değişkeni

// Eğer form POST metodu ile gönderildiyse
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // E-posta ve şifre bilgilerini alıyorum, boşlukları temizliyorum.
    $email = trim($_POST['email']);
    $sifre = $_POST['sifre'];

    // E-posta ve şifre alanlarının boş olup olmadığını kontrol ediyorum.
    if (!empty($email) && !empty($sifre)) {
        // Kullanıcıyı e-postasına göre veritabanında arıyorum.
        $sorgu = $baglanti->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
        $sorgu->execute([$email]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC); // Kullanıcı bilgilerini ilişkisel dizi olarak çekiyorum.

        // Kullanıcı bulunduysa ve şifre doğruysa
        if ($kullanici && password_verify($sifre, $kullanici['password'])) {
            // Oturum değişkenlerini belirliyorum.
            $_SESSION['user_id'] = $kullanici['id'];
            $_SESSION['role'] = $kullanici['role'];
            $_SESSION['ad'] = $kullanici['first_name'];
            $_SESSION['soyad'] = $kullanici['last_name'];

            // Kullanıcının rolüne göre uygun panele yönlendiriyorum.
            if ($kullanici['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($kullanici['role'] == 'manager') {
                header("Location: manager_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit; // Yönlendirmeden sonra kodun çalışmasını durduruyorum.
        } else {
            $mesaj = "E-posta veya şifre yanlış!"; // Hatalı giriş mesajı
        }
    } else {
        $mesaj = "Tüm alanları doldurun."; // Boş alanlar için mesaj
    }
}

// Sayfanın üst kısmını (HTML head, navigasyon vb.) dahil ediyorum.
include_once 'header.php';
?>

<style>
.login-container {
    max-width: 420px;
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

.login-container h3 {
    font-weight: 700;
    letter-spacing: 0.01em;
    margin-bottom: 1.5rem;
    text-align: center;
}

.login-container label {
    font-weight: 500;
    color: #2c3e50;
}

.login-container .form-control {
    padding: 1rem 1.2rem;
    font-size: 1.05rem;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.1);
    background: rgba(255,255,255,0.9);
}

.login-container .form-control:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74,144,226,0.15);
}

.login-container .btn-success {
    width: 100%;
    padding: 0.8rem;
    font-weight: 500;
    font-size: 1.05rem;
    border-radius: 10px;
    background: #4a90e2;
    border: none;
    transition: all 0.3s ease;
}

.login-container .btn-success:hover {
    background: #357abd;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(74,144,226,0.2);
}

.login-container p {
    text-align: center;
    margin-top: 1.5rem;
}

.login-container a {
    color: #4a90e2;
    text-decoration: none;
    transition: color 0.3s ease;
}

.login-container a:hover {
    color: #357abd;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .login-container {
        margin: 1rem;
        padding: 1.5rem 1rem;
    }
}
</style>

<div class="container">
    <div class="login-container">
        <h3>Giriş Yap</h3>
        <?php 
        if (!empty($mesaj)): 
        ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($mesaj); ?></div>
        <?php 
        endif; 
        ?>
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">E-posta</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="sifre" class="form-label">Şifre</label>
                <input type="password" name="sifre" id="sifre" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Giriş Yap</button>
        </form>
        <p>
            <a href="register.php">Hesabınız yok mu? Kayıt Olun</a>
        </p>
    </div>
</div>

<?php 
// Sayfanın alt kısmını (JavaScript, kapanış etiketleri vb.) dahil ediyorum.
include_once 'footer.php'; 
?>