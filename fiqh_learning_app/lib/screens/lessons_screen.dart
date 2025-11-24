import 'package:flutter/material.dart';
import '../models/course_model.dart';
import '../models/lesson_model.dart';
import '../services/api_service.dart';
import 'lesson_detail_screen.dart';

class LessonsScreen extends StatefulWidget {
  final CourseModel course;

  const LessonsScreen({super.key, required this.course});

  @override
  State<LessonsScreen> createState() => _LessonsScreenState();
}

class _LessonsScreenState extends State<LessonsScreen> {
  List<LessonModel>? _lessons;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadLessons();
  }

  Future<void> _loadLessons() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final lessons = await ApiService.getCourseLessons(widget.course.id);
      // ترتيب الدروس حسب الترتيب
      lessons.sort((a, b) {
        final orderA = a.order ?? 0;
        final orderB = b.order ?? 0;
        return orderA.compareTo(orderB);
      });

      setState(() {
        _lessons = lessons;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.course.title),
        backgroundColor: Colors.teal,
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(
        child: CircularProgressIndicator(),
      );
    }

    if (_error != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.error_outline,
              size: 64,
              color: Colors.red,
            ),
            const SizedBox(height: 16),
            Text(
              'حدث خطأ',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32),
              child: Text(
                _error!,
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.grey[600]),
              ),
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _loadLessons,
              child: const Text('إعادة المحاولة'),
            ),
          ],
        ),
      );
    }

    if (_lessons == null || _lessons!.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.folder_open,
              size: 64,
              color: Colors.grey,
            ),
            const SizedBox(height: 16),
            Text(
              'لا توجد دروس متاحة',
              style: TextStyle(
                fontSize: 18,
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadLessons,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _lessons!.length,
        itemBuilder: (context, index) {
          final lesson = _lessons![index];
          return _buildLessonCard(lesson, index + 1);
        },
      ),
    );
  }

  Widget _buildLessonCard(LessonModel lesson, int displayOrder) {
    final hasVideo = lesson.videoUrl != null && lesson.videoUrl!.isNotEmpty;
    final hasAudio = lesson.audioUrl != null && lesson.audioUrl!.isNotEmpty;
    final hasPdf = lesson.pdfUrl != null && lesson.pdfUrl!.isNotEmpty;

    return Card(
      elevation: 4,
      margin: const EdgeInsets.only(bottom: 16),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () {
          Navigator.of(context).push(
            MaterialPageRoute(
              builder: (_) => LessonDetailScreen(
                lesson: lesson,
                courseId: widget.course.id,
              ),
            ),
          ).then((_) => _loadLessons()); // تحديث عند العودة
        },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  // رقم الدرس
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: lesson.isCompleted
                          ? Colors.green
                          : Colors.teal.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Center(
                      child: lesson.isCompleted
                          ? const Icon(
                              Icons.check,
                              color: Colors.white,
                            )
                          : Text(
                              '$displayOrder',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: Colors.teal,
                              ),
                            ),
                    ),
                  ),
                  const SizedBox(width: 12),

                  // عنوان الدرس
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          lesson.title,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        if (lesson.duration != null &&
                            lesson.duration!.isNotEmpty) ...[
                          const SizedBox(height: 4),
                          Text(
                            lesson.duration!,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),

                  // أيقونة السهم
                  Icon(
                    Icons.arrow_forward_ios,
                    size: 16,
                    color: Colors.grey[400],
                  ),
                ],
              ),

              // شريط التقدم
              if (lesson.progressPercentage != null &&
                  lesson.progressPercentage! > 0) ...[
                const SizedBox(height: 12),
                ClipRRect(
                  borderRadius: BorderRadius.circular(4),
                  child: LinearProgressIndicator(
                    value: lesson.progressPercentage! / 100,
                    backgroundColor: Colors.grey[200],
                    valueColor: AlwaysStoppedAnimation<Color>(
                      lesson.isCompleted ? Colors.green : Colors.teal,
                    ),
                    minHeight: 6,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  '${lesson.progressPercentage}% مكتمل',
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                  ),
                ),
              ],

              // الوصف
              if (lesson.excerpt != null && lesson.excerpt!.isNotEmpty) ...[
                const SizedBox(height: 12),
                Text(
                  lesson.excerpt!,
                  style: TextStyle(
                    color: Colors.grey[700],
                    fontSize: 14,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],

              // الموارد المتاحة
              if (hasVideo || hasAudio || hasPdf) ...[
                const SizedBox(height: 12),
                Wrap(
                  spacing: 8,
                  children: [
                    if (hasVideo)
                      Chip(
                        avatar: const Icon(Icons.play_circle, size: 16),
                        label: const Text('فيديو', style: TextStyle(fontSize: 12)),
                        backgroundColor: Colors.red.withOpacity(0.1),
                      ),
                    if (hasAudio)
                      Chip(
                        avatar: const Icon(Icons.headphones, size: 16),
                        label: const Text('صوت', style: TextStyle(fontSize: 12)),
                        backgroundColor: Colors.blue.withOpacity(0.1),
                      ),
                    if (hasPdf)
                      Chip(
                        avatar: const Icon(Icons.picture_as_pdf, size: 16),
                        label: const Text('PDF', style: TextStyle(fontSize: 12)),
                        backgroundColor: Colors.orange.withOpacity(0.1),
                      ),
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
