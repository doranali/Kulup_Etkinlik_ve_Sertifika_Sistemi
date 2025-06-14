<?php
// edit_profile.php
// Giriş yapmış kullanıcının kendi profil bilgilerini (ad, soyad, e-posta, şifre) düzenlemesini sağlar.

include_once 'session_check.php';
include_once 'veritabani.php';
include_once 'header.php';

$mesaj = "";
$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini veritabanından çekiyorum
$sorgu = $baglanti->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$sorgu->execute([$user_id]);
$kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    echo '<div class="alert alert-danger">Kullanıcı bilgileri bulunamadı.</div>';
    include_once 'footer.php';
    exit;
}

// Form gönderildiyse bilgileri güncelliyorum
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (!empty($first_name) && !empty($last_name) && !empty($email)) {
        $guncelle_sorgu = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
        $parametreler = [$first_name, $last_name, $email, $user_id];

        // E-posta değişikliği varsa, e-postanın zaten kayıtlı olup olmadığını kontrol ediyorum
        if ($email !== $kullanici['email']) {
            $email_kontrol_sorgu = $baglanti->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $email_kontrol_sorgu->execute([$email, $user_id]);
            if ($email_kontrol_sorgu->rowCount() > 0) {
                $mesaj = "Bu e-posta adresi zaten başka bir kullanıcı tarafından kullanılıyor.";
            }
        }

        // E-posta kontrolü başarılıysa veya e-posta değişmediyse devam ediyorum
        if (empty($mesaj)) {
            // Şifre alanı boş değilse ve iki şifre birbiriyle eşleşiyorsa şifreyi güncelliyorum
            if (!empty($password)) {
                if ($password === $password_confirm) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $guncelle_sorgu = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE id = ?";
                    $parametreler = [$first_name, $last_name, $email, $hashed_password, $user_id];
                } else {
                    $mesaj = "Yeni şifreler birbiriyle eşleşmiyor.";
                }
            }

            // Şifre kontrolü başarılıysa veya şifre değişmediyse veritabanı güncellemesini yapıyorum
            if (empty($mesaj)) {
                $stmt = $baglanti->prepare($guncelle_sorgu);
                if ($stmt->execute($parametreler)) {
                    // Oturum bilgilerini de güncelliyorum
                    $_SESSION['ad'] = $first_name;
                    $_SESSION['soyad'] = $last_name;
                    $_SESSION['email'] = $email;
                    $mesaj = "Profil bilgileriniz başarıyla güncellendi!";
                    $sorgu->execute([$user_id]);
                    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);
                } else {
                    $mesaj = "Profil güncellenirken bir hata oluştu.";
                }
            }
        }
    } else {
        $mesaj = "Lütfen tüm zorunlu alanları doldurun (Ad, Soyad, E-posta).";
    }
}
?>

<style>
.profile-card {max-width:420px;margin:2.5rem auto;background:linear-gradient(135deg,#f8fafc 0%,#e3e9f7 100%);border-radius:18px;box-shadow:0 4px 24px rgba(74,144,226,0.10);padding:2.2rem 1.5rem;animation:fadeInUp 0.7s forwards;opacity:0;transform:translateY(30px);}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0);}}
.profile-card h2{font-weight:700;letter-spacing:0.01em;margin-bottom:1.5rem;}
.profile-card label{font-weight:500;color:#2c3e50;}
.profile-card .form-control{padding:1rem 1.2rem;font-size:1.05rem;border-radius:10px;}
.profile-card .btn-primary{background:#4a90e2;border:none;border-radius:8px;padding:0.7rem 1.5rem;font-weight:500;font-size:1rem;transition:background 0.2s,box-shadow 0.2s;box-shadow:0 2px 8px rgba(74,144,226,0.08);}
.profile-card .btn-primary:hover{background:#357ab8;}
.profile-card .btn-secondary{border-radius:8px;padding:0.7rem 1.5rem;font-size:1rem;}
.profile-card hr{margin:2rem 0 1.2rem 0;}
@media (max-width:600px){.profile-card{padding:1.2rem 0.7rem;}}
</style>
<div class="container fade-in">
    <div class="profile-card">
        <h2>Profil Düzenle</h2>
        <?php if (!empty($mesaj)): ?>
            <div class="alert alert-<?php echo strpos($mesaj, 'başarıyla') !== false ? 'success' : 'danger'; ?> text-center" style="border-radius:12px;"><?php echo htmlspecialchars($mesaj); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="first_name">Ad</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($kullanici['first_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name">Soyad</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($kullanici['last_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($kullanici['email']); ?>" required>
            </div>
            <hr>
            <div class="mb-3">
                <label for="password">Yeni Şifre (İsteğe Bağlı)</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifrenizi değiştirmek istemiyorsanız boş bırakın">
            </div>
            <div class="mb-3">
                <label for="password_confirm">Yeni Şifre Tekrar</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Yeni şifrenizi tekrar girin">
            </div>
            <button type="submit" class="btn btn-primary">Güncelle</button>
            <a href="<?php echo $_SESSION['role']; ?>_dashboard.php" class="btn btn-secondary ms-2">Panele Dön</a>
        </form>
    </div>
</div>

<?php
// Sayfanın alt kısmını ekliyorum
include_once 'footer.php';
?>