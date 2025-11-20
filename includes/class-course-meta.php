<?php
class TLAP_Course_Meta {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_course_meta_box'));
        add_action('save_post', array($this, 'save_course_meta'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function enqueue_admin_scripts($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        global $post;
        if ($post && $post->post_type === 'courses') {
            wp_enqueue_script('tlap-course-meta-js', TLAP_PLUGIN_URL . 'admin/js/course-meta.js', array('jquery'), TLAP_PLUGIN_VERSION, true);
            
            wp_localize_script('tlap-course-meta-js', 'tlap_course_meta', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('tlap_nonce')
            ));
        }
    }
    
    public function add_course_meta_box() {
        add_meta_box(
            'tlap-course-meta',
            'التصنيف الأكاديمي للكورس',
            array($this, 'render_course_meta_box'),
            'courses',
            'side',
            'high'
        );
    }
    
    public function render_course_meta_box($post) {
        wp_nonce_field('tlap_course_meta_nonce', 'tlap_course_meta_nonce');
        
        $academic_type = get_post_meta($post->ID, 'tlap_academic_type', true);
        $university = get_post_meta($post->ID, 'tlap_university', true);
        $faculty = get_post_meta($post->ID, 'tlap_faculty', true);
        $department = get_post_meta($post->ID, 'tlap_department', true);
        $school = get_post_meta($post->ID, 'tlap_school', true);
        $general_course = get_post_meta($post->ID, 'tlap_general_course', true);
        ?>
        <div class="tlap-course-meta">
            <div class="tutor-form-group">
                <label>نوع الكورس:</label>
                <select name="tlap_academic_type" class="tlap-academic-type-select">
                    <option value="">اختر النوع</option>
                    <option value="university" <?php selected($academic_type, 'university'); ?>>جامعي</option>
                    <option value="school" <?php selected($academic_type, 'school'); ?>>مدرسي</option>
                    <option value="general" <?php selected($academic_type, 'general'); ?>>كورس عام</option>
                </select>
            </div>
            
            <!-- حقول الجامعة -->
            <div class="tlap-fields-container tlap-university-fields" style="<?php echo $academic_type !== 'university' ? 'display: none;' : ''; ?>">
                <div class="tutor-form-group">
                    <label>الجامعة:</label>
                    <select name="tlap_university" class="tlap-university-select">
                        <option value="">اختر الجامعة</option>
                        <?php
                        $universities = get_terms(array(
                            'taxonomy' => 'academic_university',
                            'hide_empty' => false,
                        ));
                        foreach ($universities as $university_term) {
                            echo '<option value="' . $university_term->term_id . '" ' . selected($university, $university_term->term_id, false) . '>' . $university_term->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label>الكلية:</label>
                    <select name="tlap_faculty" class="tlap-faculty-select">
                        <option value="">اختر الكلية</option>
                        <?php
                        if ($university) {
                            $faculties = get_terms(array(
                                'taxonomy' => 'academic_faculty',
                                'hide_empty' => false,
                                'meta_query' => array(
                                    array(
                                        'key' => 'parent_university',
                                        'value' => $university
                                    )
                                )
                            ));
                            foreach ($faculties as $faculty_term) {
                                echo '<option value="' . $faculty_term->term_id . '" ' . selected($faculty, $faculty_term->term_id, false) . '>' . $faculty_term->name . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="tutor-form-group">
                    <label>القسم:</label>
                    <select name="tlap_department" class="tlap-department-select">
                        <option value="">اختر القسم</option>
                        <?php
                        if ($faculty) {
                            $departments = get_terms(array(
                                'taxonomy' => 'academic_department',
                                'hide_empty' => false,
                                'meta_query' => array(
                                    array(
                                        'key' => 'parent_faculty',
                                        'value' => $faculty
                                    )
                                )
                            ));
                            foreach ($departments as $department_term) {
                                echo '<option value="' . $department_term->term_id . '" ' . selected($department, $department_term->term_id, false) . '>' . $department_term->name . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <!-- حقول المدرسة -->
            <div class="tlap-fields-container tlap-school-fields" style="<?php echo $academic_type !== 'school' ? 'display: none;' : ''; ?>">
                <div class="tutor-form-group">
                    <label>المدرسة:</label>
                    <select name="tlap_school">
                        <option value="">اختر المدرسة</option>
                        <?php
                        $schools = get_terms(array(
                            'taxonomy' => 'academic_school',
                            'hide_empty' => false,
                        ));
                        foreach ($schools as $school_term) {
                            echo '<option value="' . $school_term->term_id . '" ' . selected($school, $school_term->term_id, false) . '>' . $school_term->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <!-- الكورسات العامة -->
            <div class="tlap-fields-container tlap-general-fields" style="<?php echo $academic_type !== 'general' ? 'display: none;' : ''; ?>">
                <div class="tutor-form-group">
                    <label>
                        <input type="checkbox" name="tlap_general_course" value="1" <?php checked($general_course, '1'); ?>>
                        كورس عام (متاح للجميع)
                    </label>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function save_course_meta($post_id) {
        if (!isset($_POST['tlap_course_meta_nonce']) || 
            !wp_verify_nonce($_POST['tlap_course_meta_nonce'], 'tlap_course_meta_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if ('courses' !== $_POST['post_type']) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // حفظ البيانات
        $fields = array(
            'tlap_academic_type',
            'tlap_university',
            'tlap_faculty',
            'tlap_department',
            'tlap_school',
            'tlap_general_course'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            } else {
                delete_post_meta($post_id, $field);
            }
        }
    }
}