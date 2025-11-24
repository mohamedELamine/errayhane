<?php
/**
 * REST API Endpoints
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_REST_API {

    private static $instance = null;
    private $namespace = 'fiqh-lms/v1';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * تسجيل Routes
     */
    public function register_routes() {
        // Authentication
        register_rest_route($this->namespace, '/auth/login', array(
            'methods' => 'POST',
            'callback' => array($this, 'login'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route($this->namespace, '/auth/validate', array(
            'methods' => 'GET',
            'callback' => array($this, 'validate_token'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        register_rest_route($this->namespace, '/auth/user', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_current_user'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        // Sciences (Taxonomies)
        register_rest_route($this->namespace, '/sciences', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_sciences'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        // Courses
        register_rest_route($this->namespace, '/courses', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_courses'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        register_rest_route($this->namespace, '/courses/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_course'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        // Lessons
        register_rest_route($this->namespace, '/lessons', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_lessons'),
            'permission_callback' => array($this, 'check_read_permission'),
        ));

        register_rest_route($this->namespace, '/lessons/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_lesson'),
            'permission_callback' => array($this, 'check_lesson_access'),
        ));

        // Progress
        register_rest_route($this->namespace, '/progress', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_user_progress'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        register_rest_route($this->namespace, '/progress/update', array(
            'methods' => 'POST',
            'callback' => array($this, 'update_progress'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        // Questions
        register_rest_route($this->namespace, '/questions', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_questions'),
            'permission_callback' => array($this, 'check_read_permission'),
        ));

        register_rest_route($this->namespace, '/questions', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_question'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        register_rest_route($this->namespace, '/questions/(?P<id>\d+)/answer', array(
            'methods' => 'POST',
            'callback' => array($this, 'answer_question'),
            'permission_callback' => array($this, 'check_teacher_permission'),
        ));

        // Enrollments
        register_rest_route($this->namespace, '/enrollments', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_enrollments'),
            'permission_callback' => array($this, 'check_auth'),
        ));

        register_rest_route($this->namespace, '/enrollments', array(
            'methods' => 'POST',
            'callback' => array($this, 'enroll_student'),
            'permission_callback' => array($this, 'check_admin_permission'),
        ));

        // Stats
        register_rest_route($this->namespace, '/stats/dashboard', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_dashboard_stats'),
            'permission_callback' => array($this, 'check_auth'),
        ));
    }

    /**
     * تسجيل الدخول
     */
    public function login($request) {
        $username = $request->get_param('username');
        $password = $request->get_param('password');

        if (empty($username) || empty($password)) {
            return new WP_Error('missing_credentials', 'اسم المستخدم وكلمة المرور مطلوبان', array('status' => 400));
        }

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            return new WP_Error('invalid_credentials', 'بيانات الدخول غير صحيحة', array('status' => 401));
        }

        // إنشاء token بسيط (في الإنتاج يجب استخدام JWT)
        $token = base64_encode($username . ':' . wp_hash_password($password));

        // حفظ token في user meta
        update_user_meta($user->ID, '_app_auth_token', $token);

        return new WP_REST_Response(array(
            'success' => true,
            'data' => array(
                'user_id' => $user->ID,
                'username' => $user->user_login,
                'display_name' => $user->display_name,
                'email' => $user->user_email,
                'token' => $token,
                'roles' => $user->roles,
                'level' => get_user_meta($user->ID, '_fiqh_user_level', true),
            ),
        ), 200);
    }

    /**
     * التحقق من صحة token
     */
    public function validate_token($request) {
        $user_id = get_current_user_id();

        return new WP_REST_Response(array(
            'success' => true,
            'valid' => true,
            'user_id' => $user_id,
        ), 200);
    }

    /**
     * الحصول على بيانات المستخدم الحالي
     */
    public function get_current_user($request) {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        if (!$user) {
            return new WP_Error('user_not_found', 'المستخدم غير موجود', array('status' => 404));
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => array(
                'id' => $user->ID,
                'username' => $user->user_login,
                'display_name' => $user->display_name,
                'email' => $user->user_email,
                'roles' => $user->roles,
                'level' => get_user_meta($user_id, '_fiqh_user_level', true),
                'avatar_url' => get_avatar_url($user_id),
            ),
        ), 200);
    }

    /**
     * الحصول على العلوم
     */
    public function get_sciences($request) {
        $user_id = get_current_user_id();
        $user_level = get_user_meta($user_id, '_fiqh_user_level', true);

        $args = array(
            'taxonomy' => 'fiqh_course_science',
            'hide_empty' => false,
        );

        $sciences = get_terms($args);
        $formatted_sciences = array();

        foreach ($sciences as $science) {
            // الحصول على المقررات المرتبطة بهذا العلم
            $courses_query = new WP_Query(array(
                'post_type' => 'fiqh_course',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'fiqh_course_science',
                        'field' => 'term_id',
                        'terms' => $science->term_id,
                    ),
                ),
            ));

            $courses = array();
            foreach ($courses_query->posts as $course) {
                $course_level = get_post_meta($course->ID, '_fiqh_course_level', true);

                // تصفية المقررات حسب مستوى الطالب
                if (!empty($user_level) && !empty($course_level) && intval($course_level) > intval($user_level)) {
                    continue;
                }

                // التحقق من التسجيل
                global $wpdb;
                $is_enrolled = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments
                    WHERE user_id = %d AND course_id = %d AND status = 'active'",
                    $user_id, $course->ID
                ));

                $courses[] = array(
                    'id' => $course->ID,
                    'title' => $course->post_title,
                    'excerpt' => $course->post_excerpt,
                    'thumbnail' => get_the_post_thumbnail_url($course->ID, 'medium'),
                    'level' => $course_level,
                    'is_enrolled' => (bool)$is_enrolled,
                );
            }

            if (!empty($courses)) {
                $formatted_sciences[] = array(
                    'id' => $science->term_id,
                    'name' => $science->name,
                    'slug' => $science->slug,
                    'description' => $science->description,
                    'courses_count' => count($courses),
                    'courses' => $courses,
                );
            }
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $formatted_sciences,
        ), 200);
    }

    /**
     * الحصول على المقررات
     */
    public function get_courses($request) {
        $args = array(
            'post_type' => 'fiqh_course',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);
        $courses = array();

        foreach ($query->posts as $post) {
            $courses[] = $this->format_course($post);
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $courses,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ), 200);
    }

    /**
     * الحصول على مقرر واحد
     */
    public function get_course($request) {
        $course_id = $request->get_param('id');
        $post = get_post($course_id);

        if (!$post || $post->post_type !== 'fiqh_course') {
            return new WP_Error('course_not_found', 'المقرر غير موجود', array('status' => 404));
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $this->format_course($post),
        ), 200);
    }

    /**
     * الحصول على الدروس
     */
    public function get_lessons($request) {
        $course_id = $request->get_param('course_id');

        $args = array(
            'post_type' => 'fiqh_lesson',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        if ($course_id) {
            $args['meta_query'] = array(
                array(
                    'key' => '_fiqh_lesson_course_id',
                    'value' => $course_id,
                )
            );
        }

        $query = new WP_Query($args);
        $lessons = array();

        foreach ($query->posts as $post) {
            $lessons[] = $this->format_lesson($post);
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $lessons,
        ), 200);
    }

    /**
     * الحصول على درس واحد
     */
    public function get_lesson($request) {
        $lesson_id = $request->get_param('id');
        $post = get_post($lesson_id);

        if (!$post || $post->post_type !== 'fiqh_lesson') {
            return new WP_Error('lesson_not_found', 'الدرس غير موجود', array('status' => 404));
        }

        // التحقق من صلاحية الوصول
        if (!fiqh_can_access_lesson($lesson_id)) {
            return new WP_Error('access_denied', 'ليس لديك صلاحية لعرض هذا الدرس', array('status' => 403));
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $this->format_lesson($post, true),
        ), 200);
    }

    /**
     * الحصول على تقدم المستخدم
     */
    public function get_user_progress($request) {
        global $wpdb;
        $user_id = get_current_user_id();

        $progress = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fiqh_progress
            WHERE user_id = %d
            ORDER BY updated_at DESC",
            $user_id
        ), ARRAY_A);

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $progress,
        ), 200);
    }

    /**
     * تحديث التقدم
     */
    public function update_progress($request) {
        global $wpdb;
        $user_id = get_current_user_id();
        $lesson_id = $request->get_param('lesson_id');
        $percentage = $request->get_param('percentage');
        $course_id = get_post_meta($lesson_id, '_fiqh_lesson_course_id', true);

        // التحقق من الصلاحية
        if (!fiqh_can_access_lesson($lesson_id, $user_id)) {
            return new WP_Error('access_denied', 'ليس لديك صلاحية', array('status' => 403));
        }

        $table = $wpdb->prefix . 'fiqh_progress';
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND lesson_id = %d",
            $user_id, $lesson_id
        ));

        $data = array(
            'progress_percentage' => min(100, max(0, intval($percentage))),
            'status' => $percentage >= 100 ? 'completed' : 'in_progress',
        );

        if ($existing) {
            $wpdb->update($table, $data, array('user_id' => $user_id, 'lesson_id' => $lesson_id));
        } else {
            $data['user_id'] = $user_id;
            $data['lesson_id'] = $lesson_id;
            $data['course_id'] = $course_id;
            $wpdb->insert($table, $data);
        }

        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'تم تحديث التقدم بنجاح',
        ), 200);
    }

    /**
     * الحصول على الأسئلة
     */
    public function get_questions($request) {
        global $wpdb;

        $where = array('1=1');
        $params = array();

        if ($course_id = $request->get_param('course_id')) {
            $where[] = 'course_id = %d';
            $params[] = $course_id;
        }

        if ($lesson_id = $request->get_param('lesson_id')) {
            $where[] = 'lesson_id = %d';
            $params[] = $lesson_id;
        }

        if ($status = $request->get_param('status')) {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        $where_clause = implode(' AND ', $where);
        $query = "SELECT * FROM {$wpdb->prefix}fiqh_questions WHERE $where_clause ORDER BY created_at DESC";

        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }

        $questions = $wpdb->get_results($query, ARRAY_A);

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $questions,
        ), 200);
    }

    /**
     * إنشاء سؤال
     */
    public function create_question($request) {
        global $wpdb;
        $user_id = get_current_user_id();

        $data = array(
            'user_id' => $user_id,
            'course_id' => $request->get_param('course_id'),
            'lesson_id' => $request->get_param('lesson_id'),
            'question' => sanitize_textarea_field($request->get_param('question')),
            'is_anonymous' => $request->get_param('is_anonymous') ? 1 : 0,
            'status' => 'pending',
        );

        $wpdb->insert($wpdb->prefix . 'fiqh_questions', $data);

        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'تم إضافة السؤال بنجاح',
            'question_id' => $wpdb->insert_id,
        ), 201);
    }

    /**
     * الإجابة على سؤال
     */
    public function answer_question($request) {
        global $wpdb;
        $question_id = $request->get_param('id');
        $answer = sanitize_textarea_field($request->get_param('answer'));
        $user_id = get_current_user_id();

        $updated = $wpdb->update(
            $wpdb->prefix . 'fiqh_questions',
            array(
                'answer' => $answer,
                'answered_by' => $user_id,
                'answered_at' => current_time('mysql'),
                'status' => 'answered',
            ),
            array('id' => $question_id)
        );

        if ($updated === false) {
            return new WP_Error('update_failed', 'فشل تحديث الإجابة', array('status' => 500));
        }

        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'تم إضافة الإجابة بنجاح',
        ), 200);
    }

    /**
     * الحصول على التسجيلات
     */
    public function get_enrollments($request) {
        global $wpdb;
        $user_id = get_current_user_id();

        $enrollments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND status = 'active'",
            $user_id
        ), ARRAY_A);

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $enrollments,
        ), 200);
    }

    /**
     * تسجيل طالب
     */
    public function enroll_student($request) {
        global $wpdb;

        $data = array(
            'user_id' => $request->get_param('user_id'),
            'course_id' => $request->get_param('course_id'),
            'status' => 'active',
        );

        $wpdb->insert($wpdb->prefix . 'fiqh_enrollments', $data);

        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'تم تسجيل الطالب بنجاح',
        ), 201);
    }

    /**
     * إحصائيات لوحة التحكم
     */
    public function get_dashboard_stats($request) {
        global $wpdb;
        $user_id = get_current_user_id();

        $stats = array(
            'total_courses' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND status = 'active'",
                $user_id
            )),
            'completed_lessons' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_progress WHERE user_id = %d AND status = 'completed'",
                $user_id
            )),
            'pending_questions' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions WHERE user_id = %d AND status = 'pending'",
                $user_id
            )),
        );

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $stats,
        ), 200);
    }

    /**
     * تنسيق بيانات المقرر
     */
    private function format_course($post, $full = false) {
        $data = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'medium'),
            'permalink' => get_permalink($post->ID),
        );

        if ($full) {
            $data['content'] = $post->post_content;
            $data['book_url'] = get_post_meta($post->ID, '_fiqh_course_book_url', true);
            $data['duration'] = get_post_meta($post->ID, '_fiqh_course_duration', true);
        }

        return $data;
    }

    /**
     * تنسيق بيانات الدرس
     */
    private function format_lesson($post, $full = false) {
        $data = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'order' => get_post_meta($post->ID, '_fiqh_lesson_order', true),
        );

        if ($full) {
            $data['content'] = $post->post_content;
            $data['video_url'] = get_post_meta($post->ID, '_fiqh_lesson_video_url', true);
            $data['audio_url'] = get_post_meta($post->ID, '_fiqh_lesson_audio_url', true);
            $data['pdf_url'] = get_post_meta($post->ID, '_fiqh_lesson_pdf_url', true);
            $data['duration'] = get_post_meta($post->ID, '_fiqh_lesson_duration', true);
            $data['exercises'] = get_post_meta($post->ID, '_fiqh_lesson_exercises', true);
        }

        return $data;
    }

    /**
     * فحص صلاحية القراءة
     */
    public function check_read_permission() {
        return true; // عام للجميع
    }

    /**
     * فحص صلاحية المصادقة
     */
    public function check_auth() {
        return is_user_logged_in();
    }

    /**
     * فحص صلاحية الوصول للدرس
     */
    public function check_lesson_access($request) {
        if (!is_user_logged_in()) {
            return false;
        }

        $lesson_id = $request->get_param('id');
        return fiqh_can_access_lesson($lesson_id);
    }

    /**
     * فحص صلاحية المعلم
     */
    public function check_teacher_permission() {
        if (!is_user_logged_in()) {
            return false;
        }

        $user = wp_get_current_user();
        return in_array('teacher', $user->roles) || in_array('administrator', $user->roles);
    }

    /**
     * فحص صلاحية المدير
     */
    public function check_admin_permission() {
        return current_user_can('manage_options');
    }
}

// تهيئة
FiqhLearning_REST_API::get_instance();
