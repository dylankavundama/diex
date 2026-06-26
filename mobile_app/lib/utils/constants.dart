import 'package:flutter/material.dart';

class AppColors {
  static const Color primaryBlue = Color(0xFF2563EB);
  static const Color primaryGreen = Color(0xFF10B981);
  static const Color primaryRed = Color(0xFFEF4444);
  static const Color primaryOrange = Color(0xFFF59E0B);
  static const Color backgroundLight = Color(0xFFF5F7FA);
  static const Color textDark = Color(0xFF1F2937);
  static const Color textLight = Color(0xFF6B7280);
}

class AppConstants {
  // CRITICAL: Change this to your production URL before deployment (e.g., https://votre-site.com)
  static const String baseUrl = 'https://boutiquedieuagit.com';
  static const String apiUrl = '$baseUrl/api';

  // Taux de change USD vers CDF (Note: Ideally fetch this from API)
  static const double usdToCdfRate = 2200.0;
}

class AppStrings {
  static const String appName = 'Dieu Agit';
  static const String login = 'Connexion';
  static const String logout = 'Déconnexion';
  static const String email = 'Email';
  static const String password = 'Mot de passe';
  static const String loginError = 'Erreur de connexion';
  static const String invalidCredentials = 'Identifiants invalides';
}
