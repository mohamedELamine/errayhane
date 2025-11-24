import 'course_model.dart';

class ScienceModel {
  final int id;
  final String name;
  final String slug;
  final String? description;
  final int coursesCount;
  final List<CourseModel> courses;

  ScienceModel({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    required this.coursesCount,
    required this.courses,
  });

  factory ScienceModel.fromJson(Map<String, dynamic> json) {
    return ScienceModel(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      description: json['description'],
      coursesCount: json['courses_count'] ?? 0,
      courses: (json['courses'] as List<dynamic>?)
              ?.map((c) => CourseModel.fromJson(c))
              .toList() ??
          [],
    );
  }
}
