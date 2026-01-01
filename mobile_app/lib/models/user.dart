class User {
  final int id;
  final String nom;
  final String prenom;
  final String email;
  final String? telephone;
  final String role;
  final String statut;

  User({
    required this.id,
    required this.nom,
    required this.prenom,
    required this.email,
    this.telephone,
    required this.role,
    required this.statut,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] as int,
      nom: json['nom'] as String,
      prenom: json['prenom'] as String,
      email: json['email'] as String,
      telephone: json['telephone'] as String?,
      role: json['role'] as String,
      statut: json['statut'] as String,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nom': nom,
      'prenom': prenom,
      'email': email,
      'telephone': telephone,
      'role': role,
      'statut': statut,
    };
  }

  String get fullName => '$nom $prenom';
  bool get isAdmin => role == 'admin';
  bool get isVendeur => role == 'vendeur';
}

