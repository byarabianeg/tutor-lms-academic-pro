<?php
/**
 * Plugin Name: Tutor LMS Academic Pro
 * Plugin URI: https://github.com/byarabiano/plugin
 * Description: إضافة التصنيفات الأكاديمية لـ Tutor LMS - جامعات، مدارس، وكورسات عامة
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/byarabiano
 * License: GPL v2 or later
 * Text Domain: tutor-lms-academic-pro
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.7
 * Requires PHP: 7.4
 */

// منع الوصول المباشر
if (!defined('ABSPATH')) {
    exit;
}

// تعريف ثوابت الإضافة
define('TLAP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TLAP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TLAP_PLUGIN_VERSION', '1.0.0');

// التحقق من وجود Tutor LMS
function tlap_check_tutor_lms() {
    if (!is_plugin_active('tutor/tutor.php') && !is_plugin_active('tutor-pro/tutor-pro.php')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('هذه الإضافة تتطلب تفعيل إضافة Tutor LMS. الرجاء تثبيت وتفعيل Tutor LMS أولاً.');
    }
}
register_activation_hook(__FILE__, 'tlap_check_tutor_lms');

// رسالة تنبيه عند عدم وجود Tutor LMS
function tlap_tutor_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p><strong>Tutor LMS Academic Pro:</strong> هذه الإضافة تتطلب تفعيل إضافة Tutor LMS.</p>
    </div>
    <?php
}

// تحميل ملفات الإضافة
function tlap_init_plugin() {
    // التحقق من وجود Tutor LMS
    if (!function_exists('tutor')) {
        add_action('admin_notices', 'tlap_tutor_missing_notice');
        return;
    }
    
    // تحميل ملفات الإضافة
    require_once TLAP_PLUGIN_PATH . 'includes/class-taxonomy.php';
    require_once TLAP_PLUGIN_PATH . 'includes/class-admin.php';
    require_once TLAP_PLUGIN_PATH . 'includes/class-registration.php';
    require_once TLAP_PLUGIN_PATH . 'includes/class-course-meta.php';
    require_once TLAP_PLUGIN_PATH . 'includes/class-filters.php';
    
    // تهيئة المكونات
    new TLAP_Taxonomy();
    new TLAP_Admin();
    new TLAP_Registration();
    new TLAP_Course_Meta();
    new TLAP_Filters();
    
    // تحميل النص
    load_plugin_textdomain('tutor-lms-academic-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'tlap_init_plugin');

// إضافة رابط إعدادات في صفحة الإضافات
function tlap_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=tutor-academic-pro">الإعدادات</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tlap_add_settings_link');