class QuestionModel {
  final int id;
  final int userId;
  final int? courseId;
  final int? lessonId;
  final String question;
  final String? answer;
  final bool isAnonymous;
  final String status;
  final String? answeredBy;
  final String? answeredAt;
  final String createdAt;

  QuestionModel({
    required this.id,
    required this.userId,
    this.courseId,
    this.lessonId,
    required this.question,
    this.answer,
    this.isAnonymous = false,
    required this.status,
    this.answeredBy,
    this.answeredAt,
    required this.createdAt,
  });

  factory QuestionModel.fromJson(Map<String, dynamic> json) {
    return QuestionModel(
      id: int.parse(json['id'].toString()),
      userId: int.parse(json['user_id'].toString()),
      courseId: json['course_id'] != null ? int.parse(json['course_id'].toString()) : null,
      lessonId: json['lesson_id'] != null ? int.parse(json['lesson_id'].toString()) : null,
      question: json['question'],
      answer: json['answer'],
      isAnonymous: json['is_anonymous'] == 1 || json['is_anonymous'] == true,
      status: json['status'],
      answeredBy: json['answered_by']?.toString(),
      answeredAt: json['answered_at'],
      createdAt: json['created_at'],
    );
  }

  bool get isAnswered => status == 'answered' && answer != null;
}
