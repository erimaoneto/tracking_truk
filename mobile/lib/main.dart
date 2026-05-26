import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'core/network/api_client.dart';
import 'features/auth/bloc/auth_bloc.dart';
import 'features/auth/views/login_view.dart';
import 'features/trip/bloc/trip_bloc.dart';
import 'features/trip/views/trip_dashboard_view.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    // 1. Menyediakan ApiClient secara global menggunakan RepositoryProvider
    return RepositoryProvider(
      create: (context) => ApiClient(),
      child: MultiBlocProvider(
        // 2. Menyediakan AuthBloc & TripBloc secara global
        providers: [
          BlocProvider<AuthBloc>(
            create: (context) => AuthBloc(
              apiClient: RepositoryProvider.of<ApiClient>(context),
            )..add(CheckAuthEvent()), // Trigger pengecekan session/token saat start
          ),
          BlocProvider<TripBloc>(
            create: (context) => TripBloc(
              apiClient: RepositoryProvider.of<ApiClient>(context),
            ),
          ),
        ],
        child: MaterialApp(
          title: 'Erickman Driver App',
          debugShowCheckedModeBanner: false,
          theme: ThemeData(
            colorScheme: ColorScheme.fromSeed(
              seedColor: const Color(0xFF1E3A8A), // Biru Royal
              primary: const Color(0xFF1E3A8A),
              secondary: const Color(0xFF2563EB),
            ),
            useMaterial3: true,
            fontFamily: 'sans-serif', // Menggunakan font bawaan ponsel yang andal
          ),
          home: const AppRoutingHandler(),
        ),
      ),
    );
  }
}

// 3. Routing Handler Dinamis Berdasarkan State Autentikasi Supir
class AppRoutingHandler extends StatelessWidget {
  const AppRoutingHandler({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AuthBloc, AuthState>(
      builder: (context, state) {
        // A. Jika Supir SUDAH Login / Session Aktif
        if (state is Authenticated) {
          // Pemicu pengecekan trip aktif milik supir secara asinkron
          context.read<TripBloc>().add(CheckActiveTripEvent());
          return const TripDashboardView();
        }

        // B. Jika Supir BELUM Login
        if (state is Unauthenticated || state is AuthInitial || state is AuthError) {
          return const LoginView();
        }

        // C. Tampilan Splash / Loading Screen saat Memuat Session
        if (state is AuthLoading) {
          return const Scaffold(
            backgroundColor: Color(0xFF1E3A8A), // Splash Biru Royal
            body: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Logo Erickman
                  Text(
                    'E',
                    style: TextStyle(
                      fontSize: 80,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                      letterSpacing: -2,
                    ),
                  ),
                  SizedBox(height: 16),
                  Text(
                    'PT. ERICKMAN',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                      letterSpacing: 1.5,
                    ),
                  ),
                  Text(
                    'Sarana Abadi',
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.white70,
                    ),
                  ),
                  SizedBox(height: 40),
                  CircularProgressIndicator(
                    valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                  ),
                ],
              ),
            ),
          );
        }

        return const LoginView();
      },
    );
  }
}
