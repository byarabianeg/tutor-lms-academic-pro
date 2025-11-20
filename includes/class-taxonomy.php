<?php
class TLAP_Taxonomy {
    
    public function __construct() {
        add_action('init', array($this, 'register_taxonomies'));
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
            'show_in_menu' => false,
            'show_in_rest' => true,
        ));
        
        // تصنيف الكليات (تابع للجامعات)
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
            'show_in_menu' => false,
            'show_in_rest' => true,
        ));
        
        // تصنيف الأقسام (تابع للكليات)
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
            'show_in_menu' => false,
            'show_in_rest' => true,
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
            'show_in_menu' => false,
            'show_in_rest' => true,
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
            'show_in_menu' => false,
            'show_in_rest' => true,
        ));
    }
}