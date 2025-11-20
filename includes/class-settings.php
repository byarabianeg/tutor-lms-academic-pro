<?php
class TLAP_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'tutor',
            'الإعدادات الأكاديمية',
            'الإعدادات الأكاديمية',
            'manage_options',
            'tutor-academic-settings',
            array($this, 'settings_page')
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'tutor_page_tutor-academic-settings') {
            return;
        }
        
        wp_enqueue_style('tlap-admin-css', TLAP_PLUGIN_URL . 'admin/css/admin.css', array(), TLAP_PLUGIN_VERSION);
        wp_enqueue_script('tlap-admin-js', TLAP_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), TLAP_PLUGIN_VERSION, true);
    }
    
    public function register_settings() {
        register_setting('tlap_settings_group', 'tlap_settings');
        
        add_settings_section(
            'tlap_general_section',
            'الإعدادات العامة',
            array($this, 'general_section_callback'),
            'tutor-academic-settings'
        );
        
        add_settings_field(
            'enable_registration_fields',
            'تفعيل حقول التسجيل',
            array($this, 'enable_registration_fields_callback'),
            'tutor-academic-settings',
            'tlap_general_section'
        );
        
        add_settings_field(
            'enable_course_filtering',
            'تفعيل فلترة الكورسات',
            array($this, 'enable_course_filtering_callback'),
            'tutor-academic-settings',
            'tlap_general_section'
        );
        
        add_settings_field(
            'show_in_course_creation',
            'إظهار في إنشاء الكورسات',
            array($this, 'show_in_course_creation_callback'),
            'tutor-academic-settings',
            'tlap_general_section'
        );
    }
    
    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>🛠️ إعدادات Tutor LMS Academic Pro</h1>
            
            <div class="tlap-settings-container">
                <div class="tlap-settings-main">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('tlap_settings_group');
                        do_settings_sections('tutor-academic-settings');
                        submit_button('حفظ الإعدادات');
                        ?>
                    </form>
                </div>
                
                <div class="tlap-settings-sidebar">
                    <div class="tlap-info-box">
                        <h3>📊 إحصائيات سريعة</h3>
                        <ul>
                            <li>الجامعات: <?php echo wp_count_terms('academic_university'); ?></li>
                            <li>الكليات: <?php echo wp_count_terms('academic_faculty'); ?></li>
                            <li>الأقسام: <?php echo wp_count_terms('academic_department'); ?></li>
                            <li>المدارس: <?php echo wp_count_terms('academic_school'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="tlap-info-box">
                        <h3>🔗 روابط سريعة</h3>
                        <ul>
                            <li><a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_university'); ?>">إدارة الجامعات</a></li>
                            <li><a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_faculty'); ?>">إدارة الكليات</a></li>
                            <li><a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_department'); ?>">إدارة الأقسام</a></li>
                            <li><a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_school'); ?>">إدارة المدارس</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function general_section_callback() {
        echo '<p>الإعدادات العامة للإضافة الأكاديمية</p>';
    }
    
    public function enable_registration_fields_callback() {
        $options = get_option('tlap_settings');
        $value = isset($options['enable_registration_fields']) ? $options['enable_registration_fields'] : 1;
        ?>
        <label>
            <input type="checkbox" name="tlap_settings[enable_registration_fields]" value="1" <?php checked(1, $value); ?> />
            تفعيل عرض حقول التصنيف الأكاديمي في صفحات التسجيل
        </label>
        <p class="description">سيتم إظهار حقول اختيار الجامعة/المدرسة في صفحات تسجيل الطلاب والمعلمين</p>
        <?php
    }
    
    public function enable_course_filtering_callback() {
        $options = get_option('tlap_settings');
        $value = isset($options['enable_course_filtering']) ? $options['enable_course_filtering'] : 1;
        ?>
        <label>
            <input type="checkbox" name="tlap_settings[enable_course_filtering]" value="1" <?php checked(1, $value); ?> />
            تفعيل نظام الفلترة الأكاديمية للكورسات
        </label>
        <p class="description">الطلاب سيرون فقط الكورسات الخاصة بتخصصهم بالإضافة للكورسات العامة</p>
        <?php
    }
    
    public function show_in_course_creation_callback() {
        $options = get_option('tlap_settings');
        $value = isset($options['show_in_course_creation']) ? $options['show_in_course_creation'] : 1;
        ?>
        <label>
            <input type="checkbox" name="tlap_settings[show_in_course_creation]" value="1" <?php checked(1, $value); ?> />
            إظهار خيارات التصنيف الأكاديمي عند إنشاء الكورسات
        </label>
        <p class="description">سيتم إضافة تبويب التصنيف الأكاديمي في صفحة إنشاء وتعديل الكورسات</p>
        <?php
    }
}