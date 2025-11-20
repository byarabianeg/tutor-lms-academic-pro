<?php
class TLAP_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'tutor',
            'التصنيفات الأكاديمية',
            'التصنيفات الأكاديمية',
            'manage_options',
            'tutor-academic-pro',
            array($this, 'admin_page')
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'tutor_page_tutor-academic-pro') {
            return;
        }
        
        wp_enqueue_style('tlap-admin-css', TLAP_PLUGIN_URL . 'admin/css/admin.css', array(), TLAP_PLUGIN_VERSION);
        wp_enqueue_script('tlap-admin-js', TLAP_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), TLAP_PLUGIN_VERSION, true);
        
        wp_localize_script('tlap-admin-js', 'tlap_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tlap_nonce')
        ));
    }
    
    public function admin_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'universities';
        ?>
        <div class="wrap tlap-admin-wrap">
            <h1>التصنيفات الأكاديمية - Tutor LMS Academic Pro</h1>
            
            <nav class="nav-tab-wrapper">
                <a href="?page=tutor-academic-pro&tab=universities" class="nav-tab <?php echo $active_tab === 'universities' ? 'nav-tab-active' : ''; ?>">
                    الجامعات والكليات
                </a>
                <a href="?page=tutor-academic-pro&tab=schools" class="nav-tab <?php echo $active_tab === 'schools' ? 'nav-tab-active' : ''; ?>">
                    المدارس
                </a>
                <a href="?page=tutor-academic-pro&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
                    الكورسات العامة
                </a>
            </nav>
            
            <div class="tlap-tab-content">
                <?php
                switch ($active_tab) {
                    case 'universities':
                        $this->universities_tab();
                        break;
                    case 'schools':
                        $this->schools_tab();
                        break;
                    case 'general':
                        $this->general_tab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    private function universities_tab() {
        ?>
        <div class="tlap-tab-panel">
            <h2>إدارة الجامعات والكليات</h2>
            <p>من هنا يمكنك إضافة وإدارة الجامعات والكليات والأقسام الأكاديمية.</p>
            
            <div class="tlap-admin-grid">
                <div class="tlap-admin-column">
                    <h3>الجامعات</h3>
                    <?php
                    $universities = get_terms(array(
                        'taxonomy' => 'academic_university',
                        'hide_empty' => false,
                    ));
                    ?>
                    <ul class="tlap-term-list">
                        <?php foreach ($universities as $university): ?>
                            <li>
                                <strong><?php echo esc_html($university->name); ?></strong>
                                <span class="tlap-term-actions">
                                    <a href="<?php echo get_edit_term_link($university->term_id, 'academic_university'); ?>">تعديل</a>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_university'); ?>" class="button button-primary">
                        إدارة الجامعات
                    </a>
                </div>
                
                <div class="tlap-admin-column">
                    <h3>الكليات</h3>
                    <?php
                    $faculties = get_terms(array(
                        'taxonomy' => 'academic_faculty',
                        'hide_empty' => false,
                    ));
                    ?>
                    <ul class="tlap-term-list">
                        <?php foreach ($faculties as $faculty): ?>
                            <li>
                                <strong><?php echo esc_html($faculty->name); ?></strong>
                                <span class="tlap-term-actions">
                                    <a href="<?php echo get_edit_term_link($faculty->term_id, 'academic_faculty'); ?>">تعديل</a>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_faculty'); ?>" class="button button-primary">
                        إدارة الكليات
                    </a>
                </div>
                
                <div class="tlap-admin-column">
                    <h3>الأقسام</h3>
                    <?php
                    $departments = get_terms(array(
                        'taxonomy' => 'academic_department',
                        'hide_empty' => false,
                    ));
                    ?>
                    <ul class="tlap-term-list">
                        <?php foreach ($departments as $department): ?>
                            <li>
                                <strong><?php echo esc_html($department->name); ?></strong>
                                <span class="tlap-term-actions">
                                    <a href="<?php echo get_edit_term_link($department->term_id, 'academic_department'); ?>">تعديل</a>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_department'); ?>" class="button button-primary">
                        إدارة الأقسام
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function schools_tab() {
        ?>
        <div class="tlap-tab-panel">
            <h2>إدارة المدارس</h2>
            <p>من هنا يمكنك إضافة وإدارة المدارس والمراحل الدراسية.</p>
            
            <?php
            $schools = get_terms(array(
                'taxonomy' => 'academic_school',
                'hide_empty' => false,
            ));
            ?>
            
            <ul class="tlap-term-list">
                <?php foreach ($schools as $school): ?>
                    <li>
                        <strong><?php echo esc_html($school->name); ?></strong>
                        <span class="tlap-term-actions">
                            <a href="<?php echo get_edit_term_link($school->term_id, 'academic_school'); ?>">تعديل</a>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_school'); ?>" class="button button-primary">
                إدارة المدارس
            </a>
        </div>
        <?php
    }
    
    private function general_tab() {
        ?>
        <div class="tlap-tab-panel">
            <h2>إدارة الكورسات العامة</h2>
            <p>من هنا يمكنك إضافة وإدارة الكورسات العامة المتاحة للجميع.</p>
            
            <?php
            $general_courses = get_terms(array(
                'taxonomy' => 'academic_general',
                'hide_empty' => false,
            ));
            ?>
            
            <ul class="tlap-term-list">
                <?php foreach ($general_courses as $course): ?>
                    <li>
                        <strong><?php echo esc_html($course->name); ?></strong>
                        <span class="tlap-term-actions">
                            <a href="<?php echo get_edit_term_link($course->term_id, 'academic_general'); ?>">تعديل</a>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <a href="<?php echo admin_url('edit-tags.php?taxonomy=academic_general'); ?>" class="button button-primary">
                إدارة الكورسات العامة
            </a>
        </div>
        <?php
    }
}