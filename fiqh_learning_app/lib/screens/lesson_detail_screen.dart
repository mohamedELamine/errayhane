import 'package:flutter/material.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:url_launcher/url_launcher.dart';
import '../models/lesson_model.dart';
import '../models/question_model.dart';
import '../services/api_service.dart';

class LessonDetailScreen extends StatefulWidget {
  final LessonModel lesson;
  final int courseId;

  const LessonDetailScreen({
    super.key,
    required this.lesson,
    required this.courseId,
  });

  @override
  State<LessonDetailScreen> createState() => _LessonDetailScreenState();
}

class _LessonDetailScreenState extends State<LessonDetailScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  YoutubePlayerController? _youtubeController;
  List<QuestionModel>? _questions;
  bool _isLoadingQuestions = false;
  final _questionController = TextEditingController();
  bool _isAnonymous = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);

    // تهيئة مشغل YouTube إذا كان الدرس يحتوي على فيديو YouTube
    if (widget.lesson.getVideoType() == 'youtube') {
      final videoId = widget.lesson.getVideoId();
      if (videoId.isNotEmpty) {
        _youtubeController = YoutubePlayerController(
          initialVideoId: videoId,
          flags: const YoutubePlayerFlags(
            autoPlay: false,
            mute: false,
          ),
        );

        // تتبع التقدم
        _youtubeController!.addListener(() {
          if (_youtubeController!.value.isReady) {
            final progress = (_youtubeController!.value.position.inSeconds /
                    _youtubeController!.metadata.duration.inSeconds *
                    100)
                .round();
            if (progress > 0 && progress % 10 == 0) {
              // تحديث كل 10%
              _updateProgress(progress);
            }
          }
        });
      }
    }

    _loadQuestions();
  }

  @override
  void dispose() {
    _tabController.dispose();
    _youtubeController?.dispose();
    _questionController.dispose();
    super.dispose();
  }

  Future<void> _updateProgress(int percentage) async {
    await ApiService.updateProgress(widget.lesson.id, percentage);
  }

  Future<void> _loadQuestions() async {
    setState(() => _isLoadingQuestions = true);

    try {
      final questions = await ApiService.getQuestions(
        lessonId: widget.lesson.id,
      );
      setState(() {
        _questions = questions;
        _isLoadingQuestions = false;
      });
    } catch (e) {
      setState(() => _isLoadingQuestions = false);
    }
  }

  Future<void> _submitQuestion() async {
    if (_questionController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('الرجاء كتابة السؤال')),
      );
      return;
    }

    try {
      await ApiService.createQuestion(
        courseId: widget.courseId,
        lessonId: widget.lesson.id,
        question: _questionController.text.trim(),
        isAnonymous: _isAnonymous,
      );

      _questionController.clear();
      setState(() => _isAnonymous = false);
      _loadQuestions();

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('تم إرسال السؤال بنجاح'),
          backgroundColor: Colors.green,
        ),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('فشل إرسال السؤال: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.lesson.title),
        backgroundColor: Colors.teal,
        bottom: TabBar(
          controller: _tabController,
          tabs: const [
            Tab(icon: Icon(Icons.play_circle), text: 'الدرس'),
            Tab(icon: Icon(Icons.question_answer), text: 'الأسئلة'),
            Tab(icon: Icon(Icons.assignment), text: 'التمارين'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildLessonTab(),
          _buildQuestionsTab(),
          _buildExercisesTab(),
        ],
      ),
    );
  }

  Widget _buildLessonTab() {
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // مشغل الفيديو
          if (_youtubeController != null)
            YoutubePlayer(
              controller: _youtubeController!,
              showVideoProgressIndicator: true,
            )
          else if (widget.lesson.videoUrl != null &&
              widget.lesson.videoUrl!.isNotEmpty)
            Container(
              height: 200,
              color: Colors.grey[300],
              child: Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.video_library, size: 64, color: Colors.grey[600]),
                    const SizedBox(height: 8),
                    Text(
                      'فيديو من مصدر خارجي',
                      style: TextStyle(color: Colors.grey[600]),
                    ),
                    const SizedBox(height: 8),
                    ElevatedButton(
                      onPressed: () async {
                        final url = Uri.parse(widget.lesson.videoUrl!);
                        if (await canLaunchUrl(url)) {
                          await launchUrl(url);
                        }
                      },
                      child: const Text('فتح الفيديو'),
                    ),
                  ],
                ),
              ),
            ),

          // المحتوى
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // العنوان
                Text(
                  widget.lesson.title,
                  style: const TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 16),

                // المحتوى
                if (widget.lesson.content != null &&
                    widget.lesson.content!.isNotEmpty)
                  Html(
                    data: widget.lesson.content!,
                    style: {
                      'body': Style(
                        fontSize: FontSize(16),
                        lineHeight: const LineHeight(1.6),
                      ),
                    },
                  ),

                const SizedBox(height: 24),

                // الموارد الإضافية
                if (widget.lesson.audioUrl != null ||
                    widget.lesson.pdfUrl != null) ...[
                  const Text(
                    'الموارد الإضافية',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 12),

                  if (widget.lesson.audioUrl != null) ...[
                    Card(
                      child: ListTile(
                        leading: const Icon(Icons.headphones, color: Colors.blue),
                        title: const Text('ملف صوتي'),
                        trailing: const Icon(Icons.download),
                        onTap: () async {
                          final url = Uri.parse(widget.lesson.audioUrl!);
                          if (await canLaunchUrl(url)) {
                            await launchUrl(url);
                          }
                        },
                      ),
                    ),
                    const SizedBox(height: 8),
                  ],

                  if (widget.lesson.pdfUrl != null) ...[
                    Card(
                      child: ListTile(
                        leading:
                            const Icon(Icons.picture_as_pdf, color: Colors.red),
                        title: const Text('ملف PDF'),
                        trailing: const Icon(Icons.download),
                        onTap: () async {
                          final url = Uri.parse(widget.lesson.pdfUrl!);
                          if (await canLaunchUrl(url)) {
                            await launchUrl(url);
                          }
                        },
                      ),
                    ),
                  ],
                ],

                const SizedBox(height: 24),

                // زر إنهاء الدرس
                if (!widget.lesson.isCompleted)
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () async {
                        await _updateProgress(100);
                        if (!mounted) return;
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('تم إنهاء الدرس بنجاح'),
                            backgroundColor: Colors.green,
                          ),
                        );
                        Navigator.of(context).pop();
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                      ),
                      child: const Text(
                        'إنهاء الدرس',
                        style: TextStyle(fontSize: 18, color: Colors.white),
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuestionsTab() {
    return Column(
      children: [
        // نموذج إضافة سؤال
        Card(
          margin: const EdgeInsets.all(16),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'اسأل سؤالاً',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: _questionController,
                  maxLines: 3,
                  decoration: const InputDecoration(
                    hintText: 'اكتب سؤالك هنا...',
                    border: OutlineInputBorder(),
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Checkbox(
                      value: _isAnonymous,
                      onChanged: (value) {
                        setState(() => _isAnonymous = value ?? false);
                      },
                    ),
                    const Text('سؤال مجهول'),
                    const Spacer(),
                    ElevatedButton(
                      onPressed: _submitQuestion,
                      child: const Text('إرسال'),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),

        // قائمة الأسئلة
        Expanded(
          child: _isLoadingQuestions
              ? const Center(child: CircularProgressIndicator())
              : _questions == null || _questions!.isEmpty
                  ? const Center(
                      child: Text('لا توجد أسئلة بعد'),
                    )
                  : RefreshIndicator(
                      onRefresh: _loadQuestions,
                      child: ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: _questions!.length,
                        itemBuilder: (context, index) {
                          final question = _questions![index];
                          return _buildQuestionCard(question);
                        },
                      ),
                    ),
        ),
      ],
    );
  }

  Widget _buildQuestionCard(QuestionModel question) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  Icons.help_outline,
                  color: question.isAnswered ? Colors.green : Colors.orange,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    question.question,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              question.createdAt,
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
              ),
            ),
            if (question.isAnswered) ...[
              const Divider(height: 24),
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(Icons.check_circle, color: Colors.green, size: 20),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'الإجابة:',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            color: Colors.green,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(question.answer!),
                      ],
                    ),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildExercisesTab() {
    if (widget.lesson.exercises == null || widget.lesson.exercises!.isEmpty) {
      return const Center(
        child: Text('لا توجد تمارين متاحة'),
      );
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Html(
        data: widget.lesson.exercises!,
        style: {
          'body': Style(
            fontSize: FontSize(16),
            lineHeight: const LineHeight(1.6),
          ),
        },
      ),
    );
  }
}
