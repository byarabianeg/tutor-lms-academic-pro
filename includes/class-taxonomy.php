<?php
class TLAP_Taxonomy {
    
    public function __construct() {
        add_action('init', array($this, 'register_taxonomies'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // حقول إضافية للتصنيفات
        add_action('academic_university_add_form_fields', array($this, 'add_university_fields'));
        add_action('academic_university_edit_form_fields', array($this, 'edit_university_fields'));
        add_action('created_academic_university', array($this, 'save_university_fields'));
        add_action('edited_academic_university', array($this, 'save_university_fields'));
        
        add_action('academic_faculty_add_form_fields', array($this, 'add_faculty_fields'));
        add_action('academic_faculty_edit_form_fields', array($this, 'edit_faculty_fields'));
        add_action('created_academic_faculty', array($this, 'save_faculty_fields'));
        add_action('edited_academic_faculty', array($this, 'save_faculty_fields'));
        
        add_action('academic_school_add_form_fields', array($this, 'add_school_fields'));
        add_action('academic_school_edit_form_fields', array($this, 'edit_school_fields'));
        add_action('created_academic_school', array($this, 'save_school_fields'));
        add_action('edited_academic_school', array($this, 'save_school_fields'));
    }
    
    public function enqueue_admin_scripts($hook) {
        if ('edit-tags.php' !== $hook && 'term.php' !== $hook) {
            return;
        }
        
        $screen = get_current_screen();
        $taxonomies = array('academic_university', 'academic_faculty', 'academic_department', 'academic_school');
        
        if (in_array($screen->taxonomy, $taxonomies)) {
            wp_enqueue_script('tlap-taxonomy-js', TLAP_PLUGIN_URL . 'admin/js/taxonomy.js', array('jquery'), TLAP_PLUGIN_VERSION, true);
        }
    }
    
    public function register_taxonomies() {
        // تصنيف الجامعات
        register_taxonomy('academic_university', 'courses', array(
            'labels' => array(
                'name' => 'الجامعات',
                'singular_name' => 'جامعة',
                'menu_name' => 'الجامعات',
                'all_items' => 'جميع الجامعات',
                'edit_item' => 'تعديل الجامعة',
                'view_item' => 'عرض الجامعة',
                'update_item' => 'تحديث الجامعة',
                'add_new_item' => 'إضافة جامعة جديدة',
                'new_item_name' => 'اسم الجامعة الجديدة',
                'search_items' => 'بحث الجامعات',
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'public' => true,
        ));
        
        // تصنيف الكليات (مرتبط بالجامعات)
        register_taxonomy('academic_faculty', 'courses', array(
            'labels' => array(
                'name' => 'الكليات',
                'singular_name' => 'كلية',
                'menu_name' => 'الكليات',
                'all_items' => 'جميع الكليات',
                'edit_item' => 'تعديل الكلية',
                'view_item' => 'عرض الكلية',
                'update_item' => 'تحديث الكلية',
                'add_new_item' => 'إضافة كلية جديدة',
                'new_item_name' => 'اسم الكلية الجديدة',
                'search_items' => 'بحث الكليات',
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'public' => true,
        ));
        
        // تصنيف الأقسام (مرتبط بالكليات)
        register_taxonomy('academic_department', 'courses', array(
            'labels' => array(
                'name' => 'الأقسام',
                'singular_name' => 'قسم',
                'menu_name' => 'الأقسام',
                'all_items' => 'جميع الأقسام',
                'edit_item' => 'تعديل القسم',
                'view_item' => 'عرض القسم',
                'update_item' => 'تحديث القسم',
                'add_new_item' => 'إضافة قسم جديد',
                'new_item_name' => 'اسم القسم الجديد',
                'search_items' => 'بحث الأقسام',
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'public' => true,
        ));
        
        // تصنيف المدارس
        register_taxonomy('academic_school', 'courses', array(
            'labels' => array(
                'name' => 'المدارس',
                'singular_name' => 'مدرسة',
                'menu_name' => 'المدارس',
                'all_items' => 'جميع المدارس',
                'edit_item' => 'تعديل المدرسة',
                'view_item' => 'عرض المدرسة',
                'update_item' => 'تحديث المدرسة',
                'add_new_item' => 'إضافة مدرسة جديدة',
                'new_item_name' => 'اسم المدرسة الجديدة',
                'search_items' => 'بحث المدارس',
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'public' => true,
        ));
        
        // تصنيف الكورسات العامة
        register_taxonomy('academic_general', 'courses', array(
            'labels' => array(
                'name' => 'الكورسات العامة',
                'singular_name' => 'كورس عام',
                'menu_name' => 'كورسات عامة',
                'all_items' => 'جميع الكورسات العامة',
                'edit_item' => 'تعديل الكورس العام',
                'view_item' => 'عرض الكورس العام',
                'update_item' => 'تحديث الكورس العام',
                'add_new_item' => 'إضافة كورس عام جديد',
                'new_item_name' => 'اسم الكورس العام الجديد',
                'search_items' => 'بحث الكورسات العامة',
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'public' => true,
        ));
    }
    
    // حقول الجامعات
    public function add_university_fields($taxonomy) {
        ?>
        <div class="form-field">
            <label for="university_type">نوع الجامعة</label>
            <select name="university_type" id="university_type">
                <option value="public">حكومية</option>
                <option value="private">خاصة</option>
            </select>
            <p>اختر نوع الجامعة</p>
        </div>
        
        <div class="form-field">
            <label for="university_location">المحافظة/المدينة</label>
            <input type="text" name="university_location" id="university_location" value="">
            <p>موقع الجامعة</p>
        </div>
        <?php
    }
    
    public function edit_university_fields($term) {
        $university_type = get_term_meta($term->term_id, 'university_type', true);
        $university_location = get_term_meta($term->term_id, 'university_location', true);
        ?>
        <tr class="form-field">
            <th scope="row"><label for="university_type">نوع الجامعة</label></th>
            <td>
                <select name="university_type" id="university_type">
                    <option value="public" <?php selected($university_type, 'public'); ?>>حكومية</option>
                    <option value="private" <?php selected($university_type, 'private'); ?>>خاصة</option>
                </select>
                <p class="description">اختر نوع الجامعة</p>
            </td>
        </tr>
        
        <tr class="form-field">
            <th scope="row"><label for="university_location">المحافظة/المدينة</label></th>
            <td>
                <input type="text" name="university_location" id="university_location" value="<?php echo esc_attr($university_location); ?>">
                <p class="description">موقع الجامعة</p>
            </td>
        </tr>
        <?php
    }
    
    public function save_university_fields($term_id) {
        if (isset($_POST['university_type'])) {
            update_term_meta($term_id, 'university_type', sanitize_text_field($_POST['university_type']));
        }
        if (isset($_POST['university_location'])) {
            update_term_meta($term_id, 'university_location', sanitize_text_field($_POST['university_location']));
        }
    }
    
    // حقول الكليات
    public function add_faculty_fields($taxonomy) {
        $universities = get_terms(array(
            'taxonomy' => 'academic_university',
            'hide_empty' => false,
        ));
        ?>
        <div class="form-field">
            <label for="parent_university">الجامعة التابعة لها</label>
            <select name="parent_university" id="parent_university">
                <option value="">اختر الجامعة</option>
                <?php foreach ($universities as $university): ?>
                    <option value="<?php echo $university->term_id; ?>"><?php echo $university->name; ?></option>
                <?php endforeach; ?>
            </select>
            <p>اختر الجامعة التي تتبع لها هذه الكلية</p>
        </div>
        <?php
    }
    
    public function edit_faculty_fields($term) {
        $parent_university = get_term_meta($term->term_id, 'parent_university', true);
        $universities = get_terms(array(
            'taxonomy' => 'academic_university',
            'hide_empty' => false,
        ));
        ?>
        <tr class="form-field">
            <th scope="row"><label for="parent_university">الجامعة التابعة لها</label></th>
            <td>
                <select name="parent_university" id="parent_university">
                    <option value="">اختر الجامعة</option>
                    <?php foreach ($universities as $university): ?>
                        <option value="<?php echo $university->term_id; ?>" <?php selected($parent_university, $university->term_id); ?>>
                            <?php echo $university->name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">اختر الجامعة التي تتبع لها هذه الكلية</p>
            </td>
        </tr>
        <?php
    }
    
    public function save_faculty_fields($term_id) {
        if (isset($_POST['parent_university'])) {
            update_term_meta($term_id, 'parent_university', intval($_POST['parent_university']));
        }
    }
    
    // حقول المدارس
    public function add_school_fields($taxonomy) {
        ?>
        <div class="form-field">
            <label for="school_type">نوع المدرسة</label>
            <select name="school_type" id="school_type">
                <option value="public">حكومية</option>
                <option value="private">خاصة</option>
                <option value="international">دولية</option>
            </select>
            <p>اختر نوع المدرسة</p>
        </div>
        
        <div class="form-field">
            <label for="school_levels">المراحل المتاحة</label>
            <div>
                <label><input type="checkbox" name="school_levels[]" value="preschool"> رياض أطفال</label><br>
                <label><input type="checkbox" name="school_levels[]" value="elementary"> ابتدائي</label><br>
                <label><input type="checkbox" name="school_levels[]" value="middle"> متوسط</label><br>
                <label><input type="checkbox" name="school_levels[]" value="high"> ثانوي</label>
            </div>
            <p>اختر المراحل الدراسية المتاحة في المدرسة</p>
        </div>
        <?php
    }
    
    public function edit_school_fields($term) {
        $school_type = get_term_meta($term->term_id, 'school_type', true);
        $school_levels = get_term_meta($term->term_id, 'school_levels', true);
        $school_levels = is_array($school_levels) ? $school_levels : array();
        ?>
        <tr class="form-field">
            <th scope="row"><label for="school_type">نوع المدرسة</label></th>
            <td>
                <select name="school_type" id="school_type">
                    <option value="public" <?php selected($school_type, 'public'); ?>>حكومية</option>
                    <option value="private" <?php selected($school_type, 'private'); ?>>خاصة</option>
                    <option value="international" <?php selected($school_type, 'international'); ?>>دولية</option>
                </select>
                <p class="description">اختر نوع المدرسة</p>
            </td>
        </tr>
        
        <tr class="form-field">
            <th scope="row"><label>المراحل المتاحة</label></th>
            <td>
                <label><input type="checkbox" name="school_levels[]" value="preschool" <?php checked(in_array('preschool', $school_levels)); ?>> رياض أطفال</label><br>
                <label><input type="checkbox" name="school_levels[]" value="elementary" <?php checked(in_array('elementary', $school_levels)); ?>> ابتدائي</label><br>
                <label><input type="checkbox" name="school_levels[]" value="middle" <?php checked(in_array('middle', $school_levels)); ?>> متوسط</label><br>
                <label><input type="checkbox" name="school_levels[]" value="high" <?php checked(in_array('high', $school_levels)); ?>> ثانوي</label>
                <p class="description">اختر المراحل الدراسية المتاحة في المدرسة</p>
            </td>
        </tr>
        <?php
    }
    
    public function save_school_fields($term_id) {
        if (isset($_POST['school_type'])) {
            update_term_meta($term_id, 'school_type', sanitize_text_field($_POST['school_type']));
        }
        
        if (isset($_POST['school_levels'])) {
            update_term_meta($term_id, 'school_levels', array_map('sanitize_text_field', $_POST['school_levels']));
        } else {
            delete_term_meta($term_id, 'school_levels');
        }
    }
}