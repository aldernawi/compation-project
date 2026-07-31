import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/dio_provider.dart';
import '../models/notification_model.dart';

enum NotificationStatus { initial, loading, loaded, error }

class NotificationState {
  final NotificationStatus status;
  final List<NotificationModel> notifications;
  final String? error;

  NotificationState({
    this.status = NotificationStatus.initial,
    this.notifications = const [],
    this.error,
  });

  NotificationState copyWith({
    NotificationStatus? status,
    List<NotificationModel>? notifications,
    String? error,
  }) {
    return NotificationState(
      status: status ?? this.status,
      notifications: notifications ?? this.notifications,
      error: error,
    );
  }

  int get unreadCount => notifications.where((n) => !n.isRead).length;
}

class NotificationNotifier extends StateNotifier<NotificationState> {
  final Dio _dio;

  NotificationNotifier(this._dio) : super(NotificationState());

  Future<void> load() async {
    state = state.copyWith(status: NotificationStatus.loading, error: null);

    try {
      final response = await _dio.get('/notifications');
      final data = response.data['data'] as List<dynamic>;
      final notifications = data
          .map((e) => NotificationModel.fromJson(e as Map<String, dynamic>))
          .toList();

      state = NotificationState(
        status: NotificationStatus.loaded,
        notifications: notifications,
      );
    } on DioException catch (e) {
      state = state.copyWith(
        status: NotificationStatus.error,
        error: e.message ?? 'حدث خطأ أثناء تحميل الإشعارات',
      );
    }
  }

  Future<void> markRead(String id) async {
    try {
      await _dio.patch('/notifications/$id/read');
      final notifications = state.notifications.map((n) {
        if (n.id == id) {
          return NotificationModel(
            id: n.id,
            type: n.type,
            data: n.data,
            createdAt: n.createdAt,
            readAt: DateTime.now(),
          );
        }
        return n;
      }).toList();
      state = state.copyWith(notifications: notifications);
    } catch (_) {}
  }
}

final notificationProvider =
    StateNotifierProvider<NotificationNotifier, NotificationState>((ref) {
  return NotificationNotifier(ref.watch(dioProvider));
});
