import '../utils/constants.dart';

class Product {
  final int id;
  final String nom;
  final String? description;
  final double prixAchat;
  final double prixVente;
  final int stock;
  final int stockMinimum;
  final int categorieId;
  final int? vendeurId;
  final String? imagePrincipale;
  final String statut;
  final DateTime createdAt;
  final DateTime? updatedAt;
  final String? categorieNom;
  final String? vendeurNom;

  Product({
    required this.id,
    required this.nom,
    this.description,
    required this.prixAchat,
    required this.prixVente,
    required this.stock,
    required this.stockMinimum,
    required this.categorieId,
    this.vendeurId,
    this.imagePrincipale,
    required this.statut,
    required this.createdAt,
    this.updatedAt,
    this.categorieNom,
    this.vendeurNom,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'] as int,
      nom: json['nom'] as String,
      description: json['description'] as String?,
      prixAchat: (json['prix_achat'] as num).toDouble(),
      prixVente: (json['prix_vente'] as num).toDouble(),
      stock: json['stock'] as int,
      stockMinimum: json['stock_minimum'] as int,
      categorieId: json['categorie_id'] as int,
      vendeurId: json['vendeur_id'] as int?,
      imagePrincipale: json['image_principale'] as String?,
      statut: json['statut'] as String,
      createdAt: DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] != null 
          ? DateTime.parse(json['updated_at'] as String) 
          : null,
      categorieNom: json['categorie_nom'] as String?,
      vendeurNom: json['vendeur_nom'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nom': nom,
      'description': description,
      'prix_achat': prixAchat,
      'prix_vente': prixVente,
      'stock': stock,
      'stock_minimum': stockMinimum,
      'categorie_id': categorieId,
      'vendeur_id': vendeurId,
      'image_principale': imagePrincipale,
      'statut': statut,
    };
  }

  bool get isLowStock => stock <= stockMinimum;
  bool get isActive => statut == 'actif';
  double get benefice => prixVente - prixAchat;
  String get imageUrl => imagePrincipale != null 
      ? '${AppConstants.baseUrl}/uploads/products/$imagePrincipale'
      : '';
}

