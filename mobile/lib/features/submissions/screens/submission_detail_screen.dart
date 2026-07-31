import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/empty_state.dart';
import '../../../core/widgets/loading_widget.dart';
import '../../../core/widgets/status_badge.dart';
import '../providers/submission_provider.dart';

class SubmissionDetailScreen extends ConsumerWidget {
  final int id;

  const SubmissionDetailScreen({super.key, required this.id});

  String _formatDate(DateTime date) {
    final months = [
      'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
      'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];
    return '${date.day} ${months[date.month - 1]} ${date.year}';
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final submissionAsync = ref.watch(submissionDetailProvider(id));

    return Scaffold(
      appBar: AppBar(title: const Text('تفاصيل المشاركة')),
      body: submissionAsync.when(
        loading: () => const LoadingWidget(message: 'جاري التحميل...'),
        error: (e, _) => EmptyState(
          icon: Icons.error_outline,
          title: 'حدث خطأ',
          subtitle: e.toString(),
        ),
        data: (submission) {
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              submission.competition?.title ?? 'مسابقة',
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          StatusBadge(status: submission.status),
                        ],
                      ),
                      if (submission.createdAt != null) ...[
                        const SizedBox(height: 8),
                        Text(
                          'تاريخ المشاركة: ${_formatDate(submission.createdAt!)}',
                          style: const TextStyle(color: AppColors.textSecondary),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
              if (submission.textContent != null && submission.textContent!.isNotEmpty)
                _sectionCard('المحتوى النصي', submission.textContent!),
              if (submission.linkUrl != null && submission.linkUrl!.isNotEmpty) ...[
                const SizedBox(height: 16),
                _sectionCard('الرابط', submission.linkUrl!, isLink: true),
              ],
              if (submission.mediaUrl != null && submission.mediaUrl!.isNotEmpty) ...[
                const SizedBox(height: 16),
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'الملف المرفق',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 12),
                        if (submission.mediaUrl!.contains('.pdf') ||
                            submission.mediaUrl!.contains('pdf')) ...[
                          const Row(
                            children: [
                              Icon(Icons.picture_as_pdf, color: AppColors.error, size: 32),
                              SizedBox(width: 12),
                              Text('ملف PDF'),
                            ],
                          ),
                        ] else ...[
                          ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: CachedNetworkImage(
                              imageUrl: submission.mediaUrl!.startsWith('http')
                                  ? submission.mediaUrl!
                                  : '${AppConstants.baseMediaUrl}${submission.mediaUrl!}',
                              fit: BoxFit.cover,
                              placeholder: (_, __) => const SizedBox(
                                height: 200,
                                child: Center(child: CircularProgressIndicator()),
                              ),
                              errorWidget: (_, __, ___) => Container(
                                height: 200,
                                color: AppColors.background,
                                child: const Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.broken_image, size: 48, color: AppColors.textSecondary),
                                    SizedBox(height: 8),
                                    Text('تعذر تحميل الصورة', style: TextStyle(color: AppColors.textSecondary)),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                ),
              ],
              if (submission.rejectionReason != null) ...[
                const SizedBox(height: 16),
                Card(
                  color: AppColors.error.withValues(alpha: 0.05),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.cancel_outlined, color: AppColors.error),
                            SizedBox(width: 8),
                            Text(
                              'سبب الرفض',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: AppColors.error,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          submission.rejectionReason!,
                          style: const TextStyle(color: AppColors.textSecondary),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
              if (submission.averageScore != null) ...[
                const SizedBox(height: 16),
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        const Icon(Icons.star, color: AppColors.secondary),
                        const SizedBox(width: 12),
                        const Text('متوسط التقييم: ', style: TextStyle(fontWeight: FontWeight.w600)),
                        Text(
                          submission.averageScore!.toStringAsFixed(1),
                          style: const TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                            color: AppColors.primary,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
              if (submission.isWinner) ...[
                const SizedBox(height: 16),
                Card(
                  color: AppColors.success.withValues(alpha: 0.05),
                  child: const Padding(
                    padding: EdgeInsets.all(16),
                    child: Row(
                      children: [
                        Icon(Icons.emoji_events, color: AppColors.success, size: 32),
                        SizedBox(width: 12),
                        Text(
                          'مبروك! أنت من الفائزين',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: AppColors.success,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
              if (submission.rank != null) ...[
                const SizedBox(height: 16),
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        const Icon(Icons.leaderboard_outlined, color: AppColors.primary),
                        const SizedBox(width: 12),
                        const Text('الترتيب: ', style: TextStyle(fontWeight: FontWeight.w600)),
                        Text(
                          '#${submission.rank}',
                          style: const TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                            color: AppColors.primary,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
              const SizedBox(height: 24),
              if (submission.competition != null)
                OutlinedButton.icon(
                  onPressed: () => context.push('/competitions/${submission.competition!.id}'),
                  icon: const Icon(Icons.emoji_events_outlined),
                  label: const Text('عرض المسابقة'),
                ),
            ],
          );
        },
      ),
    );
  }

  Widget _sectionCard(String title, String content, {bool isLink = false}) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            SelectableText(
              content,
              style: TextStyle(
                color: isLink ? AppColors.primary : AppColors.textSecondary,
                decoration: isLink ? TextDecoration.underline : null,
                height: 1.6,
              ),
              textDirection: isLink ? TextDirection.ltr : TextDirection.rtl,
            ),
          ],
        ),
      ),
    );
  }
}
