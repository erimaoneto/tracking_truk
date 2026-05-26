import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/api_constants.dart';

class ApiClient {
  final http.Client _client = http.Client();

  // Mengambil header standar, otomatis menyertakan Bearer Token jika tersimpan di memori lokal
  Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString(ApiConstants.keyToken);
    
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }
    
    return headers;
  }

  // HTTP POST Request
  Future<http.Response> post(String endpoint, Map<String, dynamic> body) async {
    final url = Uri.parse('${ApiConstants.baseUrl}$endpoint');
    final headers = await _getHeaders();
    
    return await _client.post(
      url,
      headers: headers,
      body: jsonEncode(body),
    );
  }

  // HTTP GET Request
  Future<http.Response> get(String endpoint) async {
    final url = Uri.parse('${ApiConstants.baseUrl}$endpoint');
    final headers = await _getHeaders();
    
    return await _client.get(
      url,
      headers: headers,
    );
  }

  // HTTP POST Multipart Request (untuk mengunggah file foto struk bensin)
  Future<http.StreamedResponse> multipartPost(
    String endpoint, 
    Map<String, String> fields, 
    File file, 
    String fileFieldName
  ) async {
    final url = Uri.parse('${ApiConstants.baseUrl}$endpoint');
    final headers = await _getHeaders();
    // Request multipart mengelola Content-Type batas (boundary) secara otomatis, jadi hapus JSON type
    headers.remove('Content-Type');
    
    final request = http.MultipartRequest('POST', url);
    request.headers.addAll(headers);
    request.fields.addAll(fields);
    
    // Deteksi tipe konten mime berdasarkan ekstensi file
    final mimeType = file.path.toLowerCase().endsWith('.png') ? 'image/png' : 'image/jpeg';
    
    request.files.add(
      await http.MultipartFile.fromPath(
        fileFieldName,
        file.path,
        contentType: MediaType.parse(mimeType),
      ),
    );
    
    return await request.send();
  }
}
