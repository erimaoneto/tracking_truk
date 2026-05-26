import 'dart:async';
import 'dart:convert';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:geolocator/geolocator.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../models/trip_model.dart';

// ================= EVENTS (EVENT PERJALANAN) =================
abstract class TripEvent {}

class CheckActiveTripEvent extends TripEvent {}

class FetchAvailableVehiclesEvent extends TripEvent {}

class StartTripEvent extends TripEvent {
  final int vehicleId;
  final String vehiclePlate;
  StartTripEvent({required this.vehicleId, required this.vehiclePlate});
}

class EndTripEvent extends TripEvent {}

class GetGpsLocationEvent extends TripEvent {}

class SyncGpsEvent extends TripEvent {
  final double latitude;
  final double longitude;
  final double speed;
  SyncGpsEvent({required this.latitude, required this.longitude, required this.speed});
}

// ================= STATES (STATE PERJALANAN) =================
abstract class TripState {}

class TripInitial extends TripState {}

class TripLoading extends TripState {}

class TripIdle extends TripState {
  final List<dynamic> availableVehicles;
  TripIdle({required this.availableVehicles});
}

class TripActive extends TripState {
  final TripModel trip;
  final String vehiclePlate;
  final List<dynamic> availableVehicles;
  TripActive({required this.trip, required this.vehiclePlate, required this.availableVehicles});
}

class TripError extends TripState {
  final String message;
  final TripState previousState;
  TripError({required this.message, required this.previousState});
}

// ================= BLOC (LOGIKA BISNIS TRIP & GPS) =================
class TripBloc extends Bloc<TripEvent, TripState> {
  final ApiClient apiClient;
  Timer? _gpsTimer;
  List<dynamic> _cachedVehicles = [];

  TripBloc({required this.apiClient}) : super(TripInitial()) {
    on<CheckActiveTripEvent>(_onCheckActiveTrip);
    on<FetchAvailableVehiclesEvent>(_onFetchAvailableVehicles);
    on<StartTripEvent>(_onStartTrip);
    on<EndTripEvent>(_onEndTrip);
    on<GetGpsLocationEvent>(_onGetGpsLocation);
    on<SyncGpsEvent>(_onSyncGps);
  }

  @override
  Future<void> close() {
    _gpsTimer?.cancel();
    return super.close();
  }

  // 1. Memeriksa apakah ada perjalanan aktif yang sedang menggantung di memori lokal ponsel
  Future<void> _onCheckActiveTrip(CheckActiveTripEvent event, Emitter<TripState> emit) async {
    emit(TripLoading());
    try {
      final prefs = await SharedPreferences.getInstance();
      final activeTripId = prefs.getInt(ApiConstants.keyActiveTripId);
      final activeVehicleId = prefs.getInt(ApiConstants.keyActiveVehicleId);
      final activeVehiclePlate = prefs.getString(ApiConstants.keyActiveVehiclePlate);
      final driverId = prefs.getInt(ApiConstants.keyDriverId);

      // Selalu muat daftar kendaraan terbaru dari backend Laravel
      await _fetchVehicles();

      if (activeTripId != null && activeVehicleId != null && driverId != null) {
        final trip = TripModel(
          id: activeTripId,
          vehicleId: activeVehicleId,
          driverId: driverId,
          startTime: DateTime.now().toIso8601String(), // Waktu penanda lokal
          status: 'ongoing',
        );
        
        emit(TripActive(
          trip: trip,
          vehiclePlate: activeVehiclePlate ?? '',
          availableVehicles: _cachedVehicles,
        ));

        // Nyalakan sinkronisasi berkala lokasi GPS ke server
        _startGpsTimer();
      } else {
        emit(TripIdle(availableVehicles: _cachedVehicles));
      }
    } catch (e) {
      emit(TripIdle(availableVehicles: const []));
    }
  }

  // 2. Mengambil data armada truk yang berstatus Tersedia (Available)
  Future<void> _onFetchAvailableVehicles(FetchAvailableVehiclesEvent event, Emitter<TripState> emit) async {
    final currentState = state;
    emit(TripLoading());
    try {
      await _fetchVehicles();
      if (currentState is TripActive) {
        emit(TripActive(
          trip: currentState.trip,
          vehiclePlate: currentState.vehiclePlate,
          availableVehicles: _cachedVehicles,
        ));
      } else {
        emit(TripIdle(availableVehicles: _cachedVehicles));
      }
    } catch (e) {
      emit(TripError(
        message: 'Gagal mengambil data kendaraan.',
        previousState: currentState,
      ));
    }
  }

  // Helper pemanggil API kendaraan
  Future<void> _fetchVehicles() async {
    final response = await apiClient.get('/vehicles/available');
    final Map<String, dynamic> data = jsonDecode(response.body);
    if (response.statusCode == 200 && data['status'] == 'success') {
      _cachedVehicles = data['vehicles'] as List<dynamic>;
    }
  }

