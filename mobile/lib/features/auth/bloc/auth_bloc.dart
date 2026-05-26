import 'dart:convert';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/user_model.dart';

// ================= EVENTS (EVENT AUTENTIKASI) =================
abstract class AuthEvent {}

class CheckAuthEvent extends AuthEvent {}

class LoginEvent extends AuthEvent {
  final String email;
  final String password;
  LoginEvent({required this.email, required this.password});
}

class LogoutEvent extends AuthEvent {}

// ================= STATES (STATE AUTENTIKASI) =================
abstract class AuthState {}

class AuthInitial extends AuthState {}

class AuthLoading extends AuthState {}

class Authenticated extends AuthState {
  final UserModel user;
  final String token;
  Authenticated({required this.user, required this.token});
}

class Unauthenticated extends AuthState {}

class AuthError extends AuthState {
  final String message;
  AuthError({required this.message});
}

// ================= BLOC (LOGIKA AUTENTIKASI) =================
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final ApiClient apiClient;

  AuthBloc({required this.apiClient}) : super(AuthInitial()) {
    on<CheckAuthEvent>(_onCheckAuth);
    on<LoginEvent>(_onLogin);
    on<LogoutEvent>(_onLogout);
  }

  // 1. Memeriksa keberadaan token di local storage saat aplikasi pertama dibuka
  Future<void> _onCheckAuth(CheckAuthEvent event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString(ApiConstants.keyToken);
      final userId = prefs.getInt(ApiConstants.keyDriverId);
      final userName = prefs.getString(ApiConstants.keyUserName);
      final userEmail = prefs.getString(ApiConstants.keyUserEmail);
      
      if (token != null && token.isNotEmpty && userId != null) {
        final user = UserModel(
          id: userId,
          name: userName ?? '',
          email: userEmail ?? '',
          role: 'Supir',
        );
        emit(Authenticated(user: user, token: token));
      } else {
        emit(Unauthenticated());
      }
    } catch (e) {
      emit(Unauthenticated());
    }
  }

  // 2. Logika login supir ke API Laravel
  Future<void> _onLogin(LoginEvent event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      final response = await apiClient.post('/auth/login', {
        'email': event.email,
        'password': event.password,
      });

      final Map<String, dynamic> data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['status'] == 'success') {
        final token = data['token'] as String;
        final userJson = data['user'] as Map<String, dynamic>;
        
        final user = UserModel.fromJson(userJson);

        // Validasi Role Keamanan: Hanya role Supir yang diijinkan masuk lewat aplikasi mobile
        if (user.role.toLowerCase() != 'supir') {
          emit(AuthError(message: 'Hanya akun supir yang diperbolehkan login di aplikasi ini!'));
          return;
        }

        // Simpan token & profil supir di SharedPreferences secara persisten
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(ApiConstants.keyToken, token);
        await prefs.setInt(ApiConstants.keyDriverId, user.id);
        await prefs.setString(ApiConstants.keyUserName, user.name);
        await prefs.setString(ApiConstants.keyUserEmail, user.email);

        emit(Authenticated(user: user, token: token));
      } else {
        final errorMsg = data['message'] ?? 'Email atau password salah';
        emit(AuthError(message: errorMsg));
      }
    } catch (e) {
      emit(AuthError(message: 'Gagal terhubung ke server. Silakan periksa koneksi Anda.'));
    }
  }

  // 3. Logika logout supir
  Future<void> _onLogout(LogoutEvent event, Emitter<AuthState> emit) async {
    emit(AuthLoading());
    try {
      await apiClient.post('/auth/logout', {});
    } catch (_) {
      // Hiraukan kesalahan jaringan saat logout, bersihkan cache lokal saja
    } finally {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(ApiConstants.keyToken);
      await prefs.remove(ApiConstants.keyDriverId);
      await prefs.remove(ApiConstants.keyUserName);
      await prefs.remove(ApiConstants.keyUserEmail);
      await prefs.remove(ApiConstants.keyActiveTripId);
      await prefs.remove(ApiConstants.keyActiveVehicleId);
      await prefs.remove(ApiConstants.keyActiveVehiclePlate);
      
      emit(Unauthenticated());
    }
  }
}
