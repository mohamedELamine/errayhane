import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/science_model.dart';
import '../models/course_model.dart';
import 'lessons_screen.dart';

class CoursesScreen extends StatelessWidget {
  final ScienceModel science;

  const CoursesScreen({super.key, required this.science});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(science.name),
        backgroundColor: Colors.teal,
      ),
      body: science.courses.isEmpty
          ? Center(
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
                    'لا توجد مقررات متاحة',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey[600],
                    ),
                  ),
                ],
              ),
            )
          : ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: science.courses.length,
              itemBuilder: (context, index) {
                final course = science.courses[index];
                return _buildCourseCard(context, course);
              },
            ),
    );
  }

  Widget _buildCourseCard(BuildContext context, CourseModel course) {
    return Card(
      elevation: 4,
      margin: const EdgeInsets.only(bottom: 16),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: course.isEnrolled
            ? () {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => LessonsScreen(course: course),
                  ),
                );
              }
            : () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('أنت غير مسجل في هذا المقرر'),
                    backgroundColor: Colors.orange,
                  ),
                );
              },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // صورة المقرر
            if (course.thumbnail != null && course.thumbnail!.isNotEmpty)
              ClipRRect(
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(12),
                  topRight: Radius.circular(12),
                ),
                child: CachedNetworkImage(
                  imageUrl: course.thumbnail!,
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => Container(
                    height: 200,
                    color: Colors.grey[200],
                    child: const Center(
                      child: CircularProgressIndicator(),
                    ),
                  ),
                  errorWidget: (context, url, error) => Container(
                    height: 200,
                    color: Colors.grey[200],
                    child: const Icon(
                      Icons.image_not_supported,
                      size: 64,
                      color: Colors.grey,
                    ),
                  ),
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // العنوان
                  Text(
                    course.title,
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),

                  // الوصف
                  if (course.excerpt != null && course.excerpt!.isNotEmpty) ...[
                    Text(
                      course.excerpt!,
                      style: TextStyle(
                        color: Colors.grey[700],
                        fontSize: 14,
                      ),
                      maxLines: 3,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 12),
                  ],

                  // المعلومات
                  Wrap(
                    spacing: 12,
                    runSpacing: 8,
                    children: [
                      if (course.level != null && course.level!.isNotEmpty)
                        _buildInfoChip(
                          icon: Icons.signal_cellular_alt,
                          label: 'المستوى ${course.level}',
                          color: Colors.blue,
                        ),
                      if (course.duration != null && course.duration!.isNotEmpty)
                        _buildInfoChip(
                          icon: Icons.access_time,
                          label: course.duration!,
                          color: Colors.orange,
                        ),
                      _buildInfoChip(
                        icon: course.isEnrolled ? Icons.check_circle : Icons.lock,
                        label: course.isEnrolled ? 'مسجل' : 'غير مسجل',
                        color: course.isEnrolled ? Colors.green : Colors.red,
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoChip({
    required IconData icon,
    required String label,
    required Color color,
  }) {
    return Chip(
      avatar: Icon(icon, size: 16, color: color),
      label: Text(
        label,
        style: TextStyle(fontSize: 12, color: color),
      ),
      backgroundColor: color.withOpacity(0.1),
      side: BorderSide(color: color.withOpacity(0.3)),
    );
  }
}
