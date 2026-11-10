import 'package:flutter/material.dart';

/// [PageTransitionsBuilder] kustom untuk menghilangkan animasi transisi
/// antar halaman di seluruh platform.
class TanpaTransisiBuilder extends PageTransitionsBuilder {
  const TanpaTransisiBuilder();

  @override
  Widget buildTransitions<T>(
    PageRoute<T> route,
    BuildContext context,
    Animation<double> animation,
    Animation<double> secondaryAnimation,
    Widget child,
  ) {
    return child;
  }
}
