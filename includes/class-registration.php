<?php
class TLAP_Registration {
    
    public function __construct() {
        // إضافة الحقول لنماذج تسجيل Tutor LMS باستخدام الهوكات الصحيحة
        add_action('tutor_student_reg_form_after', array($this, 'add_student_registration_fields'));
        add_action('tutor_instructor_reg_form_after', array($this, 'add_instructor_registration_fields'));
        
        // التحقق من البيانات قبل التسجيل
        add_filter('tutor_student_registration_errors', array($this, 'validate_student_fields'));
        add_filter('tutor_instructor_registration_errors', array($this, 'validate_instructor_fields'));
        
        // حفظ بيانات التسجيل
        add_action('user_register', array($this, 'save_registration_data'));
        add_action('profile_update', array($this, 'save_registration_data'));
        
        // إضافة السكريبتات
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // إضافة AJAX handlers للكليات والأقسام
        add_action('wp_ajax_tlap_get_faculties', array($this, 'get_faculties_ajax'));
        add_action('wp_ajax_nopriv_tlap_get_faculties', array($this, 'get_faculties_ajax'));
        add_action('wp_ajax_tlap_get_departments', array($this, 'get_departments_ajax'));
        add_action('wp_ajax_nopriv_tlap_get_departments', array($this, 'get_departments_ajax'));
    }
    
