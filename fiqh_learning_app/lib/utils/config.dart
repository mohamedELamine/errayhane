class AppConfig {
  // قم بتغيير هذا الرابط إلى رابط موقعك الفعلي
  static const String baseUrl = 'https://yourwebsite.com';
  static const String apiUrl = '$baseUrl/wp-json/fiqh-lms/v1';

  // API Endpoints
  static const String loginEndpoint = '$apiUrl/auth/login';
  static const String userEndpoint = '$apiUrl/auth/user';
  static const String sciencesEndpoint = '$apiUrl/sciences';
  static const String coursesEndpoint = '$apiUrl/courses';
  static const String lessonsEndpoint = '$apiUrl/lessons';
  static const String questionsEndpoint = '$apiUrl/questions';
  static const String progressEndpoint = '$apiUrl/progress';
  static const String enrollmentsEndpoint = '$apiUrl/enrollments';

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userIdKey = 'user_id';
  static const String usernameKey = 'username';
}
