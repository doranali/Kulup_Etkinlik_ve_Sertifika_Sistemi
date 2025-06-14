<?php
// upload_certificate.php
// Kulüp yöneticisinin belirli bir etkinliğe katılan öğrencilere sertifika yüklemesini sağlar.

include_once 'manager_check.php';
include_once 'veritabani.php';
include_once 'header.php';

$message = "";

// Yöneticinin yönettiği kulübü bul
$kulupSorgu = $baglanti->prepare("SELECT id, name FROM clubs WHERE manager_id = ?");
$kulupSorgu->execute([$_SESSION['user_id']]);
$kulubum = $kulupSorgu->fetch(PDO::FETCH_ASSOC);

if (!$kulubum) {
    echo '<div class="alert alert-warning">Yetkili olduğunuz bir kulüp bulunamadı. Lütfen bir kulüp oluşturduğunuzdan veya atandığınızdan emin olun.</div>';
    include_once 'footer.php';
    exit;
}

$club_id = $kulubum['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $user_ids = isset($_POST['user_id']) ? $_POST['user_id'] : [];
    $issued_date = date('Y-m-d');
    $message_type = '';
    $message_content = '';

    if ($event_id <= 0 || empty($user_ids)) {
        $message_type = 'danger';
        $message_content = "Geçersiz etkinlik veya öğrenci seçimi.";
    } elseif (!isset($_FILES['certificate_file']) || $_FILES['certificate_file']['error'] !== UPLOAD_ERR_OK) {
        $message_type = 'danger';
        $message_content = "Sertifika dosyası yüklenirken bir hata oluştu: " . $_FILES['certificate_file']['error'];
    } else {
        $file = $_FILES['certificate_file'];
        $fileName = basename($file['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf'];
        $maxFileSize = 5 * 1024 * 1024;
        if (!in_array($fileExt, $allowedExtensions)) {
            $message_type = 'danger';
            $message_content = "Sadece PDF formatında sertifika yükleyebilirsiniz.";
        } elseif ($file['size'] > $maxFileSize) {
            $message_type = 'danger';
            $message_content = "Dosya boyutu 5MB'ı aşamaz.";
        } else {
            $newFileName = uniqid('cert_', true) . '.' . $fileExt;
            $uploadPath = 'certificates/' . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $successCount = 0;
                $failCount = 0;
                foreach ($user_ids as $user_id_to_certify) {
                    $insertCertSorgu = $baglanti->prepare(
                        "INSERT INTO certificates (user_id, event_id, file_path, issued_date) VALUES (?, ?, ?, ?)"
                    );
                    if ($insertCertSorgu->execute([intval($user_id_to_certify), $event_id, $newFileName, $issued_date])) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                }
                if ($successCount > 0) {
                    $message_type = 'success';
                    $message_content = "$successCount öğrenciye sertifika başarıyla yüklendi.";
                } else {
                    $message_type = 'danger';
                    $message_content = "Sertifika yüklenemedi.";
                }
            } else {
                $message_type = 'danger';
                $message_content = "Dosya sunucuya taşınırken bir hata oluştu.";
            }
        }
    }
}

$eventsSorgu = $baglanti->prepare("SELECT id, title FROM events WHERE club_id = ? ORDER BY event_date DESC");
$eventsSorgu->execute([$club_id]);
$events = $eventsSorgu->fetchAll(PDO::FETCH_ASSOC);

$studentsSorgu = $baglanti->prepare(
    "SELECT DISTINCT U.id, U.first_name, U.last_name, U.email, E.title AS event_title, E.id AS event_id
     FROM users U
     JOIN event_participation EP ON U.id = EP.user_id
     JOIN events E ON EP.event_id = E.id
     WHERE E.club_id = ?"
);
$studentsSorgu->execute([$club_id]);
$participatingStudents = $studentsSorgu->fetchAll(PDO::FETCH_ASSOC);

$groupedStudents = [];
foreach ($participatingStudents as $student) {
    if (!isset($groupedStudents[$student['event_id']])) {
        $groupedStudents[$student['event_id']] = [];
    }
    $groupedStudents[$student['event_id']][] = $student;
}
?>

