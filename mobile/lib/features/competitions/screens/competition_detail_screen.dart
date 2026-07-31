import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_button.dart';
import '../../../core/widgets/empty_state.dart';
import '../../../core/widgets/loading_widget.dart';
import '../../../core/widgets/status_badge.dart';
import '../models/competition_model.dart';
import '../providers/competition_provider.dart';

class CompetitionDetailScreen extends ConsumerWidget {
  final int id;

  const CompetitionDetailScreen({super.key, required this.id});

  String _formatDate(DateTime date) {
    final months = [
      'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
      'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];
    return '${date.day} ${months[date.month - 1]} ${date.year}';
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final competitionAsync = ref.watch(competitionDetailProvider(id));
    final resultsAsync = ref.watch(competitionResultsProvider(id));

    return Scaffold(
      appBar: AppBar(title: const Text('تفاصيل المسابقة')),
      body: competitionAsync.when(
        loading: () => const LoadingWidget(message: 'جاري التحميل...'),
        error: (e, _) => EmptyState(
          icon: Icons.error_outline,
          title: 'حدث خطأ',
          subtitle: e.toString(),
        ),
        data: (competition) {
          final canParticipate = competition.status == 'open';

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
                              competition.title,
                              style: const TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.bold,
                                color: AppColors.textPrimary,
                              ),
                            ),
                          ),
                          StatusBadge(status: competition.status),
                        ],
                      ),
                      if (competition.organizer != null) ...[
                        const SizedBox(height: 12),
                        _infoRow(Icons.person_outline, 'المنظم', competition.organizer!.name),
                      ],
                      if (competition.competitionType != null) ...[
                        const SizedBox(height: 8),
                        _infoRow(Icons.category_outlined, 'النوع', competition.competitionType!.name),
                        const SizedBox(height: 8),
                        _infoRow(Icons.upload_file_outlined, 'نوع المشاركة',
                            _submissionKindLabel(competition.competitionType!.submissionKind)),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
              if (competition.description != null && competition.description!.isNotEmpty)
                _sectionCard('الوصف', competition.description!),
              if (competition.terms != null && competition.terms!.isNotEmpty) ...[
                const SizedBox(height: 16),
                _sectionCard('الشروط والأحكام', competition.terms!),
              ],
              const SizedBox(height: 16),
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'التواريخ',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 12),
                      if (competition.startsAt != null)
                        _infoRow(Icons.play_arrow, 'تاريخ البداية', _formatDate(competition.startsAt!)),
                      const SizedBox(height: 8),
                      if (competition.endsAt != null)
                        _infoRow(Icons.stop, 'تاريخ النهاية', _formatDate(competition.endsAt!)),
                    ],
                  ),
                ),
              ),
              if (competition.prizes.isNotEmpty) ...[
                const SizedBox(height: 16),
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'الجوائز',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 12),
                        ...competition.prizes.map((p) => Padding(
                              padding: const EdgeInsets.only(bottom: 8),
                              child: Row(
                                children: [
                                  Container(
                                    width: 32,
                                    height: 32,
                                    decoration: BoxDecoration(
                                      color: AppColors.secondary.withValues(alpha: 0.1),
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    child: Center(
                                      child: Text(
                                        '#${p.rank}',
                                        style: const TextStyle(
                                          fontWeight: FontWeight.bold,
                                          color: AppColors.secondary,
                                        ),
                                      ),
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          p.title,
                                          style: const TextStyle(fontWeight: FontWeight.w600),
                                        ),
                                        if (p.description != null)
                                          Text(
                                            p.description!,
                                            style: const TextStyle(
                                              color: AppColors.textSecondary,
                                              fontSize: 13,
                                            ),
                                          ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            )),
                      ],
                    ),
                  ),
                ),
              ],
              if (competition.status == 'finished') ...[
                const SizedBox(height: 16),
                resultsAsync.when(
                  loading: () => const LoadingWidget(message: 'جاري تحميل النتائج...'),
                  error: (_, __) => const SizedBox.shrink(),
                  data: (winners) {
                    if (winners.isEmpty) {
                      return const Card(
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: Text('لم يتم نشر النتائج بعد'),
                        ),
                      );
                    }
                    return Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'الفائزون',
                              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 12),
                            ...winners.map((w) => Padding(
                                  padding: const EdgeInsets.only(bottom: 8),
                                  child: Row(
                                    children: [
                                      Container(
                                        width: 32,
                                        height: 32,
                                        decoration: BoxDecoration(
                                          color: AppColors.primaryLight,
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Center(
                                          child: Text(
                                            '#${w.prize?.rank ?? 0}',
                                            style: const TextStyle(
                                              fontWeight: FontWeight.bold,
                                              color: AppColors.primary,
                                            ),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Text(
                                          w.participant?.name ?? 'مشارك',
                                          style: const TextStyle(fontWeight: FontWeight.w600),
                                        ),
                                      ),
                                      Text(
                                        w.prize?.title ?? '',
                                        style: const TextStyle(color: AppColors.textSecondary),
                                      ),
                                    ],
                                  ),
                                )),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ],
              const SizedBox(height: 24),
              if (canParticipate)
                AppButton(
                  label: 'شارك الآن',
                  icon: Icons.send,
                  onPressed: () => context.push('/competitions/$id/submit'),
                ),
            ],
          );
        },
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 18, color: AppColors.textSecondary),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: const TextStyle(color: AppColors.textSecondary, fontSize: 14),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
          ),
        ),
      ],
    );
  }

  Widget _sectionCard(String title, String content) {
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
            Text(
              content,
              style: const TextStyle(color: AppColors.textSecondary, height: 1.6),
            ),
          ],
        ),
      ),
    );
  }

  String _submissionKindLabel(String kind) {
    switch (kind) {
      case 'image':
        return 'صورة';
      case 'pdf':
        return 'ملف PDF';
      case 'text':
        return 'نص';
      case 'link':
        return 'رابط';
      case 'video':
        return 'فيديو';
      default:
        return kind;
    }
  }
}
