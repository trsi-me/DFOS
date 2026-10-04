/**
 * DFOS - Digital Food Ordering System
 * السكريبتات الرئيسية
 */

// تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // إضافة حركة بسيطة للشعار عند التحميل
    var logo = document.querySelector('.site-logo');
    if (logo && logo.style.display !== 'none') {
        logo.style.opacity = '0';
        logo.style.transform = 'scale(0.95)';
        setTimeout(function() {
            logo.style.transition = 'opacity 0.3s, transform 0.3s';
            logo.style.opacity = '1';
            logo.style.transform = 'scale(1)';
        }, 100);
    }
});
