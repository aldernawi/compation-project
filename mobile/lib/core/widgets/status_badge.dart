import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class StatusBadge extends StatelessWidget {
  final String status;
  final double fontSize;

  const StatusBadge({
    super.key,
    required this.status,
    this.fontSize = 12,
  });

  Color _color(String status) {
    switch (status) {
      case 'submitted':
        return AppColors.info;
      case 'under_review':
      case 'under_evaluation':
        return AppColors.warning;
      case 'accepted':
      case 'evaluated':
        return AppColors.success;
      case 'rejected':
        return AppColors.error;
      default:
        return AppColors.textSecondary;
    }
  }

  String _label(String status) {
    switch (status) {
      case 'submitted':
        return 'تم الإرسال';
      case 'under_review':
        return 'قيد المراجعة';
      case 'under_evaluation':
        return 'قيد التقييم';
      case 'accepted':
        return 'مقبول';
      case 'rejected':
        return 'مرفوض';
      case 'evaluated':
        return 'تم التقييم';
      case 'open':
        return 'مفتوحة';
      case 'upcoming':
        return 'قادمة';
      case 'closed':
        return 'مغلقة';
      case 'finished':
        return 'منتهية';
      default:
        return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    final color = _color(status);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withValues(alpha: 0.3)),
      ),
      child: Text(
        _label(status),
        style: TextStyle(
          color: color,
          fontSize: fontSize,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }
}
