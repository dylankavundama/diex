import 'dart:convert';
import 'package:http/http.dart' as http;
import '../utils/constants.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  String? _token;

  void setToken(String? token) {
    _token = token;
  }

  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  Future<Map<String, dynamic>> _handleResponse(http.Response response) async {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      try {
        return json.decode(response.body) as Map<String, dynamic>;
      } catch (e) {
        return {'success': true, 'data': response.body};
      }
    } else {
      final error = json.decode(response.body) as Map<String, dynamic>;
      throw Exception(error['message'] ?? 'Une erreur est survenue');
    }
  }

  Future<Map<String, dynamic>> login(String login, String password) async {
    final response = await http.post(
      Uri.parse('${AppConstants.apiUrl}/auth/login.php'),
      headers: _headers,
      body: json.encode({
        'login': login,
        'password': password,
      }),
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> getDashboardStats(String role) async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/dashboard.php?role=$role'),
      headers: _headers,
    );
    return _handleResponse(response);
  }

  Future<List<dynamic>> getProducts() async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/products.php'),
      headers: _headers,
    );
    final data = await _handleResponse(response);
    return data['data'] as List<dynamic>;
  }

  Future<Map<String, dynamic>> getProduct(int id) async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/products.php?id=$id'),
      headers: _headers,
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> createProduct(Map<String, dynamic> productData) async {
    final response = await http.post(
      Uri.parse('${AppConstants.apiUrl}/products.php'),
      headers: _headers,
      body: json.encode(productData),
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> updateProduct(int id, Map<String, dynamic> productData) async {
    final response = await http.put(
      Uri.parse('${AppConstants.apiUrl}/products.php?id=$id'),
      headers: _headers,
      body: json.encode(productData),
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> deleteProduct(int id) async {
    final response = await http.delete(
      Uri.parse('${AppConstants.apiUrl}/products.php?id=$id'),
      headers: _headers,
    );
    return _handleResponse(response);
  }

  Future<List<dynamic>> getOrders() async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/orders.php'),
      headers: _headers,
    );
    final data = await _handleResponse(response);
    return data['data'] as List<dynamic>;
  }

  Future<Map<String, dynamic>> getOrder(int id) async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/orders.php?id=$id'),
      headers: _headers,
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> createOrder(Map<String, dynamic> orderData) async {
    final response = await http.post(
      Uri.parse('${AppConstants.apiUrl}/orders.php'),
      headers: _headers,
      body: json.encode(orderData),
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> updateOrderStatus(int id, String status) async {
    final response = await http.put(
      Uri.parse('${AppConstants.apiUrl}/orders.php?id=$id'),
      headers: _headers,
      body: json.encode({'statut': status}),
    );
    return _handleResponse(response);
  }

  Future<List<dynamic>> getClients() async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/clients.php'),
      headers: _headers,
    );
    final data = await _handleResponse(response);
    return data['data'] as List<dynamic>;
  }

  Future<Map<String, dynamic>> createClient(Map<String, dynamic> clientData) async {
    final response = await http.post(
      Uri.parse('${AppConstants.apiUrl}/clients.php'),
      headers: _headers,
      body: json.encode(clientData),
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> getCashBalance() async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/cash/balance.php'),
      headers: _headers,
    );
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> recordExpense(Map<String, dynamic> expenseData) async {
    final response = await http.post(
      Uri.parse('${AppConstants.apiUrl}/cash/expense.php'),
      headers: _headers,
      body: json.encode(expenseData),
    );
    return _handleResponse(response);
  }

  Future<List<dynamic>> getCashMovements() async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/cash/movements.php'),
      headers: _headers,
    );
    final data = await _handleResponse(response);
    return data['data'] as List<dynamic>;
  }

  Future<Map<String, dynamic>> getReports(String period) async {
    final response = await http.get(
      Uri.parse('${AppConstants.apiUrl}/reports.php?period=$period'),
      headers: _headers,
    );
    return _handleResponse(response);
  }
}

