jQuery(document).ready(function($) {
    // تغيير نوع التصنيف الأكاديمي
    $('.tlap-type-radio').on('change', function() {
        var selectedType = $(this).val();
        
        // إخفاء جميع الحقول
        $('.tlap-fields-container').hide();
        
        // إظهار الحقول المناسبة
        if (selectedType === 'university') {
            $('.tlap-university-fields').show();
        } else if (selectedType === 'school') {
            $('.tlap-school-fields').show();
        } else if (selectedType === 'general') {
            $('.tlap-general-fields').show();
        }
    });
    
    // عند تغيير الجامعة - تحميل الكليات
    $('.tlap-university-select').on('change', function() {
        var universityId = $(this).val();
        var facultySelect = $('.tlap-faculty-select');
        var departmentSelect = $('.tlap-department-select');
        
        // تفريغ القوائم التابعة
        facultySelect.html('<option value="">اختر الكلية</option>');
        departmentSelect.html('<option value="">اختر القسم</option>');
        
        if (universityId) {
            // إظهار تحميل
            facultySelect.prop('disabled', true);
            
            $.ajax({
                url: tlap_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'tlap_get_faculties',
                    university_id: universityId,
                    nonce: tlap_ajax.nonce
                },
                success: function(response) {
                    facultySelect.prop('disabled', false);
                    
                    if (response.success) {
                        $.each(response.data, function(index, faculty) {
                            facultySelect.append('<option value="' + faculty.term_id + '">' + faculty.name + '</option>');
                        });
                    }
                }
            });
        }
    });
    
    // عند تغيير الكلية - تحميل الأقسام
    $('.tlap-faculty-select').on('change', function() {
        var facultyId = $(this).val();
        var departmentSelect = $('.tlap-department-select');
        
        // تفريغ قائمة الأقسام
        departmentSelect.html('<option value="">اختر القسم</option>');
        
        if (facultyId) {
            // إظهار تحميل
            departmentSelect.prop('disabled', true);
            
            $.ajax({
                url: tlap_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'tlap_get_departments',
                    faculty_id: facultyId,
                    nonce: tlap_ajax.nonce
                },
                success: function(response) {
                    departmentSelect.prop('disabled', false);
                    
                    if (response.success) {
                        $.each(response.data, function(index, department) {
                            departmentSelect.append('<option value="' + department.term_id + '">' + department.name + '</option>');
                        });
                    }
                }
            });
        }
    });
});