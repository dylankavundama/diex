import '../utils/constants.dart';

class Order {
  final int id;
  final String numeroCommande;
  final int clientId;
  final double total;
  final String statut;
  final String? typeVente;
  final DateTime createdAt;
  final DateTime? updatedAt;
  final String? clientNom;
  final String? clientPrenom;
  final List<OrderItem>? items;

  Order({
    required this.id,
    required this.numeroCommande,
    required this.clientId,
    required this.total,
    required this.statut,
    this.typeVente,
    required this.createdAt,
    this.updatedAt,
    this.clientNom,
    this.clientPrenom,
    this.items,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'] as int,
      numeroCommande: json['numero_commande'] as String,
      clientId: json['client_id'] as int,
      total: (json['total'] as num).toDouble(),
      statut: json['statut'] as String,
      typeVente: json['type_vente'] as String?,
      createdAt: DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] != null 
          ? DateTime.parse(json['updated_at'] as String) 
          : null,
      clientNom: json['client_nom'] as String?,
      clientPrenom: json['client_prenom'] as String?,
      items: json['items'] != null
          ? (json['items'] as List).map((i) => OrderItem.fromJson(i)).toList()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'numero_commande': numeroCommande,
      'client_id': clientId,
      'total': total,
      'statut': statut,
      'type_vente': typeVente,
    };
  }

  String get clientFullName => '$clientNom $clientPrenom';
  bool get isPending => statut == 'en_attente';
  bool get isDelivered => statut == 'livree';
  bool get isCancelled => statut == 'annulee';
  bool get isCredit => typeVente == 'credit';
}

class OrderItem {
  final int id;
  final int orderId;
  final int productId;
  final int quantity;
  final double prixUnitaire;
  final double total;
  final double benefice;
  final String? productNom;

  OrderItem({
    required this.id,
    required this.orderId,
    required this.productId,
    required this.quantity,
    required this.prixUnitaire,
    required this.total,
    required this.benefice,
    this.productNom,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'] as int,
      orderId: json['order_id'] as int,
      productId: json['product_id'] as int,
      quantity: json['quantity'] as int,
      prixUnitaire: (json['prix_unitaire'] as num).toDouble(),
      total: (json['total'] as num).toDouble(),
      benefice: (json['benefice'] as num).toDouble(),
      productNom: json['product_nom'] as String?,
    );
  }
}

