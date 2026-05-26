import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/network/api_client.dart';
import '../../auth/bloc/auth_bloc.dart';
import '../../fuel/bloc/fuel_bloc.dart';
import '../../fuel/views/fuel_report_view.dart';
import '../bloc/trip_bloc.dart';

class TripDashboardView extends StatefulWidget {
  const TripDashboardView({super.key});

  @override
  State<TripDashboardView> createState() => _TripDashboardViewState();
}

class _TripDashboardViewState extends State<TripDashboardView> {
  int? _selectedVehicleId;
  String? _selectedVehiclePlate;
  
  // Ticker waktu perjalanan dinamis
  Timer? _tickerTimer;
  int _secondsElapsed = 0;

  @override
  void dispose() {
    _tickerTimer?.cancel();
    super.dispose();
  }

  // Mulai detak timer perjalanan aktif
  void _startTicker(String startTimeStr) {
    _tickerTimer?.cancel();
    DateTime startTime;
    try {
      startTime = DateTime.parse(startTimeStr);
    } catch (_) {
      startTime = DateTime.now();
    }
    
    _secondsElapsed = DateTime.now().difference(startTime).inSeconds;
    if (_secondsElapsed < 0) _secondsElapsed = 0;

    _tickerTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (mounted) {
        setState(() {
          _secondsElapsed++;
        });
      }
    });
  }

  // Matikan detak timer
  void _stopTicker() {
    _tickerTimer?.cancel();
    _tickerTimer = null;
    _secondsElapsed = 0;
  }

  // Format durasi detik ke bentuk HH:MM:SS
  String _formatDuration(int totalSeconds) {
    final int hours = totalSeconds ~/ 3600;
    final int minutes = (totalSeconds % 3600) ~/ 60;
    final int seconds = totalSeconds % 60;
    
    final String hStr = hours.toString().padLeft(2, '0');
    final String mStr = minutes.toString().padLeft(2, '0');
    final String sStr = seconds.toString().padLeft(2, '0');
    
    return '$hStr:$mStr:$sStr';
  }

  @override
  Widget build(BuildContext context) {
    final authState = context.watch<AuthBloc>().state;
    String supirName = 'Supir PT. Erickman';
    String supirEmail = '';
    
    if (authState is Authenticated) {
      supirName = authState.user.name;
      supirEmail = authState.user.email;
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9), // Latar terang matching White Clean Theme
      appBar: AppBar(
        backgroundColor: const Color(0xFF1E3A8A), // Biru Royal khas Erickman
        foregroundColor: Colors.white,
        title: const Text(
          'Dashboard Supir',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 20),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout_rounded),
            tooltip: 'Logout',
            onPressed: () {
              // Konfirmasi logout
              showDialog(
                context: context,
                builder: (ctx) => AlertDialog(
                  title: const Text('Konfirmasi Logout'),
                  content: const Text('Apakah Anda yakin ingin keluar dari aplikasi supir?'),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.pop(ctx),
                      child: const Text('Batal'),
                    ),
                    TextButton(
                      onPressed: () {
                        Navigator.pop(ctx);
                        context.read<AuthBloc>().add(LogoutEvent());
                      },
                      child: const Text('Keluar', style: TextStyle(color: Colors.redAccent)),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
        elevation: 0,
      ),
      body: BlocListener<TripBloc, TripState>(
        listener: (context, state) {
          // Monitor state active untuk menyalakan/mematikan ticker waktu perjalanan
          if (state is TripActive) {
            _startTicker(state.trip.startTime);
          } else {
            _stopTicker();
          }

          // Tangani notifikasi kesalahan (error SnackBar)
          if (state is TripError) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(state.message),
                backgroundColor: Colors.redAccent,
                behavior: SnackBarBehavior.floating,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
            );
          }
        },
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // 1. Profil Banner Driver
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                color: Colors.white,
                child: Padding(
                  padding: const EdgeInsets.all(18.0),
                  child: Row(
                    children: [
                      CircleAvatar(
                        radius: 28,
                        backgroundColor: const Color(0xFF1E3A8A).withOpacity(0.1),
                        child: const Icon(Icons.person_pin, size: 36, color: Color(0xFF1E3A8A)),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              supirName,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: Color(0xFF0F172A),
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              supirEmail,
                              style: const TextStyle(fontSize: 13, color: Color(0xFF64748B)),
                            ),
                            const SizedBox(height: 2),
                            const Text(
                              'Status: Dedicated Supir',
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Color(0xFF2563EB)),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // 2. Kontrol Panel Berdasarkan State
              BlocBuilder<TripBloc, TripState>(
                builder: (context, state) {
                  if (state is TripLoading) {
                    return const Card(
                      elevation: 2,
                      child: Padding(
                        padding: EdgeInsets.all(40.0),
                        child: Center(
                          child: CircularProgressIndicator(
                            valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF2563EB)),
                          ),
                        ),
                      ),
                    );
                  }

                  // A. Tampilan Trip AKTIF (Sedang Jalan)
                  if (state is TripActive) {
                    return Column(
                      children: [
                        // Card Status Perjalanan Aktif
                        Card(
                          elevation: 3,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                          color: Colors.white,
                          child: Padding(
                            padding: const EdgeInsets.all(22.0),
                            child: Column(
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    const Row(
                                      children: [
                                        Icon(Icons.local_shipping, color: Color(0xFF059669), size: 24),
                                        SizedBox(width: 8),
                                        Text(
                                          'Perjalanan Aktif',
                                          style: TextStyle(
                                            fontSize: 16,
                                            fontWeight: FontWeight.bold,
                                            color: Color(0xFF0F172A),
                                          ),
                                        ),
                                      ],
                                    ),
                                    // Pulsing Live GPS Indicator
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: const Color(0xFFD1FAE5),
                                        borderRadius: BorderRadius.circular(20),
                                      ),
                                      child: const Row(
                                        children: [
                                          Icon(Icons.gps_fixed, size: 12, color: Color(0xFF059669)),
                                          SizedBox(width: 4),
                                          Text(
                                            'GPS AKTIF',
                                            style: TextStyle(fontSize: 10, color: Color(0xFF059669), fontWeight: FontWeight.bold),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                                const Divider(height: 24),
                                const Text(
                                  'DURASI PERJALANAN',
                                  style: TextStyle(fontSize: 11, color: Color(0xFF64748B), fontWeight: FontWeight.w600, letterSpacing: 1),
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  _formatDuration(_secondsElapsed),
                                  style: const TextStyle(
                                    fontSize: 36,
                                    fontWeight: FontWeight.bold,
                                    color: Color(0xFF0F172A),
                                    fontFamily: 'monospace',
                                  ),
                                ),
                                const SizedBox(height: 18),
                                Container(
                                  width: double.infinity,
                                  padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFF8FAFC),
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(color: const Color(0xFFE2E8F0)),
                                  ),
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      const Text('Plat Armada:', style: TextStyle(color: Color(0xFF64748B), fontSize: 14)),
                                      Text(
                                        state.vehiclePlate,
                                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A)),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(height: 20),

                        // Tombol 1: Laporkan Pengisian BBM
                        ElevatedButton.icon(
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => BlocProvider<FuelBloc>(
                                  create: (context) => FuelBloc(
                                    apiClient: context.read<ApiClient>(),
                                  ),
                                  child: FuelReportView(vehicleId: state.trip.vehicleId),
                                ),
                              ),
                            );
                          },
                          icon: const Icon(Icons.local_gas_station_rounded, size: 22),
                          label: const Text(
                            'LAPORKAN PENGISIAN BBM',
                            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFFD97706), // Orange BBM
                            foregroundColor: Colors.white,
                            minimumSize: const Size(double.infinity, 54),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            elevation: 2,
                          ),
                        ),
                        const SizedBox(height: 14),

                        // Tombol 2: Akhiri Perjalanan (End Trip)
                        ElevatedButton.icon(
                          onPressed: () {
                            showDialog(
                              context: context,
                              builder: (ctx) => AlertDialog(
                                title: const Text('Selesaikan Perjalanan?'),
                                content: const Text('Apakah Anda yakin telah sampai di tujuan dan ingin mengakhiri pelacakan?'),
                                actions: [
                                  TextButton(
                                    onPressed: () => Navigator.pop(ctx),
                                    child: const Text('Kembali'),
                                  ),
                                  TextButton(
                                    onPressed: () {
                                      Navigator.pop(ctx);
                                      context.read<TripBloc>().add(EndTripEvent());
                                    },
                                    child: const Text('Akhiri Perjalanan', style: TextStyle(color: Colors.redAccent)),
                                  ),
                                ],
                              ),
                            );
                          },
                          icon: const Icon(Icons.stop_circle_rounded, size: 22),
                          label: const Text(
                            'SELESAI PERJALANAN',
                            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFFDC2626), // Merah Bahaya
                            foregroundColor: Colors.white,
                            minimumSize: const Size(double.infinity, 54),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            elevation: 2,
                          ),
                        ),
                      ],
                    );
                  }

                  // B. Tampilan Trip IDLE (Siap Berangkat)
                  final vehicles = state is TripIdle ? state.availableVehicles : [];
                  
                  return Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    color: Colors.white,
                    child: Padding(
                      padding: const EdgeInsets.all(22.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'Persiapan Perjalanan',
                                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                              ),
                              IconButton(
                                icon: const Icon(Icons.refresh_rounded, color: Color(0xFF2563EB)),
                                onPressed: () {
                                  context.read<TripBloc>().add(FetchAvailableVehiclesEvent());
                                },
                                tooltip: 'Refresh Kendaraan',
                              ),
                            ],
                          ),
                          const Divider(height: 20),
                          const SizedBox(height: 8),
                          
                          if (vehicles.isEmpty) ...[
                            Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: const Color(0xFFFEF2F2),
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: const Color(0xFFFEE2E2)),
                              ),
                              child: const Column(
                                children: [
                                  Icon(Icons.warning_amber_rounded, size: 36, color: Color(0xFFDC2626)),
                                  SizedBox(height: 8),
                                  Text(
                                    'Semua armada sedang jalan atau tidak ada kendaraan tersedia.',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(fontSize: 13, color: Color(0xFF991B1B), fontWeight: FontWeight.w500),
                                  ),
                                ],
                              ),
                            ),
                          ] else ...[
                            const Text(
                              'PILIH KENDARAAN / ARMADA',
                              style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Color(0xFF64748B)),
                            ),
                            const SizedBox(height: 8),
                            DropdownButtonFormField<int>(
                              value: _selectedVehicleId,
                              hint: const Text('Pilih plat nomor truk Anda'),
                              decoration: InputDecoration(
                                filled: true,
                                fillColor: const Color(0xFFF8FAFC),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  borderSide: const BorderSide(color: Color(0xFFCBD5E1)),
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                                ),
                              ),
                              items: vehicles.map<DropdownMenuItem<int>>((dynamic item) {
                                final Map<String, dynamic> v = item as Map<String, dynamic>;
                                return DropdownMenuItem<int>(
                                  value: v['id'] as int,
                                  child: Text(
                                    "${v['plate_number']} - ${v['type']} (${v['capacity']} Kg)",
                                    style: const TextStyle(fontSize: 14),
                                  ),
                                );
                              }).toList(),
                              onChanged: (int? value) {
                                setState(() {
                                  _selectedVehicleId = value;
                                  // Cari plat nomornya untuk disimpan di state
                                  final selected = vehicles.firstWhere((element) => element['id'] == value);
                                  _selectedVehiclePlate = selected['plate_number'] as String;
                                });
                              },
                            ),
                            const SizedBox(height: 28),
                            ElevatedButton(
                              onPressed: _selectedVehicleId == null
                                  ? null
                                  : () {
                                      context.read<TripBloc>().add(
                                        StartTripEvent(
                                          vehicleId: _selectedVehicleId!,
                                          vehiclePlate: _selectedVehiclePlate!,
                                        ),
                                      );
                                    },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF2563EB), // Biru Aksen
                                foregroundColor: Colors.white,
                                minimumSize: const Size(double.infinity, 54),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                                elevation: 3,
                                disabledBackgroundColor: const Color(0xFFCBD5E1),
                              ),
                              child: const Text(
                                'MULAI PERJALANAN',
                                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, letterSpacing: 1),
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}
