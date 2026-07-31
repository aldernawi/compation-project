class AppConstants {
  AppConstants._();

  static const String baseUrl = 'http://localhost:8000/api';
  static const String baseMediaUrl = 'http://localhost:8000';
  static const String tokenKey = 'auth_token';
  static const String onboardingKey = 'onboarding_complete';
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
}
