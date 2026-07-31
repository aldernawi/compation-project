import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/empty_state.dart';
import '../../../core/widgets/loading_widget.dart';
import '../models/notification_model.dart';
import '../providers/notification_provider.dart';

class NotificationsScreen extends ConsumerStatefulWidget {
  const NotificationsScreen({super.key});

  @override
  ConsumerState<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends ConsumerState<NotificationsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(notificationProvider.notifier).load();
    });
  }

  String _formatDate(DateTime date) {
    final now = DateTime.now();
    final diff = now.difference(date);
    if (diff.inMinutes < 1) return 'الآن';
    if (diff.inMinutes < 60) return 'منذ ${diff.inMinutes} دقيقة';
    if (diff.inHours < 24) return 'منذ ${diff.inHours} ساعة';
    if (diff.inDays < 7) return 'منذ ${diff.inDays} يوم';
    final months = [
      'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
      'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];
    return '${date.day} ${months[date.month - 1]}';
  }

  IconData _iconFor(NotificationModel n) {
    if (n.data.containsKey('status')) {
      switch (n.data['status']) {
        case 'accepted':
          return Icons.check_circle_outline;
        case 'rejected':
          return Icons.cancel_outlined;
        case 'under_review':
          return Icons.visibility_outlined;
        case 'under_evaluation':
        case 'evaluated':
          return Icons.star_outline;
        default:
          return Icons.notifications_outlined;
      }
    }
    return Icons.campaign_outlined;
  }

  Color _colorFor(NotificationModel n) {
    if (n.data.containsKey('status')) {
      switch (n.data['status']) {
        case 'accepted':
          return AppColors.success;
        case 'rejected':
          return AppColors.error;
        case 'under_review':
          return AppColors.warning;
        case 'under_evaluation':
        case 'evaluated':
          return AppColors.secondary;
        default:
          return AppColors.info;
      }
    }
    return AppColors.primary;
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(notificationProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('الإشعارات'),
        automaticallyImplyLeading: false,
      ),
      body: RefreshIndicator(
        onRefresh: () => ref.read(notificationProvider.notifier).load(),
        child: state.status == NotificationStatus.loading && state.notifications.isEmpty
            ? const LoadingWidget(message: 'جاري تحميل الإشعارات...')
            : state.notifications.isEmpty
                ? const EmptyState(
                    icon: Icons.notifications_none_outlined,
                    title: 'لا توجد إشعارات',
                    subtitle: 'لم تصلك أي إشعارات بعد',
                  )
                : ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: state.notifications.length,
                    itemBuilder: (context, index) {
                      final n = state.notifications[index];
                      final color = _colorFor(n);
                      return Card(
                        margin: const EdgeInsets.only(bottom: 12),
                        color: n.isRead ? null : color.withValues(alpha: 0.03),
                        child: InkWell(
                          onTap: () {
                            if (!n.isRead) {
                              ref.read(notificationProvider.notifier).markRead(n.id);
                            }
                            if (n.data.containsKey('submission_id')) {
                              context.push('/submissions/${n.data['submission_id']}');
                            } else if (n.data.containsKey('competition_id')) {
                              context.push('/competitions/${n.data['competition_id']}');
                            }
                          },
                          borderRadius: BorderRadius.circular(16),
                          child: Padding(
                            padding: const EdgeInsets.all(16),
                            child: Row(
                              children: [
                                Container(
                                  width: 44,
                                  height: 44,
                                  decoration: BoxDecoration(
                                    color: color.withValues(alpha: 0.1),
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Icon(_iconFor(n), color: color),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        n.title,
                                        style: TextStyle(
                                          fontWeight: n.isRead ? FontWeight.normal : FontWeight.bold,
                                          color: AppColors.textPrimary,
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        n.body,
                                        style: const TextStyle(
                                          color: AppColors.textSecondary,
                                          fontSize: 13,
                                        ),
                                      ),
                                      if (n.createdAt != null) ...[
                                        const SizedBox(height: 4),
                                        Text(
                                          _formatDate(n.createdAt!),
                                          style: const TextStyle(
                                            color: AppColors.textSecondary,
                                            fontSize: 12,
                                          ),
                                        ),
                                      ],
                                    ],
                                  ),
                                ),
                                if (!n.isRead)
                                  Container(
                                    width: 8,
                                    height: 8,
                                    decoration: BoxDecoration(
                                      color: color,
                                      shape: BoxShape.circle,
                                    ),
                                  ),
                              ],
                            ),
                          ),
                        ),
                      );
                    },
                  ),
      ),
    );
  }
}
