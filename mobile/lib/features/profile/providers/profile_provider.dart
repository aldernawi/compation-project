import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/dio_provider.dart';
import '../../auth/models/user_model.dart';
import '../../auth/providers/auth_provider.dart';

enum ProfileStatus { idle, loading, success, error }

class ProfileState {
  final ProfileStatus status;
  final String? error;

  ProfileState({this.status = ProfileStatus.idle, this.error});

  ProfileState copyWith({ProfileStatus? status, String? error}) {
    return ProfileState(
      status: status ?? this.status,
      error: error,
    );
  }
}

class ProfileNotifier extends StateNotifier<ProfileState> {
  final Dio _dio;
  final AuthNotifier _authNotifier;

  ProfileNotifier(this._dio, this._authNotifier) : super(ProfileState());

  Future<bool> updateProfile({
    required String name,
    required String email,
    required String phoneNumber,
  }) async {
    state = ProfileState(status: ProfileStatus.loading);

    try {
      final response = await _dio.put('/profile', data: {
        'name': name,
        'email': email,
        'phone_number': phoneNumber,
      });
      final user = UserModel.fromJson(response.data['data'] as Map<String, dynamic>);
      _authNotifier.updateUser(user);
      state = ProfileState(status: ProfileStatus.success);
      return true;
    } on DioException catch (e) {
      final errors = e.response?.data?['errors'] as Map<String, dynamic>?;
      final message = errors?.values.first?.first as String? ?? 'حدث خطأ أثناء التحديث';
      state = ProfileState(status: ProfileStatus.error, error: message);
      return false;
    }
  }

  Future<bool> changePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  }) async {
    state = ProfileState(status: ProfileStatus.loading);

    try {
      await _dio.put('/profile/password', data: {
        'current_password': currentPassword,
        'password': newPassword,
        'password_confirmation': newPasswordConfirmation,
      });
      state = ProfileState(status: ProfileStatus.success);
      return true;
    } on DioException catch (e) {
      final errors = e.response?.data?['errors'] as Map<String, dynamic>?;
      final message = errors?.values.first?.first as String? ?? 'حدث خطأ أثناء تغيير كلمة المرور';
      state = ProfileState(status: ProfileStatus.error, error: message);
      return false;
    }
  }

  void reset() {
    state = ProfileState();
  }
}

final profileProvider = StateNotifierProvider<ProfileNotifier, ProfileState>((ref) {
  return ProfileNotifier(ref.watch(dioProvider), ref.watch(authProvider.notifier));
});
