import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class CompetitionCard extends StatelessWidget {
  final String title;
  final String status;
  final String? typeName;
  final DateTime? startsAt;
  final DateTime? endsAt;
  final VoidCallback? onTap;

  const CompetitionCard({
    super.key,
    required this.title,
    required this.status,
    this.typeName,
    this.startsAt,
    this.endsAt,
    this.onTap,
  });

  String _formatDate(DateTime? date) {
    if (date == null) return '';
    final months = [
      'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
      'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];
    return '${date.day} ${months[date.month - 1]} ${date.year}';
  }

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      title,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                  ),
                  _StatusChip(status: status),
                ],
              ),
              if (typeName != null) ...[
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.category_outlined, size: 16, color: AppColors.textSecondary),
                    const SizedBox(width: 4),
                    Text(
                      typeName!,
                      style: const TextStyle(color: AppColors.textSecondary, fontSize: 13),
                    ),
                  ],
                ),
              ],
              if (startsAt != null || endsAt != null) ...[
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.calendar_today_outlined, size: 16, color: AppColors.textSecondary),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(
                        startsAt != null && endsAt != null
                            ? '${_formatDate(startsAt)} - ${_formatDate(endsAt)}'
                            : startsAt != null
                                ? _formatDate(startsAt)
                                : _formatDate(endsAt),
                        style: const TextStyle(color: AppColors.textSecondary, fontSize: 13),
                      ),
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

class _StatusChip extends StatelessWidget {
  final String status;

  const _StatusChip({required this.status});

  @override
  Widget build(BuildContext context) {
    final colors = {
      'open': AppColors.success,
      'upcoming': AppColors.info,
      'closed': AppColors.textSecondary,
      'under_evaluation': AppColors.warning,
      'finished': AppColors.textSecondary,
    };
    final labels = {
      'open': 'مفتوحة',
      'upcoming': 'قادمة',
      'closed': 'مغلقة',
      'under_evaluation': 'قيد التقييم',
      'finished': 'منتهية',
    };
    final color = colors[status] ?? AppColors.textSecondary;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        labels[status] ?? status,
        style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.w600),
      ),
    );
  }
}
