import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/competition_card.dart';
import '../../../core/widgets/empty_state.dart';
import '../../../core/widgets/loading_widget.dart';
import '../providers/competition_provider.dart';

class CompetitionListScreen extends ConsumerStatefulWidget {
  const CompetitionListScreen({super.key});

  @override
  ConsumerState<CompetitionListScreen> createState() => _CompetitionListScreenState();
}

class _CompetitionListScreenState extends ConsumerState<CompetitionListScreen> {
  final _searchController = TextEditingController();
  String _statusFilter = 'all';

  final _filters = [
    {'value': 'all', 'label': 'الكل'},
    {'value': 'open', 'label': 'مفتوحة'},
    {'value': 'upcoming', 'label': 'قادمة'},
    {'value': 'finished', 'label': 'منتهية'},
  ];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(competitionListProvider.notifier).load(reset: true);
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _applyFilter() {
    ref.read(competitionListProvider.notifier).load(
          search: _searchController.text.trim(),
          statusFilter: _statusFilter,
          reset: true,
        );
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(competitionListProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('المسابقات'),
        automaticallyImplyLeading: false,
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              controller: _searchController,
              onSubmitted: (_) => _applyFilter(),
              textDirection: TextDirection.rtl,
              decoration: InputDecoration(
                hintText: 'ابحث عن مسابقة...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          _applyFilter();
                        },
                      )
                    : null,
              ),
            ),
          ),
          SizedBox(
            height: 44,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: _filters.map((f) {
                final isSelected = _statusFilter == f['value'];
                return Padding(
                  padding: const EdgeInsets.only(left: 8),
                  child: FilterChip(
                    label: Text(f['label']!),
                    selected: isSelected,
                    onSelected: (_) {
                      setState(() => _statusFilter = f['value']!);
                      _applyFilter();
                    },
                    selectedColor: AppColors.primary,
                    labelStyle: TextStyle(
                      color: isSelected ? Colors.white : AppColors.textSecondary,
                    ),
                  ),
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: RefreshIndicator(
              onRefresh: () => ref.read(competitionListProvider.notifier).load(
                    search: _searchController.text.trim(),
                    statusFilter: _statusFilter,
                    reset: true,
                  ),
              child: state.status == CompetitionListStatus.loading && state.competitions.isEmpty
                  ? const LoadingWidget(message: 'جاري تحميل المسابقات...')
                  : state.competitions.isEmpty
                      ? const EmptyState(
                          icon: Icons.emoji_events_outlined,
                          title: 'لا توجد مسابقات',
                          subtitle: 'لم يتم العثور على مسابقات مطابقة',
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          itemCount: state.competitions.length,
                          itemBuilder: (context, index) {
                            final c = state.competitions[index];
                            return CompetitionCard(
                              title: c.title,
                              status: c.status,
                              typeName: c.competitionType?.name,
                              startsAt: c.startsAt,
                              endsAt: c.endsAt,
                              onTap: () => context.push('/competitions/${c.id}'),
                            );
                          },
                        ),
            ),
          ),
        ],
      ),
    );
  }
}
