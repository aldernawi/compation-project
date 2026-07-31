import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/dio_provider.dart';
import '../models/competition_model.dart';
import '../models/winner_model.dart';

enum CompetitionListStatus { initial, loading, loaded, error }

class CompetitionListState {
  final CompetitionListStatus status;
  final List<CompetitionModel> competitions;
  final String? error;
  final bool hasMore;
  final int currentPage;

  CompetitionListState({
    this.status = CompetitionListStatus.initial,
    this.competitions = const [],
    this.error,
    this.hasMore = true,
    this.currentPage = 1,
  });

  CompetitionListState copyWith({
    CompetitionListStatus? status,
    List<CompetitionModel>? competitions,
    String? error,
    bool? hasMore,
    int? currentPage,
  }) {
    return CompetitionListState(
      status: status ?? this.status,
      competitions: competitions ?? this.competitions,
      error: error,
      hasMore: hasMore ?? this.hasMore,
      currentPage: currentPage ?? this.currentPage,
    );
  }
}

class CompetitionListNotifier extends StateNotifier<CompetitionListState> {
  final Dio _dio;

  CompetitionListNotifier(this._dio) : super(CompetitionListState());

  Future<void> load({String? search, String? statusFilter, bool reset = false}) async {
    if (state.status == CompetitionListStatus.loading) return;

    final page = reset ? 1 : state.currentPage;
    state = state.copyWith(
      status: CompetitionListStatus.loading,
      competitions: reset ? [] : state.competitions,
      error: null,
    );

    try {
      final params = <String, dynamic>{'page': page};
      if (search != null && search.isNotEmpty) params['search'] = search;

      final response = await _dio.get('/competitions', queryParameters: params);
      final dataList = response.data['data'] as List<dynamic>;
      final competitions = dataList
          .map((e) => CompetitionModel.fromJson(e as Map<String, dynamic>))
          .toList();

      final filtered = statusFilter != null && statusFilter != 'all'
          ? competitions.where((c) => c.status == statusFilter).toList()
          : competitions;

      final meta = response.data['meta'] as Map<String, dynamic>?;
      final lastPage = meta?['last_page'] as int? ?? 1;

      state = CompetitionListState(
        status: CompetitionListStatus.loaded,
        competitions: [...state.competitions, ...filtered],
        hasMore: page < lastPage,
        currentPage: page + 1,
      );
    } on DioException catch (e) {
      state = state.copyWith(
        status: CompetitionListStatus.error,
        error: e.message ?? 'حدث خطأ أثناء تحميل المسابقات',
      );
    }
  }

  void reset() {
    state = CompetitionListState();
  }
}

final competitionListProvider =
    StateNotifierProvider<CompetitionListNotifier, CompetitionListState>((ref) {
  return CompetitionListNotifier(ref.watch(dioProvider));
});

final competitionDetailProvider =
    FutureProvider.family<CompetitionModel, int>((ref, id) async {
  final dio = ref.watch(dioProvider);
  final response = await dio.get('/competitions/$id');
  return CompetitionModel.fromJson(response.data['data'] as Map<String, dynamic>);
});

final competitionResultsProvider =
    FutureProvider.family<List<WinnerModel>, int>((ref, id) async {
  final dio = ref.watch(dioProvider);
  final response = await dio.get('/competitions/$id/results');
  final data = response.data['data'] as List<dynamic>;
  return data.map((e) => WinnerModel.fromJson(e as Map<String, dynamic>)).toList();
});
