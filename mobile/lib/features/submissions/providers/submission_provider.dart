import 'dart:typed_data';

import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/dio_provider.dart';
import '../models/submission_model.dart';

enum SubmissionListStatus { initial, loading, loaded, error }

class SubmissionListState {
  final SubmissionListStatus status;
  final List<SubmissionModel> submissions;
  final String? error;

  SubmissionListState({
    this.status = SubmissionListStatus.initial,
    this.submissions = const [],
    this.error,
  });

  SubmissionListState copyWith({
    SubmissionListStatus? status,
    List<SubmissionModel>? submissions,
    String? error,
  }) {
    return SubmissionListState(
      status: status ?? this.status,
      submissions: submissions ?? this.submissions,
      error: error,
    );
  }
}

class SubmissionListNotifier extends StateNotifier<SubmissionListState> {
  final Dio _dio;

  SubmissionListNotifier(this._dio) : super(SubmissionListState());

  Future<void> load() async {
    state = state.copyWith(status: SubmissionListStatus.loading, error: null);

    try {
      final response = await _dio.get('/my/submissions');
      final data = response.data['data'] as List<dynamic>;
      final submissions = data
          .map((e) => SubmissionModel.fromJson(e as Map<String, dynamic>))
          .toList();

      state = SubmissionListState(
        status: SubmissionListStatus.loaded,
        submissions: submissions,
      );
    } on DioException catch (e) {
      state = state.copyWith(
        status: SubmissionListStatus.error,
        error: e.message ?? 'حدث خطأ أثناء تحميل المشاركات',
      );
    }
  }
}

final submissionListProvider =
    StateNotifierProvider<SubmissionListNotifier, SubmissionListState>((ref) {
  return SubmissionListNotifier(ref.watch(dioProvider));
});

final submissionDetailProvider =
    FutureProvider.family<SubmissionModel, int>((ref, id) async {
  final dio = ref.watch(dioProvider);
  final response = await dio.get('/submissions/$id');
  return SubmissionModel.fromJson(response.data['data'] as Map<String, dynamic>);
});

// Submission creation state
enum SubmissionCreateStatus { idle, uploading, success, error }

class SubmissionCreateState {
  final SubmissionCreateStatus status;
  final String? error;
  final double progress;

  SubmissionCreateState({
    this.status = SubmissionCreateStatus.idle,
    this.error,
    this.progress = 0,
  });

  SubmissionCreateState copyWith({
    SubmissionCreateStatus? status,
    String? error,
    double? progress,
  }) {
    return SubmissionCreateState(
      status: status ?? this.status,
      error: error,
      progress: progress ?? this.progress,
    );
  }
}

class SubmissionCreateNotifier extends StateNotifier<SubmissionCreateState> {
  final Dio _dio;

  SubmissionCreateNotifier(this._dio) : super(SubmissionCreateState());

  Future<bool> submit({
    required int competitionId,
    String? textContent,
    String? linkUrl,
    Uint8List? fileBytes,
    String? fileName,
  }) async {
    state = SubmissionCreateState(status: SubmissionCreateStatus.uploading, progress: 0);

    try {
      final formData = FormData();

      if (textContent != null) {
        formData.fields.add(MapEntry('text_content', textContent));
      }
      if (linkUrl != null) {
        formData.fields.add(MapEntry('link_url', linkUrl));
      }
      if (fileBytes != null) {
        formData.files.add(
          MapEntry('file', MultipartFile.fromBytes(fileBytes, filename: fileName)),
        );
      }

      final response = await _dio.post(
        '/competitions/$competitionId/submissions',
        data: formData,
        onSendProgress: (sent, total) {
          if (total > 0) {
            state = state.copyWith(progress: sent / total);
          }
        },
      );

      state = SubmissionCreateState(status: SubmissionCreateStatus.success, progress: 1);
      return true;
    } on DioException catch (e) {
      final errors = e.response?.data?['errors'] as Map<String, dynamic>?;
      final message = errors?.values.first?.first as String? ?? 'حدث خطأ أثناء الإرسال';
      state = SubmissionCreateState(status: SubmissionCreateStatus.error, error: message);
      return false;
    }
  }

  void reset() {
    state = SubmissionCreateState();
  }
}

final submissionCreateProvider =
    StateNotifierProvider<SubmissionCreateNotifier, SubmissionCreateState>((ref) {
  return SubmissionCreateNotifier(ref.watch(dioProvider));
});
