import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/api/dio_provider.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/services/token_service.dart';
import '../models/auth_response.dart';
import '../models/user_model.dart';

enum AuthStatus { initial, authenticated, unauthenticated }

class AuthState {
  final AuthStatus status;
  final UserModel? user;
  final String? error;
  final bool loading;

  const AuthState({
    this.status = AuthStatus.initial,
    this.user,
    this.error,
    this.loading = false,
  });

  AuthState copyWith({
    AuthStatus? status,
    UserModel? user,
    String? error,
    bool? loading,
  }) {
    return AuthState(
      status: status ?? this.status,
      user: user ?? this.user,
      error: error,
      loading: loading ?? this.loading,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final Dio _dio;

  AuthNotifier(this._dio) : super(const AuthState());

  Future<void> checkAuth() async {
    final hasToken = await TokenService.hasToken();
    if (!hasToken) {
      state = const AuthState(status: AuthStatus.unauthenticated);
      return;
    }

    try {
      final response = await _dio.get('/user');
      final user = UserModel.fromJson(response.data['data'] as Map<String, dynamic>);
      state = AuthState(status: AuthStatus.authenticated, user: user);
    } catch (_) {
      await TokenService.clearToken();
      state = const AuthState(status: AuthStatus.unauthenticated);
    }
  }

  Future<bool> login({required String email, required String password}) async {
    state = const AuthState(loading: true);
    try {
      final response = await _dio.post('/login', data: {
        'email': email,
        'password': password,
      });
      final auth = AuthResponse.fromJson(response.data);
      await TokenService.saveToken(auth.token);
      state = AuthState(status: AuthStatus.authenticated, user: auth.user);
      return true;
    } on DioException catch (e) {
      final message = e.response?.data?['message'] as String? ?? 'حدث خطأ أثناء تسجيل الدخول';
      state = AuthState(status: AuthStatus.unauthenticated, error: message);
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String phoneNumber,
    required String password,
    required String passwordConfirmation,
  }) async {
    state = const AuthState(loading: true);
    try {
      final response = await _dio.post('/register', data: {
        'name': name,
        'email': email,
        'phone_number': phoneNumber,
        'password': password,
        'password_confirmation': passwordConfirmation,
      });
      final auth = AuthResponse.fromJson(response.data);
      await TokenService.saveToken(auth.token);
      state = AuthState(status: AuthStatus.authenticated, user: auth.user);
      return true;
    } on DioException catch (e) {
      final errors = e.response?.data?['errors'] as Map<String, dynamic>?;
      final message = errors?.values.first?.first as String? ?? 'حدث خطأ أثناء التسجيل';
      state = AuthState(status: AuthStatus.unauthenticated, error: message);
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _dio.post('/logout');
    } catch (_) {}
    await TokenService.clearToken();
    state = const AuthState(status: AuthStatus.unauthenticated);
  }

  Future<bool> forgotPassword(String email) async {
    state = const AuthState(loading: true);
    try {
      await _dio.post('/forgot-password', data: {'email': email});
      state = const AuthState(status: AuthStatus.unauthenticated);
      return true;
    } on DioException catch (e) {
      final message = e.response?.data?['message'] as String? ?? 'حدث خطأ';
      state = AuthState(status: AuthStatus.unauthenticated, error: message);
      return false;
    }
  }

  void updateUser(UserModel user) {
    state = state.copyWith(user: user);
  }

  void clearError() {
    state = state.copyWith(error: null);
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier(ref.watch(dioProvider));
});

final onboardingCompleteProvider = FutureProvider<bool>((ref) async {
  final prefs = await SharedPreferences.getInstance();
  return prefs.getBool(AppConstants.onboardingKey) ?? false;
});
