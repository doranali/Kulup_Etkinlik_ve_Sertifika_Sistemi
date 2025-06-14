// script.js
// Sayfadaki etkileşimli özellikler ve dinamik davranışlar için JavaScript kodlarımı buraya yazacağım.

document.addEventListener('DOMContentLoaded', function() {
    // Otomatik kapanacak alert kutularını buluyorum.
    const alerts = document.querySelectorAll('.alert[data-autoclose]');

    alerts.forEach(function(alert) {
        const autoCloseTime = parseInt(alert.getAttribute('data-autoclose'));
        if (!isNaN(autoCloseTime) && autoCloseTime > 0) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease-out';
                setTimeout(function() {
                    alert.style.display = 'none';
                    alert.classList.remove('show');
                    alert.classList.add('d-none');
                }, 500);
            }, autoCloseTime);
        }
    });

    // Buraya başka JavaScript kodları da ekleyebilirim.
});