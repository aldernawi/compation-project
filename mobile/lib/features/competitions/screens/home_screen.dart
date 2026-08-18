import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../../auth/providers/auth_provider.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final user = ref.watch(authProvider).user;
    final firstName = user?.name.trim().split(RegExp(r'\s+')).first ?? 'بك';

    return Scaffold(
      appBar: AppBar(
        title: const Text('الرئيسية'),
        automaticallyImplyLeading: false,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.emoji_events_outlined,
                    color: Colors.white, size: 40),
                const SizedBox(height: 16),
                Text(
                  'أهلاً $firstName',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'اكتشف المسابقات، قدّم أعمالك، وتابع نتائجك من مكان واحد.',
                  style: TextStyle(color: Colors.white70, height: 1.5),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),
          const Text(
            'كيف تستخدم المنصة؟',
            style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 12),
          const _GuideCard(
            number: '1',
            icon: Icons.search_outlined,
            title: 'اختر المسابقة المناسبة',
            description: 'اقرأ الشروط والجوائز والمواعيد قبل المشاركة.',
          ),
          const SizedBox(height: 12),
          const _GuideCard(
            number: '2',
            icon: Icons.upload_file_outlined,
            title: 'أرسل مشاركتك',
            description: 'قدّم صورة أو ملفًا أو نصًا بحسب نوع المسابقة.',
          ),
          const SizedBox(height: 12),
          const _GuideCard(
            number: '3',
            icon: Icons.notifications_active_outlined,
            title: 'تابع الحالة والنتائج',
            description: 'ستصلك إشعارات بحالة المشاركة والنتائج النهائية.',
          ),
          const SizedBox(height: 24),
          FilledButton.icon(
            onPressed: () => context.go('/competitions'),
            icon: const Icon(Icons.emoji_events_outlined),
            label: const Text('استعرض المسابقات'),
          ),
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: () => context.go('/my-submissions'),
            icon: const Icon(Icons.folder_open_outlined),
            label: const Text('مشاركاتي'),
          ),
        ],
      ),
    );
  }
}

class _GuideCard extends StatelessWidget {
  final String number;
  final IconData icon;
  final String title;
  final String description;

  const _GuideCard({
    required this.number,
    required this.icon,
    required this.title,
    required this.description,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: AppColors.primaryLight,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Center(
                child: Text(
                  number,
                  style: const TextStyle(
                      color: AppColors.primary, fontWeight: FontWeight.bold),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(icon, size: 20, color: AppColors.primary),
                      const SizedBox(width: 8),
                      Expanded(
                          child: Text(title,
                              style: const TextStyle(
                                  fontWeight: FontWeight.bold))),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(description,
                      style: const TextStyle(
                          color: AppColors.textSecondary, height: 1.5)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
