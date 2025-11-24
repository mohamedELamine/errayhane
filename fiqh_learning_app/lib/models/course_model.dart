class CourseModel {
  final int id;
  final String title;
  final String? excerpt;
  final String? content;
  final String? thumbnail;
  final String? level;
  final bool isEnrolled;
  final String? bookUrl;
  final String? duration;
  final String? permalink;

  CourseModel({
    required this.id,
    required this.title,
    this.excerpt,
    this.content,
    this.thumbnail,
    this.level,
    this.isEnrolled = false,
    this.bookUrl,
    this.duration,
    this.permalink,
  });

  factory CourseModel.fromJson(Map<String, dynamic> json) {
    return CourseModel(
      id: json['id'],
      title: json['title'],
      excerpt: json['excerpt'],
      content: json['content'],
      thumbnail: json['thumbnail'],
      level: json['level']?.toString(),
      isEnrolled: json['is_enrolled'] ?? false,
      bookUrl: json['book_url'],
      duration: json['duration'],
      permalink: json['permalink'],
    );
  }
}
