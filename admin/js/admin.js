jQuery(document).ready(function($) {
    // إدارة التبويبات في صفحة الإعدادات
    $('.tlap-admin-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        
        // إزالة النشاط من جميع التبويبات
        $('.tlap-admin-tabs .nav-tab').removeClass('nav-tab-active');
        
        // إضافة النشاط للتبويب المحدد
        $(this).addClass('nav-tab-active');
        
        // إخفاء جميع محتويات التبويبات
        $('.tlap-tab-content').hide();
        
        // إظهار محتوى التبويب المحدد
        var tabId = $(this).data('tab');
        $('#' + tabId).show();
    });
});