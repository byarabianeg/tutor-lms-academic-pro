<?php
class TLAP_Filters {
    
    public function __construct() {
        add_action('pre_get_posts', array($this, 'filter_courses_by_academic_type'));
        add_action('wp', array($this, 'check_user_access'));
    }
    
    public function filter_courses_by_academic_type($query) {
        if (is_admin() || !$query->is_main_query() || !is_post_type_archive('courses')) {
            return;
        }
        
        if (!is_user_logged_in()) {
            // الزوار يرون فقط الكورسات العامة
            $meta_query = array(
                array(
                    'key' => 'tlap_general_course',
                    'value' => '1',
                    'compare' => '='
                )
            );
            $query->set('meta_query', $meta_query);
            return;
        }
        
        $user_id = get_current_user_id();
        $academic_type = get_user_meta($user_id, 'tlap_academic_type', true);
        
        $meta_query = array('relation' => 'OR');
        
        // إضافة الكورسات العامة دائماً
        $meta_query[] = array(
            'key' => 'tlap_general_course',
            'value' => '1',
            'compare' => '='
        );
        
        switch ($academic_type) {
            case 'university':
                $university = get_user_meta($user_id, 'tlap_university', true);
                $faculty = get_user_meta($user_id, 'tlap_faculty', true);
                $department = get_user_meta($user_id, 'tlap_department', true);
                
                if ($department) {
                    $meta_query[] = array(
                        'key' => 'tlap_department',
                        'value' => $department,
                        'compare' => '='
                    );
                } elseif ($faculty) {
                    $meta_query[] = array(
                        'key' => 'tlap_faculty',
                        'value' => $faculty,
                        'compare' => '='
                    );
                } elseif ($university) {
                    $meta_query[] = array(
                        'key' => 'tlap_university',
                        'value' => $university,
                        'compare' => '='
                    );
                }
                break;
                
            case 'school':
                $school = get_user_meta($user_id, 'tlap_school', true);
                if ($school) {
                    $meta_query[] = array(
                        'key' => 'tlap_school',
                        'value' => $school,
                        'compare' => '='
                    );
                }
                break;
                
            case 'general':
                // مستخدمو الكورسات العامة يرون الكورسات العامة فقط
                $meta_query = array(
                    array(
                        'key' => 'tlap_general_course',
                        'value' => '1',
                        'compare' => '='
                    )
                );
                break;
        }
        
        $query->set('meta_query', $meta_query);
    }
    
    public function check_user_access() {
        if (is_singular('courses') && is_user_logged_in()) {
            global $post;
            $user_id = get_current_user_id();
            $academic_type = get_user_meta($user_id, 'tlap_academic_type', true);
            $course_id = $post->ID;
            
            // الكورسات العامة متاحة للجميع
            if (get_post_meta($course_id, 'tlap_general_course', true)) {
                return;
            }
            
            $has_access = false;
            
            switch ($academic_type) {
                case 'university':
                    $user_university = get_user_meta($user_id, 'tlap_university', true);
                    $user_faculty = get_user_meta($user_id, 'tlap_faculty', true);
                    $user_department = get_user_meta($user_id, 'tlap_department', true);
                    
                    $course_university = get_post_meta($course_id, 'tlap_university', true);
                    $course_faculty = get_post_meta($course_id, 'tlap_faculty', true);
                    $course_department = get_post_meta($course_id, 'tlap_department', true);
                    
                    if ($user_department && $course_department) {
                        $has_access = ($user_department == $course_department);
                    } elseif ($user_faculty && $course_faculty) {
                        $has_access = ($user_faculty == $course_faculty);
                    } elseif ($user_university && $course_university) {
                        $has_access = ($user_university == $course_university);
                    }
                    break;
                    
                case 'school':
                    $user_school = get_user_meta($user_id, 'tlap_school', true);
                    $course_school = get_post_meta($course_id, 'tlap_school', true);
                    $has_access = ($user_school == $course_school);
                    break;
                    
                case 'general':
                    // مستخدمو الكورسات العامة لا يمكنهم الوصول للكورسات الخاصة
                    $has_access = false;
                    break;
            }
            
            if (!$has_access) {
                wp_redirect(home_url('/courses/'));
                exit;
            }
        }
    }
}