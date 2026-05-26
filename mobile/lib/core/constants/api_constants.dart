class ApiConstants {
  // Gunakan http://10.0.2.2:8000/api agar Emulator Android dapat mengakses localhost mesin pengembang
  static const String baseUrl = 'http://10.0.2.2:8000/api';
  
  // Kunci Penyimpanan Lokal (Shared Preferences Keys)
  static const String keyToken = 'auth_token';
  static const String keyUserEmail = 'user_email';
  static const String keyUserName = 'user_name';
  static const String keyDriverId = 'driver_id';
  static const String keyActiveTripId = 'active_trip_id';
  static const String keyActiveVehiclePlate = 'active_vehicle_plate';
  static const String keyActiveVehicleId = 'active_vehicle_id';
}
