<?php
class TLAP_Registration {
    
    public function __construct() {
        // إضافة الحقول لنماذج تسجيل Tutor LMS
        add_action('tutor_student_registration_after_terms', array($this, 'add_student_registration_fields'));
        add_action('tutor_instructor_registration_after_terms', array($this, 'add_instructor_registration_fields'));
        
        // حفظ بيانات التسجيل
        add_action('user_register', array($this, 'save_registration_data'));
        add_action('profile_update', array($this, 'save_registration_data'));
        
        // إضافة السكريبتات
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function enqueue_scripts() {
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
            <h3>التصنيف الأكاديمي</h3>
            <p>اختر نوع التعليم المناسب لك:</p>
            
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
                    <label>الجامعة *</label>
                    <select name="tlap_university" class="tlap-university-select">
                        <option value="">اختر الجامعة</option>
                        <?php
                        $universities = get_terms(array(
                            'taxonomy' => 'academic_university',
                            'hide_empty' => false,
                        ));
                        foreach ($universities as $university) {
                            echo '<option value="' . $university->term_id . '">' . $university->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label>الكلية *</label>
                    <select name="tlap_faculty" class="tlap-faculty-select">
                        <option value="">اختر الكلية</option>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label>القسم *</label>
                    <select name="tlap_department" class="tlap-department-select">
                        <option value="">اختر القسم</option>
                    </select>
                </div>
            </div>
            
            <!-- حقول المدرسة -->
            <div class="tlap-fields-container tlap-school-fields" style="display: none;">
                <div class="tutor-form-group">
                    <label>المدرسة *</label>
                    <select name="tlap_school">
                        <option value="">اختر المدرسة</option>
                        <?php
                        $schools = get_terms(array(
                            'taxonomy' => 'academic_school',
                            'hide_empty' => false,
                        ));
                        foreach ($schools as $school) {
                            echo '<option value="' . $school->term_id . '">' . $school->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label>الصف الدراسي *</label>
                    <select name="tlap_grade">
                        <option value="">اختر الصف</option>
                        <option value="grade1">الصف الأول</option>
                        <option value="grade2">الصف الثاني</option>
                        <option value="grade3">الصف الثالث</option>
                        <option value="grade4">الصف الرابع</option>
                        <option value="grade5">الصف الخامس</option>
                        <option value="grade6">الصف السادس</option>
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
    
    public function save_registration_data($user_id) {
        if (isset($_POST['tlap_academic_type'])) {
            update_user_meta($user_id, 'tlap_academic_type', sanitize_text_field($_POST['tlap_academic_type']));
            
            switch ($_POST['tlap_academic_type']) {
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
}