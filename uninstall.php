<?php
// منع الوصول المباشر
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// حذف الخيارات من قاعدة البيانات
delete_option('tlap_settings');
delete_option('tlap_version');

// حذف التصنيفات المخصصة (اختياري - يمكن التعليق عليه إذا أردت الاحتفاظ بالبيانات)
// global $wpdb;
// $wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'academic_university'");
// $wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'academic_school'");
// $wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'academic_general'");

// مسح بيانات المستخدمين
// $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'tlap_%'");

// مسح بيانات الكورسات
// $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'tlap_%'");