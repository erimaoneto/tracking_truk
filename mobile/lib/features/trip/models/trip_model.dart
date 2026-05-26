class TripModel {
  final int id;
  final int vehicleId;
  final int driverId;
  final int? contractId;
  final String startTime;
  final String? endTime;
  final String status;

  TripModel({
    required this.id,
    required this.vehicleId,
    required this.driverId,
    this.contractId,
    required this.startTime,
    this.endTime,
    required this.status,
  });

  // Factory untuk mengonversi JSON response dari backend Laravel
  factory TripModel.fromJson(Map<String, dynamic> json) {
    return TripModel(
      id: json['id'] as int,
      vehicleId: json['vehicle_id'] is String 
          ? int.parse(json['vehicle_id'] as String) 
          : json['vehicle_id'] as int,
      driverId: json['driver_id'] is String 
          ? int.parse(json['driver_id'] as String) 
          : json['driver_id'] as int,
      contractId: json['contract_id'] is String 
          ? int.parse(json['contract_id'] as String) 
          : json['contract_id'] as int?,
      startTime: json['start_time'] as String? ?? '',
      endTime: json['end_time'] as String?,
      status: json['status'] as String? ?? 'ongoing',
    );
  }

  // Mengubah objek TripModel ke dalam format Map JSON
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'vehicle_id': vehicleId,
      'driver_id': driverId,
      'contract_id': contractId,
      'start_time': startTime,
      'end_time': endTime,
      'status': status,
    };
  }
}
