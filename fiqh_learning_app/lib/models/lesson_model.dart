class LessonModel {
  final int id;
  final String title;
  final String? excerpt;
  final String? content;
  final int? order;
  final String? videoUrl;
  final String? audioUrl;
  final String? pdfUrl;
  final String? duration;
  final String? exercises;
  final int? progressPercentage;
  final String? status;

  LessonModel({
    required this.id,
    required this.title,
    this.excerpt,
    this.content,
    this.order,
    this.videoUrl,
    this.audioUrl,
    this.pdfUrl,
    this.duration,
    this.exercises,
    this.progressPercentage,
    this.status,
  });

  factory LessonModel.fromJson(Map<String, dynamic> json) {
    return LessonModel(
      id: json['id'],
      title: json['title'],
      excerpt: json['excerpt'],
      content: json['content'],
      order: json['order'] != null ? int.tryParse(json['order'].toString()) : null,
      videoUrl: json['video_url'],
      audioUrl: json['audio_url'],
      pdfUrl: json['pdf_url'],
      duration: json['duration'],
      exercises: json['exercises'],
      progressPercentage: json['progress_percentage'],
      status: json['status'],
    );
  }

  bool get isCompleted => progressPercentage != null && progressPercentage! >= 100;

  String getVideoId() {
    if (videoUrl == null) return '';

    // YouTube
    if (videoUrl!.contains('youtube.com') || videoUrl!.contains('youtu.be')) {
      RegExp regExp = RegExp(
        r'(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})',
        caseSensitive: false,
        multiLine: false,
      );
      final match = regExp.firstMatch(videoUrl!);
      return match?.group(1) ?? '';
    }

    // Odysee/LBRY
    if (videoUrl!.contains('odysee.com') || videoUrl!.contains('lbry.tv')) {
      return videoUrl!;
    }

    return '';
  }

  String getVideoType() {
    if (videoUrl == null) return 'none';
    if (videoUrl!.contains('youtube.com') || videoUrl!.contains('youtu.be')) {
      return 'youtube';
    }
    if (videoUrl!.contains('odysee.com') || videoUrl!.contains('lbry.tv')) {
      return 'odysee';
    }
    return 'other';
  }
}
