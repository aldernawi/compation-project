import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/competition_card.dart';
import '../../../core/widgets/empty_state.dart';
import '../../../core/widgets/loading_widget.dart';
import '../providers/competition_provider.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(competitionListProvider.notifier).load(reset: true);
    });
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(competitionListProvider);

    final openCompetitions = state.competitions.where((c) => c.status == 'open').toList();
    final upcomingCompetitions = state.competitions.where((c) => c.status == 'upcoming').toList();
    final finishedCompetitions = state.competitions.where((c) => c.status == 'finished').toList();

    return Scaffold(
      appBar: AppBar(
        title: const Text('الرئيسية'),
        automaticallyImplyLeading: false,
      ),
      body: RefreshIndicator(
        onRefresh: () => ref.read(competitionListProvider.notifier).load(reset: true),
        child: state.status == CompetitionListStatus.loading && state.competitions.isEmpty
            ? const LoadingWidget(message: 'جاري تحميل المسابقات...')
            : state.competitions.isEmpty
                ? const EmptyState(
                    icon: Icons.emoji_events_outlined,
                    title: 'لا توجد مسابقات',
                    subtitle: 'لم يتم إضافة أي مسابقات بعد',
                  )
                : ListView(
                    padding: const EdgeInsets.all(16),
                    children: [
                      if (openCompetitions.isNotEmpty) ...[
                        _sectionTitle('المسابقات المفتوحة', Icons.play_circle_outline),
                        ...openCompetitions.map((c) => CompetitionCard(
                              title: c.title,
                              status: c.status,
                              typeName: c.competitionType?.name,
                              startsAt: c.startsAt,
                              endsAt: c.endsAt,
                              onTap: () => context.push('/competitions/${c.id}'),
                            )),
                      ],
                      if (upcomingCompetitions.isNotEmpty) ...[
                        const SizedBox(height: 16),
                        _sectionTitle('المسابقات القادمة', Icons.upcoming_outlined),
                        ...upcomingCompetitions.map((c) => CompetitionCard(
                              title: c.title,
                              status: c.status,
                              typeName: c.competitionType?.name,
                              startsAt: c.startsAt,
                              endsAt: c.endsAt,
                              onTap: () => context.push('/competitions/${c.id}'),
                            )),
                      ],
                      if (finishedCompetitions.isNotEmpty) ...[
                        const SizedBox(height: 16),
                        _sectionTitle('المسابقات المنتهية', Icons.history_outlined),
                        ...finishedCompetitions.map((c) => CompetitionCard(
                              title: c.title,
                              status: c.status,
                              typeName: c.competitionType?.name,
                              startsAt: c.startsAt,
                              endsAt: c.endsAt,
                              onTap: () => context.push('/competitions/${c.id}'),
                            )),
                      ],
                    ],
                  ),
      ),
    );
  }

  Widget _sectionTitle(String title, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, top: 8),
      child: Row(
        children: [
          Icon(icon, color: AppColors.primary, size: 20),
          const SizedBox(width: 8),
          Text(
            title,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
        ],
      ),
    );
  }
}