    public function enqueue_scripts() {
        // تحميل السكريبتات في صفحات التسجيل فقط
        if (is_page() && (has_shortcode(get_post()->post_content, 'tutor_student_registration_form') || 
            has_shortcode(get_post()->post_content, 'tutor_instructor_registration_form'))) {
            
            wp_enqueue_style('tlap-public-css', TLAP_PLUGIN_URL . 'public/css/public.css', array(), TLAP_PLUGIN_VERSION);
            wp_enqueue_script('tlap-public-js', TLAP_PLUGIN_URL . 'public/js/public.js', array('jquery'), TLAP_PLUGIN_VERSION, true);
            
            wp_localize_script('tlap-public-js', 'tlap_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('tlap_nonce')
            ));
        }
    }
    
    public function add_student_registration_fields() {
        $this->render_registration_fields('student');
    }
    
    public function add_instructor_registration_fields() {
        $this->render_registration_fields('instructor');
    }
    
    private function render_registration_fields($type) {
        ?>
        <div class="tutor-form-group tlap-registration-section">
            <h3 class="tlap-section-title">التصنيف الأكاديمي</h3>
            <p class="tlap-section-description">اختر نوع التعليم المناسب لك:</p>
            
            <div class="tlap-type-selector">
                <div class="tlap-radio-group">
                    <label class="tlap-radio-label">
                        <input type="radio" name="tlap_academic_type" value="university" class="tlap-type-radio">
                        <span class="tlap-radio-text">جامعة</span>
                    </label>
                    
                    <label class="tlap-radio-label">
                        <input type="radio" name="tlap_academic_type" value="school" class="tlap-type-radio">
                        <span class="tlap-radio-text">مدرسة</span>
                    </label>
                    
                    <label class="tlap-radio-label">
                        <input type="radio" name="tlap_academic_type" value="general" class="tlap-type-radio">
                        <span class="tlap-radio-text">كورسات عامة</span>
                    </label>
                </div>
            </div>
            
            <!-- حقول الجامعة -->
            <div class="tlap-fields-container tlap-university-fields" style="display: none;">
                <div class="tutor-form-group">
                    <label for="tlap_university">الجامعة *</label>
                    <select name="tlap_university" id="tlap_university" class="tlap-university-select tutor-form-control">
                        <option value="">اختر الجامعة</option>
                        <?php
                        $universities = get_terms(array(
                            'taxonomy' => 'academic_university',
                            'hide_empty' => false,
                        ));
                        foreach ($universities as $university) {
                            echo '<option value="' . esc_attr($university->term_id) . '">' . esc_html($university->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label for="tlap_faculty">الكلية *</label>
                    <select name="tlap_faculty" id="tlap_faculty" class="tlap-faculty-select tutor-form-control">
                        <option value="">اختر الكلية</option>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label for="tlap_department">القسم *</label>
                    <select name="tlap_department" id="tlap_department" class="tlap-department-select tutor-form-control">
                        <option value="">اختر القسم</option>
                    </select>
                </div>
            </div>
            
            <!-- حقول المدرسة -->
            <div class="tlap-fields-container tlap-school-fields" style="display: none;">
                <div class="tutor-form-group">
                    <label for="tlap_school">المدرسة *</label>
                    <select name="tlap_school" id="tlap_school" class="tutor-form-control">
                        <option value="">اختر المدرسة</option>
                        <?php
                        $schools = get_terms(array(
                            'taxonomy' => 'academic_school',
                            'hide_empty' => false,
                        ));
                        foreach ($schools as $school) {
                            echo '<option value="' . esc_attr($school->term_id) . '">' . esc_html($school->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label for="tlap_grade">الصف الدراسي *</label>
                    <select name="tlap_grade" id="tlap_grade" class="tutor-form-control">
                        <option value="">اختر الصف</option>
                        <option value="grade1">الصف الأول</option>
                        <option value="grade2">الصف الثاني</option>
                        <option value="grade3">الصف الثالث</option>
                        <option value="grade4">الصف الرابع</option>
                        <option value="grade5">الصف الخامس</option>
                        <option value="grade6">الصف السادس</option>
                        <option value="grade7">الصف السابع</option>
                        <option value="grade8">الصف الثامن</option>
                        <option value="grade9">الصف التاسع</option>
                        <option value="grade10">الصف العاشر</option>
                        <option value="grade11">الصف الحادي عشر</option>
                        <option value="grade12">الصف الثاني عشر</option>
                    </select>
                </div>
            </div>
            
            <!-- رسالة الكورسات العامة -->
            <div class="tlap-fields-container tlap-general-fields" style="display: none;">
                <div class="tutor-alert tutor-success">
                    <p>ستتمكن من الوصول إلى جميع الكورسات العامة المتاحة.</p>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function validate_student_fields($errors) {
        return $this->validate_fields($errors, 'student');
    }
    
    public function validate_instructor_fields($errors) {
        return $this->validate_fields($errors, 'instructor');
    }
    
    private function validate_fields($errors, $type) {
        if (!isset($_POST['tlap_academic_type']) || empty($_POST['tlap_academic_type'])) {
            $errors->add('tlap_academic_type', 'يرجى اختيار نوع التعليم.');
            return $errors;
        }
        
        $academic_type = sanitize_text_field($_POST['tlap_academic_type']);
        
        switch ($academic_type) {
            case 'university':
                if (!isset($_POST['tlap_university']) || empty($_POST['tlap_university'])) {
                    $errors->add('tlap_university', 'يرجى اختيار الجامعة.');
                }
                if (!isset($_POST['tlap_faculty']) || empty($_POST['tlap_faculty'])) {
                    $errors->add('tlap_faculty', 'يرجى اختيار الكلية.');
                }
                if (!isset($_POST['tlap_department']) || empty($_POST['tlap_department'])) {
                    $errors->add('tlap_department', 'يرجى اختيار القسم.');
                }
                break;
                
            case 'school':
                if (!isset($_POST['tlap_school']) || empty($_POST['tlap_school'])) {
                    $errors->add('tlap_school', 'يرجى اختيار المدرسة.');
                }
                if (!isset($_POST['tlap_grade']) || empty($_POST['tlap_grade'])) {
                    $errors->add('tlap_grade', 'يرجى اختيار الصف الدراسي.');
                }
                break;
                
            case 'general':
                // لا توجد حقول إضافية للتحقق منها
                break;
        }
        
        return $errors;
    }
    
    public function save_registration_data($user_id) {
        if (isset($_POST['tlap_academic_type'])) {
            $academic_type = sanitize_text_field($_POST['tlap_academic_type']);
            update_user_meta($user_id, 'tlap_academic_type', $academic_type);
            
            switch ($academic_type) {
                case 'university':
                    if (isset($_POST['tlap_university'])) {
                        update_user_meta($user_id, 'tlap_university', intval($_POST['tlap_university']));
                    }
                    if (isset($_POST['tlap_faculty'])) {
                        update_user_meta($user_id, 'tlap_faculty', intval($_POST['tlap_faculty']));
                    }
                    if (isset($_POST['tlap_department'])) {
                        update_user_meta($user_id, 'tlap_department', intval($_POST['tlap_department']));
                    }
                    break;
                    
                case 'school':
                    if (isset($_POST['tlap_school'])) {
                        update_user_meta($user_id, 'tlap_school', intval($_POST['tlap_school']));
                    }
                    if (isset($_POST['tlap_grade'])) {
                        update_user_meta($user_id, 'tlap_grade', sanitize_text_field($_POST['tlap_grade']));
                    }
                    break;
                    
                case 'general':
                    // لا توجد حقول إضافية للكورسات العامة
                    break;
            }
        }
    }
    
    // AJAX handlers للكليات والأقسام
    public function get_faculties_ajax() {
        check_ajax_referer('tlap_nonce', 'nonce');
        
        $university_id = intval($_POST['university_id']);
        $faculties = array();
        
        if ($university_id) {
            // هنا يمكنك إضافة منطق لجلب الكليات المرتبطة بالجامعة
            // حالياً سنرجع جميع الكليات كمثال
            $faculties = get_terms(array(
                'taxonomy' => 'academic_faculty',
                'hide_empty' => false,
            ));
        }
        
        wp_send_json_success($faculties);
    }
    
    public function get_departments_ajax() {
        check_ajax_referer('tlap_nonce', 'nonce');
        
        $faculty_id = intval($_POST['faculty_id']);
        $departments = array();
        
        if ($faculty_id) {
            // هنا يمكنك إضافة منطق لجلب الأقسام المرتبطة بالكلية
            // حالياً سنرجع جميع الأقسام كمثال
            $departments = get_terms(array(
                'taxonomy' => 'academic_department',
                'hide_empty' => false,
            ));
        }
        
        wp_send_json_success($departments);
    }
}