import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  final SharedPreferences prefs;
  User? _user;
  bool _isAuthenticated = false;
  bool _isLoading = false;
  String? _error;

  AuthProvider(this.prefs) {
    _loadUser();
  }

  User? get user => _user;
  bool get isAuthenticated => _isAuthenticated;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> _loadUser() async {
    final userId = prefs.getInt('user_id');
    if (userId != null) {
      _isAuthenticated = true;
      _user = User(
        id: userId,
        nom: prefs.getString('user_nom') ?? '',
        prenom: prefs.getString('user_prenom') ?? '',
        email: prefs.getString('user_email') ?? '',
        telephone: prefs.getString('user_telephone'),
        role: prefs.getString('user_role') ?? '',
        statut: prefs.getString('user_statut') ?? 'actif',
      );
      notifyListeners();
    }
  }

  Future<bool> login(String login, String password) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await ApiService().login(login, password);
      
      if (response['success'] == true) {
        final userData = response['user'] as Map<String, dynamic>;
        _user = User.fromJson(userData);
        _isAuthenticated = true;

        // Sauvegarder dans SharedPreferences
        await prefs.setInt('user_id', _user!.id);
        await prefs.setString('user_nom', _user!.nom);
        await prefs.setString('user_prenom', _user!.prenom);
        await prefs.setString('user_email', _user!.email);
        if (_user!.telephone != null) {
          await prefs.setString('user_telephone', _user!.telephone!);
        }
        await prefs.setString('user_role', _user!.role);
        await prefs.setString('user_statut', _user!.statut);
        
        if (response['token'] != null) {
          await prefs.setString('auth_token', response['token']);
          ApiService().setToken(response['token']);
        }

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Erreur de connexion';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    _user = null;
    _isAuthenticated = false;
    
    await prefs.remove('user_id');
    await prefs.remove('user_nom');
    await prefs.remove('user_prenom');
    await prefs.remove('user_email');
    await prefs.remove('user_telephone');
    await prefs.remove('user_role');
    await prefs.remove('user_statut');
    await prefs.remove('auth_token');
    
    ApiService().setToken(null);
    notifyListeners();
  }
}

