import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../utils/config.dart';
import '../models/user_model.dart';
import '../models/science_model.dart';
import '../models/course_model.dart';
import '../models/lesson_model.dart';
import '../models/question_model.dart';

class ApiService {
  static Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString(AppConfig.tokenKey);

    final headers = {
      'Content-Type': 'application/json',
    };

    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }

    return headers;
  }

  // تسجيل الدخول
  static Future<UserModel> login(String username, String password) async {
    final response = await http.post(
      Uri.parse(AppConfig.loginEndpoint),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'username': username,
        'password': password,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        final user = UserModel.fromJson(data['data']);

        // حفظ بيانات المستخدم
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(AppConfig.tokenKey, user.token);
        await prefs.setInt(AppConfig.userIdKey, user.id);
        await prefs.setString(AppConfig.usernameKey, user.username);

        return user;
      } else {
        throw Exception(data['message'] ?? 'فشل تسجيل الدخول');
      }
    } else {
      throw Exception('فشل تسجيل الدخول: ${response.statusCode}');
    }
  }

  // تسجيل الخروج
  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConfig.tokenKey);
    await prefs.remove(AppConfig.userIdKey);
    await prefs.remove(AppConfig.usernameKey);
  }

  // التحقق من تسجيل الدخول
  static Future<bool> isLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(AppConfig.tokenKey) != null;
  }

  // الحصول على بيانات المستخدم الحالي
  static Future<UserModel> getCurrentUser() async {
    final headers = await _getHeaders();
    final response = await http.get(
      Uri.parse(AppConfig.userEndpoint),
      headers: headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return UserModel.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'فشل جلب بيانات المستخدم');
      }
    } else {
      throw Exception('فشل جلب بيانات المستخدم: ${response.statusCode}');
    }
  }

  // الحصول على العلوم
  static Future<List<ScienceModel>> getSciences() async {
    final headers = await _getHeaders();
    final response = await http.get(
      Uri.parse(AppConfig.sciencesEndpoint),
      headers: headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return (data['data'] as List)
            .map((s) => ScienceModel.fromJson(s))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'فشل جلب العلوم');
      }
    } else {
      throw Exception('فشل جلب العلوم: ${response.statusCode}');
    }
  }

  // الحصول على مقرر واحد
  static Future<CourseModel> getCourse(int courseId) async {
    final headers = await _getHeaders();
    final response = await http.get(
      Uri.parse('${AppConfig.coursesEndpoint}/$courseId'),
      headers: headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return CourseModel.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'فشل جلب المقرر');
      }
    } else {
      throw Exception('فشل جلب المقرر: ${response.statusCode}');
    }
  }

  // الحصول على دروس المقرر
  static Future<List<LessonModel>> getCourseLessons(int courseId) async {
    final headers = await _getHeaders();
    final response = await http.get(
      Uri.parse('${AppConfig.lessonsEndpoint}?course_id=$courseId'),
      headers: headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return (data['data'] as List)
            .map((l) => LessonModel.fromJson(l))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'فشل جلب الدروس');
      }
    } else {
      throw Exception('فشل جلب الدروس: ${response.statusCode}');
    }
  }

  // الحصول على درس واحد
  static Future<LessonModel> getLesson(int lessonId) async {
    final headers = await _getHeaders();
    final response = await http.get(
      Uri.parse('${AppConfig.lessonsEndpoint}/$lessonId'),
      headers: headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return LessonModel.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'فشل جلب الدرس');
      }
    } else {
      throw Exception('فشل جلب الدرس: ${response.statusCode}');
    }
  }

  // تحديث تقدم الدرس
  static Future<bool> updateProgress(int lessonId, int percentage) async {
    final headers = await _getHeaders();
    final response = await http.post(
      Uri.parse('${AppConfig.progressEndpoint}/update'),
      headers: headers,
      body: jsonEncode({
        'lesson_id': lessonId,
        'percentage': percentage,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] == true;
    }
    return false;
  }

  // الحصول على الأسئلة
  static Future<List<QuestionModel>> getQuestions({
    int? courseId,
    int? lessonId,
    String? status,
  }) async {
    final headers = await _getHeaders();

    String url = AppConfig.questionsEndpoint;
    List<String> params = [];
    if (courseId != null) params.add('course_id=$courseId');
    if (lessonId != null) params.add('lesson_id=$lessonId');
    if (status != null) params.add('status=$status');

    if (params.isNotEmpty) {
      url += '?${params.join('&')}';
    }

    final response = await http.get(
      Uri.parse(url),
      headers: headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return (data['data'] as List)
            .map((q) => QuestionModel.fromJson(q))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'فشل جلب الأسئلة');
      }
    } else {
      throw Exception('فشل جلب الأسئلة: ${response.statusCode}');
    }
  }

  // إضافة سؤال
  static Future<int> createQuestion({
    required int courseId,
    required int lessonId,
    required String question,
    bool isAnonymous = false,
  }) async {
    final headers = await _getHeaders();
    final response = await http.post(
      Uri.parse(AppConfig.questionsEndpoint),
      headers: headers,
      body: jsonEncode({
        'course_id': courseId,
        'lesson_id': lessonId,
        'question': question,
        'is_anonymous': isAnonymous,
      }),
    );

    if (response.statusCode == 201 || response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return data['question_id'] ?? 0;
      } else {
        throw Exception(data['message'] ?? 'فشل إضافة السؤال');
      }
    } else {
      throw Exception('فشل إضافة السؤال: ${response.statusCode}');
    }
  }
}
