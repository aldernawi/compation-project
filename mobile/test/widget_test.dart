import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'package:competition_app/main.dart';

void main() {
  testWidgets('renders the competition platform splash screen',
      (WidgetTester tester) async {
    SharedPreferences.setMockInitialValues({});

    await tester.pumpWidget(const ProviderScope(child: CompetitionApp()));

    expect(find.text('منصة المسابقات'), findsOneWidget);

    await tester.pump(const Duration(milliseconds: 1500));
    await tester.pump();
  });
}
