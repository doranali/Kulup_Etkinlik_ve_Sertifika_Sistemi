<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulüp Etkinlik Sistemi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="/kulup_sistemi/public/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-users me-2"></i>
                Kulüp Etkinlik Sistemi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="edit_profile.php">
                                <i class="fas fa-user me-1"></i>
                                Profilim
                            </a>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_dashboard.php">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Yönetici Paneli
                                </a>
                            </li>
                        <?php elseif ($_SESSION['role'] === 'manager'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="manager_dashboard.php">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Kulüp Yönetimi
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="certificates.php">
                                <i class="fas fa-certificate me-1"></i>
                                Sertifikalarım
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Çıkış Yap
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth_login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Giriş Yap
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>
                                Kayıt Ol
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Ana İçerik -->
    <main class="py-4">