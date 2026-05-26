import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:image_picker/image_picker.dart';
import '../bloc/fuel_bloc.dart';

class FuelReportView extends StatefulWidget {
  final int vehicleId;
  const FuelReportView({super.key, required this.vehicleId});

  @override
  State<FuelReportView> createState() => _FuelReportViewState();
}

class _FuelReportViewState extends State<FuelReportView> {
  final _formKey = GlobalKey<FormState>();
  final _litersController = TextEditingController();
  final _costController = TextEditingController();
  final _odometerController = TextEditingController();
  final _locationController = TextEditingController();
  
  File? _receiptImage;
  final ImagePicker _picker = ImagePicker();

  @override
  void dispose() {
    _litersController.dispose();
    _costController.dispose();
    _odometerController.dispose();
    _locationController.dispose();
    super.dispose();
  }

  // Mengambil foto dari Kamera HP supir
  Future<void> _pickImage() async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.camera,
        imageQuality: 70, // Kompres kualitas gambar agar hemat bandwidth pengiriman
      );
      
      if (photo != null) {
        setState(() {
          _receiptImage = File(photo.path);
        });
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Gagal mengakses kamera ponsel Anda.')),
      );
    }
  }

  // Menyerahkan data laporan BBM ke FuelBloc
  void _submitReport() {
    if (_formKey.currentState!.validate()) {
      context.read<FuelBloc>().add(
        SubmitFuelReportEvent(
          vehicleId: widget.vehicleId,
          volumeLiters: double.parse(_litersController.text.trim()),
          cost: double.parse(_costController.text.trim()),
          odometer: int.parse(_odometerController.text.trim()),
          location: _locationController.text.trim(),
          imageFile: _receiptImage,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1E3A8A),
        foregroundColor: Colors.white,
        title: const Text(
          'Lapor Pengisian BBM',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        elevation: 0,
      ),
      body: BlocListener<FuelBloc, FuelState>(
        listener: (context, state) {
          if (state is FuelSuccess) {
            // Tampilkan Dialog sukses pengiriman laporan
            showDialog(
              context: context,
              barrierDismissible: false,
              builder: (ctx) => AlertDialog(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                title: const Row(
                  children: [
                    Icon(Icons.check_circle_rounded, color: Color(0xFF059669), size: 28),
                    SizedBox(width: 8),
                    Text('Laporan Terkirim', style: TextStyle(fontWeight: FontWeight.bold)),
                  ],
                ),
                content: const Text(
                  'Log pengisian bahan bakar BBM Anda berhasil direkam. Foto struk akan divalidasi oleh Operator Admin.',
                  style: TextStyle(fontSize: 14, color: Color(0xFF475569)),
                ),
                actions: [
                  TextButton(
                    onPressed: () {
                      Navigator.pop(ctx); // Close dialog
                      Navigator.pop(context); // Back to Dashboard
                    },
                    child: const Text('OK', style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            );
          }

          if (state is FuelError) {
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
          padding: const EdgeInsets.all(22.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Container Kamera Pengambilan Struk
                const Text(
                  'FOTO STRUK PEMBELIAN BBM',
                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF64748B), letterSpacing: 1),
                ),
                const SizedBox(height: 8),
                GestureDetector(
                  onTap: _pickImage,
                  child: Container(
                    height: 200,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: const Color(0xFFCBD5E1), width: 1.5),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.04),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: _receiptImage != null
                        ? ClipRRect(
                            borderRadius: BorderRadius.circular(18),
                            child: Stack(
                              fit: StackFit.expand,
                              children: [
                                Image.file(_receiptImage!, fit: BoxFit.cover),
                                Positioned(
                                  top: 10,
                                  right: 10,
                                  child: GestureDetector(
                                    onTap: () {
                                      setState(() {
                                        _receiptImage = null;
                                      });
                                    },
                                    child: CircleAvatar(
                                      radius: 16,
                                      backgroundColor: Colors.black.withOpacity(0.6),
                                      child: const Icon(Icons.close, size: 18, color: Colors.white),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          )
                        : const Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.camera_alt_rounded, size: 48, color: Color(0xFF64748B)),
                              SizedBox(height: 10),
                              Text(
                                'Ketuk untuk Ambil Foto Struk',
                                style: TextStyle(color: Color(0xFF64748B), fontSize: 14, fontWeight: FontWeight.w600),
                              ),
                              SizedBox(height: 4),
                              Text(
                                'Harap foto struk secara jelas dan tegak',
                                style: TextStyle(color: Color(0xFF94A3B8), fontSize: 11),
                              ),
                            ],
                          ),
                  ),
                ),
                const SizedBox(height: 24),

                // Form Input Fields
                Card(
                  elevation: 1,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                  color: Colors.white,
                  child: Padding(
                    padding: const EdgeInsets.all(20.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        // Liter BBM Field
                        _buildInputField(
                          controller: _litersController,
                          label: 'VOLUME PENGISIAN (LITER)',
                          hint: 'Contoh: 45.5',
                          icon: Icons.local_gas_station_rounded,
                          keyboardType: const TextInputType.numberWithOptions(decimal: true),
                          validator: (value) {
                            if (value == null || value.trim().isEmpty) return 'Harap isi volume pengisian BBM';
                            if (double.tryParse(value.trim()) == null) return 'Harap masukkan angka desimal yang valid';
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),

                        // Total Cost Field
                        _buildInputField(
                          controller: _costController,
                          label: 'TOTAL BIAYA (RUPIAH)',
                          hint: 'Contoh: 650000',
                          icon: Icons.payments_rounded,
                          keyboardType: TextInputType.number,
                          validator: (value) {
                            if (value == null || value.trim().isEmpty) return 'Harap isi total biaya';
                            if (double.tryParse(value.trim()) == null) return 'Harap masukkan angka nominal yang valid';
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),

                        // Odometer Field
                        _buildInputField(
                          controller: _odometerController,
                          label: 'ODOMETER SAAT INI (KM)',
                          hint: 'Contoh: 125430',
                          icon: Icons.speed_rounded,
                          keyboardType: TextInputType.number,
                          validator: (value) {
                            if (value == null || value.trim().isEmpty) return 'Harap isi odometer truk saat ini';
                            if (int.tryParse(value.trim()) == null) return 'Harap masukkan angka bulat KM odometer';
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),

                        // Location Field
                        _buildInputField(
                          controller: _locationController,
                          label: 'LOKASI SPBU / PENGISIAN',
                          hint: 'Contoh: SPBU Pertamina Pancoran Jakarta',
                          icon: Icons.location_on_rounded,
                          keyboardType: TextInputType.text,
                          validator: (value) {
                            if (value == null || value.trim().isEmpty) return 'Harap isi lokasi pengisian bensin';
                            return null;
                          },
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 28),

                // Tombol Submit / Loading
                BlocBuilder<FuelBloc, FuelState>(
                  builder: (context, state) {
                    if (state is FuelSubmitting) {
                      return const Center(
                        child: CircularProgressIndicator(
                          valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF2563EB)),
                        ),
                      );
                    }

                    return ElevatedButton(
                      onPressed: _submitReport,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF1E3A8A), // Biru Royal
                        foregroundColor: Colors.white,
                        minimumSize: const Size(double.infinity, 54),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 3,
                      ),
                      child: const Text(
                        'KIRIM LAPORAN BBM',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, letterSpacing: 1),
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Builder Helper untuk merender input form premium
  Widget _buildInputField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    required TextInputType keyboardType,
    required String? Function(String?) validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          keyboardType: keyboardType,
          style: const TextStyle(color: Color(0xFF0F172A), fontSize: 14, fontWeight: FontWeight.w600),
          decoration: InputDecoration(
            prefixIcon: Icon(icon, color: const Color(0xFF64748B), size: 20),
            hintText: hint,
            hintStyle: const TextStyle(color: Color(0xFF94A3B8), fontSize: 13, fontWeight: FontWeight.w400),
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
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFF2563EB), width: 1.5),
            ),
            errorStyle: const TextStyle(color: Colors.redAccent, fontSize: 11),
          ),
          validator: validator,
        ),
      ],
    );
  }
}