  // 3. Memulai perjalanan (Start Trip)
  Future<void> _onStartTrip(StartTripEvent event, Emitter<TripState> emit) async {
    final previousState = state;
    emit(TripLoading());
    try {
      // A. Minta Izin Sensor GPS HP Supir
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        emit(TripError(
          message: 'Layanan lokasi/GPS pada ponsel Anda belum aktif.',
          previousState: previousState,
        ));
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          emit(TripError(
            message: 'Izin akses lokasi ditolak. Aplikasi membutuhkan GPS untuk melacak perjalanan.',
            previousState: previousState,
          ));
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        emit(TripError(
          message: 'Izin lokasi ditolak permanen. Silakan aktifkan izin lokasi di Pengaturan Ponsel.',
          previousState: previousState,
        ));
        return;
      }

      // B. Mengirim data request ke API backend Laravel
      final response = await apiClient.post('/trips/start', {
        'vehicle_id': event.vehicleId,
      });

      final Map<String, dynamic> data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['status'] == 'success') {
        final trip = TripModel.fromJson(data['trip'] as Map<String, dynamic>);

        // Simpan cache trip aktif di Shared Preferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setInt(ApiConstants.keyActiveTripId, trip.id);
        await prefs.setInt(ApiConstants.keyActiveVehicleId, trip.vehicleId);
        await prefs.setString(ApiConstants.keyActiveVehiclePlate, event.vehiclePlate);

        emit(TripActive(
          trip: trip,
          vehiclePlate: event.vehiclePlate,
          availableVehicles: _cachedVehicles,
        ));

        // Nyalakan sinkronisasi berkala lokasi GPS
        _startGpsTimer();
      } else {
        final msg = data['error'] ?? 'Gagal memulai perjalanan';
        emit(TripError(message: msg, previousState: previousState));
      }
    } catch (e) {
      emit(TripError(
        message: 'Terjadi kegagalan koneksi saat memulai perjalanan.',
        previousState: previousState,
      ));
    }
  }

  // 4. Mengakhiri perjalanan (End Trip)
  Future<void> _onEndTrip(EndTripEvent event, Emitter<TripState> emit) async {
    final previousState = state;
    if (previousState is! TripActive) return;
    
    emit(TripLoading());
    try {
      final tripId = previousState.trip.id;
      final response = await apiClient.post('/trips/end/$tripId', {});
      final Map<String, dynamic> data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['status'] == 'success') {
        // Matikan timer GPS
        _gpsTimer?.cancel();
        _gpsTimer = null;

        // Bersihkan cache trip lokal
        final prefs = await SharedPreferences.getInstance();
        await prefs.remove(ApiConstants.keyActiveTripId);
        await prefs.remove(ApiConstants.keyActiveVehicleId);
        await prefs.remove(ApiConstants.keyActiveVehiclePlate);

        // Ambil ulang list truk yang statusnya kembali tersedia
        await _fetchVehicles();
        emit(TripIdle(availableVehicles: _cachedVehicles));
      } else {
        emit(TripError(
          message: data['message'] ?? 'Gagal mengakhiri perjalanan',
          previousState: previousState,
        ));
      }
    } catch (e) {
      emit(TripError(
        message: 'Gagal mengakhiri perjalanan di server.',
        previousState: previousState,
      ));
    }
  }

  // 5. Menjalankan detak timer berkala setiap 15 detik
  void _startGpsTimer() {
    _gpsTimer?.cancel();
    _gpsTimer = Timer.periodic(const Duration(seconds: 15), (timer) {
      add(GetGpsLocationEvent());
    });
  }

  // 6. Mengambil koordinat GPS sensor nyata ponsel supir
  Future<void> _onGetGpsLocation(GetGpsLocationEvent event, Emitter<TripState> emit) async {
    if (state is! TripActive) return;
    try {
      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      
      add(SyncGpsEvent(
        latitude: position.latitude,
        longitude: position.longitude,
        speed: position.speed,
      ));
    } catch (_) {
      // Abaikan jika GPS gagal terbaca pada siklus kali ini
    }
  }

  // 7. Mengirim data koordinat batch ke API /gps/sync Laravel
  Future<void> _onSyncGps(SyncGpsEvent event, Emitter<TripState> emit) async {
    final currentState = state;
    if (currentState is! TripActive) return;
    try {
      await apiClient.post('/gps/sync', {
        'trip_id': currentState.trip.id,
        'logs': [
          {
            'latitude': event.latitude,
            'longitude': event.longitude,
            'speed': event.speed,
            'recorded_at': DateTime.now().toIso8601String(),
          }
        ]
      });
    } catch (_) {
      // Abaikan kegagalan upload, koordinat berikutnya akan di-sync pada siklus selanjutnya
    }
  }
}