<style>
.certificate-upload-container {
    max-width: 700px;
    margin: 2.5rem auto;
    background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(74,144,226,0.10);
    padding: 2.5rem 2rem 2rem 2rem;
    animation: fadeInUp 0.7s forwards;
    opacity: 0;
    transform: translateY(30px);
}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0);}}
.certificate-upload-container h3 {
    font-weight: 700;
    letter-spacing: 0.01em;
    margin-bottom: 1.5rem;
    text-align: center;
}
.certificate-upload-container label {
    font-weight: 500;
    color: #2c3e50;
}
.certificate-upload-container .form-control, .certificate-upload-container .form-select {
    padding: 1rem 1.2rem;
    font-size: 1.05rem;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.1);
    background: rgba(255,255,255,0.9);
}
.certificate-upload-container .form-control:focus, .certificate-upload-container .form-select:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74,144,226,0.15);
}
.certificate-upload-container .btn-primary {
    width: 100%;
    padding: 0.8rem;
    font-weight: 500;
    font-size: 1.05rem;
    border-radius: 10px;
    background: #4a90e2;
    border: none;
    transition: all 0.3s ease;
}
.certificate-upload-container .btn-primary:hover {
    background: #357abd;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(74,144,226,0.2);
}
.certificate-upload-container .btn-secondary {
    border-radius: 10px;
    width: 100%;
    margin-top: 0.7rem;
}
@media (max-width: 800px) {
    .certificate-upload-container {max-width:98vw;padding:1.2rem 0.5rem;}
}
@media (max-width: 480px) {
    .certificate-upload-container h3 {font-size: 1.2rem;}
}
</style>

<div class="certificate-upload-container">
<h3 class="mb-4">Sertifika Yükle - <?php echo htmlspecialchars($kulubum['name']); ?></h3>
<?php
if (isset($message_type) && isset($message_content) && $message_content) {
    echo '<div class="alert alert-' . $message_type . ' text-center" style="border-radius:12px;">' . htmlspecialchars($message_content) . '</div>';
}
?>
<?php if (count($events) == 0): ?>
    <div class="alert alert-info">Kulübünüz için henüz oluşturulmuş bir etkinlik bulunmamaktadır. Sertifika yükleyebilmek için önce etkinlik oluşturmalısınız.</div>
    <p><a href="create_event.php" class="btn btn-primary">Etkinlik Oluştur</a></p>
<?php else: ?>
    <form action="upload_certificate.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="event_id" class="form-label">Etkinlik Seçin:</label>
            <select class="form-select" id="event_id" name="event_id" required>
                <option value="">Lütfen bir etkinlik seçin</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?php echo htmlspecialchars($event['id']); ?>"><?php echo htmlspecialchars($event['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="user_id" class="form-label">Sertifika Yüklenecek Öğrencileri Seçin:</label>
            <select class="form-select" id="user_id" name="user_id[]" required multiple size="6" disabled>
                <option value="">Önce bir etkinlik seçin</option>
            </select>
            <small class="form-text text-muted">Birden fazla öğrenci seçebilirsiniz. (Ctrl veya Shift ile)</small>
        </div>

        <div class="mb-3">
            <label for="certificate_file" class="form-label">Sertifika Dosyası (PDF):</label>
            <input type="file" class="form-control" id="certificate_file" name="certificate_file" accept=".pdf" required>
            <small class="form-text text-muted">Sadece PDF dosyaları (max 5MB) kabul edilmektedir.</small>
        </div>

        <button type="submit" class="btn btn-primary">Sertifika Yükle</button>
    </form>
<?php endif; ?>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Panele Dön</a>
</div>

<?php include_once 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventSelect = document.getElementById('event_id');
    const userSelect = document.getElementById('user_id');
    const groupedStudents = <?php echo json_encode($groupedStudents); ?>;

    eventSelect.addEventListener('change', function() {
        const selectedEventId = this.value;
        userSelect.innerHTML = '';
        if (selectedEventId && groupedStudents[selectedEventId]) {
            groupedStudents[selectedEventId].forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.first_name + ' ' + student.last_name + ' (' + student.email + ')';
                userSelect.appendChild(option);
            });
            userSelect.disabled = false;
        } else {
            userSelect.disabled = true;
            userSelect.innerHTML = '<option value="">Önce bir etkinlik seçin</option>';
        }
    });
});
</script>