import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/widgets/empty_state.dart';
import '../../../core/widgets/loading_widget.dart';
import '../../../core/widgets/submission_card.dart';
import '../providers/submission_provider.dart';

class MySubmissionsScreen extends ConsumerStatefulWidget {
  const MySubmissionsScreen({super.key});

  @override
  ConsumerState<MySubmissionsScreen> createState() => _MySubmissionsScreenState();
}

class _MySubmissionsScreenState extends ConsumerState<MySubmissionsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(submissionListProvider.notifier).load();
    });
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(submissionListProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('مشاركاتي'),
        automaticallyImplyLeading: false,
      ),
      body: RefreshIndicator(
        onRefresh: () => ref.read(submissionListProvider.notifier).load(),
        child: state.status == SubmissionListStatus.loading && state.submissions.isEmpty
            ? const LoadingWidget(message: 'جاري تحميل المشاركات...')
            : state.submissions.isEmpty
                ? const EmptyState(
                    icon: Icons.send_outlined,
                    title: 'لا توجد مشاركات',
                    subtitle: 'لم تشارك في أي مسابقة بعد',
                  )
                : ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: state.submissions.length,
                    itemBuilder: (context, index) {
                      final s = state.submissions[index];
                      return SubmissionCard(
                        competitionTitle: s.competition?.title ?? 'مسابقة',
                        status: s.status,
                        createdAt: s.createdAt,
                        onTap: () => context.push('/submissions/${s.id}'),
                      );
                    },
                  ),
      ),
    );
  }
}
