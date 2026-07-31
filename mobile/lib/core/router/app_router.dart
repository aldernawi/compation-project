import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../features/auth/providers/auth_provider.dart';
import '../../features/auth/screens/splash_screen.dart';
import '../../features/auth/screens/onboarding_screen.dart';
import '../../features/auth/screens/login_screen.dart';
import '../../features/auth/screens/register_screen.dart';
import '../../features/auth/screens/forgot_password_screen.dart';
import '../../features/competitions/screens/home_screen.dart';
import '../../features/competitions/screens/competition_list_screen.dart';
import '../../features/competitions/screens/competition_detail_screen.dart';
import '../../features/submissions/screens/submit_screen.dart';
import '../../features/submissions/screens/my_submissions_screen.dart';
import '../../features/submissions/screens/submission_detail_screen.dart';
import '../../features/notifications/screens/notifications_screen.dart';
import '../../features/profile/screens/profile_screen.dart';
import '../../features/notifications/providers/notification_provider.dart';

final appRouterProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    initialLocation: '/splash',
    routes: [
      GoRoute(path: '/splash', builder: (_, __) => const SplashScreen()),
      GoRoute(path: '/onboarding', builder: (_, __) => const OnboardingScreen()),
      GoRoute(path: '/login', builder: (_, __) => const LoginScreen()),
      GoRoute(path: '/register', builder: (_, __) => const RegisterScreen()),
      GoRoute(path: '/forgot-password', builder: (_, __) => const ForgotPasswordScreen()),
      ShellRoute(
        builder: (context, state, child) {
          final location = state.matchedLocation;
          final index = _selectedIndex(location);
          return MainShell(currentIndex: index, child: child);
        },
        routes: [
          GoRoute(path: '/home', builder: (_, __) => const HomeScreen()),
          GoRoute(path: '/competitions', builder: (_, __) => const CompetitionListScreen()),
          GoRoute(
            path: '/competitions/:id',
            builder: (_, state) => CompetitionDetailScreen(
              id: int.parse(state.pathParameters['id']!),
            ),
          ),
          GoRoute(
            path: '/competitions/:id/submit',
            builder: (_, state) => SubmitScreen(
              competitionId: int.parse(state.pathParameters['id']!),
            ),
          ),
          GoRoute(path: '/my-submissions', builder: (_, __) => const MySubmissionsScreen()),
          GoRoute(
            path: '/submissions/:id',
            builder: (_, state) => SubmissionDetailScreen(
              id: int.parse(state.pathParameters['id']!),
            ),
          ),
          GoRoute(path: '/notifications', builder: (_, __) => const NotificationsScreen()),
          GoRoute(path: '/profile', builder: (_, __) => const ProfileScreen()),
        ],
      ),
    ],
    redirect: (context, state) {
      final authState = ref.read(authProvider);
      final status = authState.status;
      final location = state.matchedLocation;
      final publicRoutes = ['/splash', '/onboarding', '/login', '/register', '/forgot-password'];
      final isPublic = publicRoutes.contains(location);

      if (status == AuthStatus.initial && !isPublic) {
        return '/splash';
      }
      if (status == AuthStatus.unauthenticated && !isPublic) {
        return '/login';
      }
      if (status == AuthStatus.authenticated && isPublic && location != '/splash') {
        return '/home';
      }
      return null;
    },
    refreshListenable: _AuthListenable(ref),
  );
});

int _selectedIndex(String location) {
  if (location.startsWith('/home')) return 0;
  if (location.startsWith('/competitions')) return 1;
  if (location.startsWith('/my-submissions') || location.startsWith('/submissions')) return 2;
  if (location.startsWith('/notifications')) return 3;
  if (location.startsWith('/profile')) return 4;
  return 0;
}

class _AuthListenable extends ChangeNotifier {
  final Ref _ref;
  AuthStatus _lastStatus = AuthStatus.initial;

  _AuthListenable(this._ref) {
    _ref.listen<AuthState>(authProvider, (_, next) {
      if (next.status != _lastStatus) {
        _lastStatus = next.status;
        notifyListeners();
      }
    });
  }
}

class MainShell extends ConsumerStatefulWidget {
  final int currentIndex;
  final Widget child;

  const MainShell({super.key, required this.currentIndex, required this.child});

  @override
  ConsumerState<MainShell> createState() => _MainShellState();
}

class _MainShellState extends ConsumerState<MainShell> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(notificationProvider.notifier).load();
    });
  }

  @override
  Widget build(BuildContext context) {
    final notifState = ref.watch(notificationProvider);

    return Scaffold(
      body: widget.child,
      bottomNavigationBar: NavigationBar(
        selectedIndex: widget.currentIndex,
        onDestinationSelected: (index) {
          switch (index) {
            case 0:
              context.go('/home');
            case 1:
              context.go('/competitions');
            case 2:
              context.go('/my-submissions');
            case 3:
              context.go('/notifications');
            case 4:
              context.go('/profile');
          }
        },
        destinations: [
          const NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home),
            label: 'الرئيسية',
          ),
          const NavigationDestination(
            icon: Icon(Icons.emoji_events_outlined),
            selectedIcon: Icon(Icons.emoji_events),
            label: 'المسابقات',
          ),
          const NavigationDestination(
            icon: Icon(Icons.send_outlined),
            selectedIcon: Icon(Icons.send),
            label: 'مشاركاتي',
          ),
          NavigationDestination(
            icon: Badge(
              isLabelVisible: notifState.unreadCount > 0,
              label: Text('${notifState.unreadCount}'),
              child: const Icon(Icons.notifications_outlined),
            ),
            selectedIcon: Badge(
              isLabelVisible: notifState.unreadCount > 0,
              label: Text('${notifState.unreadCount}'),
              child: const Icon(Icons.notifications),
            ),
            label: 'الإشعارات',
          ),
          const NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person),
            label: 'الملف',
          ),
        ],
      ),
    );
  }
}
