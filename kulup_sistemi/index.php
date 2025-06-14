<?php
// index.php
// Uygulamanın başlangıç noktasıdır. Oturum açmış kullanıcıları rollerine göre ilgili panellere yönlendiririm. Oturum açmamış kullanıcılar için giriş ve kayıt seçeneklerini sunarım.

session_start();

// Eğer kullanıcı zaten oturum açmışsa ve rolü belirlenmişse, ilgili panele yönlendiriyorum.
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'manager') {
        header("Location: manager_dashboard.php");
        exit;
    } else {
        header("Location: dashboard.php");
        exit;
    }
}

// Oturum açmamış kullanıcılar için sayfanın üst kısmını ekliyorum.
include_once 'header.php';
?>

<style>
.hero-section {
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 3.5rem 2rem 2.5rem 2rem;
    margin: 2.5rem auto 2rem auto;
    max-width: 900px;
    text-align: center;
    position: relative;
}
.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1.2rem;
    letter-spacing: 0.01em;
}
.hero-desc {
    font-size: 1.18rem;
    color: #2c3e50;
    margin-bottom: 2.2rem;
}
.hero-img {
    width: 100%;
    max-width: 340px;
    border-radius: 16px;
    margin: 0 auto 2rem auto;
    box-shadow: 0 2px 16px rgba(74,144,226,0.13);
}
.hero-btns {
    margin-top: 2rem;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.2rem;
}
.advantage-section {
    max-width: 1100px;
    margin: 2rem auto 0 auto;
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    justify-content: center;
}
.adv-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(74,144,226,0.07);
    padding: 2rem 1.5rem 1.5rem 1.5rem;
    min-width: 260px;
    max-width: 340px;
    flex: 1 1 260px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.adv-card img {
    width: 60px;
    height: 60px;
    margin-bottom: 1rem;
}
.adv-card-title {
    font-weight: 600;
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
}
.adv-card-desc {
    color: #555;
    font-size: 1.01rem;
}
@media (max-width: 900px) {
    .advantage-section {flex-direction:column;gap:1.2rem;align-items:stretch;}
    .hero-section {padding:2rem 0.7rem;}
}
@media (max-width: 600px) {
    .hero-title {font-size: 1.45rem;}
    .hero-desc {font-size: 1rem;}
    .hero-section {padding: 1.2rem 0.3rem;}
    .adv-card {padding: 1.2rem 0.7rem;}
    .hero-img {max-width: 98vw;}
    .hero-btns {gap: 0.7rem;}
}
</style>

<div class="hero-section">
    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80" alt="Kulüp Etkinliği" class="hero-img">
    <div class="hero-title">Kulüp Etkinlik ve Sertifika Sistemine Hoş Geldiniz!</div>
    <div class="hero-desc">
        Üniversite ve topluluk yaşamını daha aktif ve verimli hale getirmek için tasarlanmış bu platformda;<br>
        <b>kulüplerin düzenlediği etkinliklere katılabilir</b>, <b>kolayca kulüp başvurusu yapabilir</b> ve <b>kazandığınız sertifikaları tek tıkla görüntüleyebilirsiniz</b>.
    </div>
    <div class="hero-btns">
        <a href="auth_login.php" class="btn btn-success btn-lg px-5 py-2">Giriş Yap</a>
        <a href="register.php" class="btn btn-primary btn-lg px-5 py-2">Kayıt Ol</a>
    </div>
</div>

<div class="advantage-section">
    <div class="adv-card">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Topluluk">
        <div class="adv-card-title">Topluluk ve Kulüp Yönetimi</div>
        <div class="adv-card-desc">Farklı kulüplere başvur, üyeliklerini ve etkinliklerini tek panelden yönet.</div>
    </div>
    <div class="adv-card">
        <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Etkinlik">
        <div class="adv-card-title">Etkinliklere Kolay Katılım</div>
        <div class="adv-card-desc">Tüm kulüp etkinliklerini incele, ilgini çekenlere anında katıl ve geçmiş etkinliklerini takip et.</div>
    </div>
    <div class="adv-card">
        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" alt="Sertifika">
        <div class="adv-card-title">Sertifikalarını Sakla</div>
        <div class="adv-card-desc">Katıldığın etkinliklerden kazandığın tüm sertifikalara tek tıkla ulaş, PDF olarak indir.</div>
    </div>
</div>

<?php
// Sayfanın alt kısmını ekliyorum.
include_once 'footer.php';
?>