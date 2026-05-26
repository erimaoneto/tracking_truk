import 'dart:convert';
import 'dart:io';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:http/http.dart' as http;
import '../../../core/network/api_client.dart';

// ================= EVENTS (EVENT BBM) =================
abstract class FuelEvent {}

class SubmitFuelReportEvent extends FuelEvent {
  final int vehicleId;
  final double volumeLiters;
  final double cost;
  final int odometer;
  final String location;
  final File? imageFile;

  SubmitFuelReportEvent({
    required this.vehicleId,
    required this.volumeLiters,
    required this.cost,
    required this.odometer,
    required this.location,
    this.imageFile,
  });
}

// ================= STATES (STATE BBM) =================
abstract class FuelState {}

class FuelInitial extends FuelState {}

class FuelSubmitting extends FuelState {}

class FuelSuccess extends FuelState {}

class FuelError extends FuelState {
  final String message;
  FuelError({required this.message});
}

// ================= BLOC (LOGIKA LAPOR BBM) =================
class FuelBloc extends Bloc<FuelEvent, FuelState> {
  final ApiClient apiClient;

  FuelBloc({required this.apiClient}) : super(FuelInitial()) {
    on<SubmitFuelReportEvent>(_onSubmitFuelReport);
  }

  // Mengolah pengiriman log laporan BBM
  Future<void> _onSubmitFuelReport(SubmitFuelReportEvent event, Emitter<FuelState> emit) async {
    emit(FuelSubmitting());
    try {
      final Map<String, String> fields = {
        'vehicle_id': event.vehicleId.toString(),
        'volume_liters': event.volumeLiters.toString(),
        'cost': event.cost.toString(),
        'odometer': event.odometer.toString(),
        'location': event.location,
      };

      if (event.imageFile != null) {
        // Mengirim multipart form data jika supir mengunggah foto struk
        final streamedResponse = await apiClient.multipartPost(
          '/fuel/report',
          fields,
          event.imageFile!,
          'receipt_photo', // Mencocokkan field request di Laravel
        );

        final response = await http.Response.fromStream(streamedResponse);
        final Map<String, dynamic> data = jsonDecode(response.body);

        if (response.statusCode == 200 && data['status'] == 'success') {
          emit(FuelSuccess());
        } else {
          final errorMsg = data['message'] ?? 'Gagal mengirim laporan pengisian BBM.';
          emit(FuelError(message: errorMsg));
        }
      } else {
        // Mengirim POST biasa jika supir tidak menyertakan foto struk
        final response = await apiClient.post('/fuel/report', fields);
        final Map<String, dynamic> data = jsonDecode(response.body);

        if (response.statusCode == 200 && data['status'] == 'success') {
          emit(FuelSuccess());
        } else {
          final errorMsg = data['message'] ?? 'Gagal mengirim laporan pengisian BBM.';
          emit(FuelError(message: errorMsg));
        }
      }
    } catch (e) {
      emit(FuelError(message: 'Terjadi kesalahan koneksi saat mengirim laporan BBM.'));
    }
  }
}
