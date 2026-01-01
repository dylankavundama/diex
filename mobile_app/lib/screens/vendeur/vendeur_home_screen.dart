import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import 'vendeur_dashboard_screen.dart';
import 'vendeur_products_screen.dart';
import 'vendeur_sales_screen.dart';
import 'vendeur_reports_screen.dart';
import 'vendeur_cash_screen.dart';

class VendeurHomeScreen extends StatefulWidget {
  const VendeurHomeScreen({Key? key}) : super(key: key);

  @override
  State<VendeurHomeScreen> createState() => _VendeurHomeScreenState();
}

class _VendeurHomeScreenState extends State<VendeurHomeScreen> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    const VendeurDashboardScreen(),
    const VendeurProductsScreen(),
    const VendeurSalesScreen(),
    const VendeurReportsScreen(),
    const VendeurCashScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('Diexo Vendeur'),
        backgroundColor: Colors.green,
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () async {
              await authProvider.logout();
              if (mounted) {
                Navigator.of(context).pushReplacementNamed('/login');
              }
            },
          ),
        ],
      ),
      body: _screens[_currentIndex],
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: (index) {
          setState(() {
            _currentIndex = index;
          });
        },
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.dashboard_outlined),
            selectedIcon: Icon(Icons.dashboard),
            label: 'Tableau de bord',
          ),
          NavigationDestination(
            icon: Icon(Icons.inventory_2_outlined),
            selectedIcon: Icon(Icons.inventory_2),
            label: 'Produits',
          ),
          NavigationDestination(
            icon: Icon(Icons.point_of_sale_outlined),
            selectedIcon: Icon(Icons.point_of_sale),
            label: 'Ventes',
          ),
          NavigationDestination(
            icon: Icon(Icons.assessment_outlined),
            selectedIcon: Icon(Icons.assessment),
            label: 'Rapports',
          ),
          NavigationDestination(
            icon: Icon(Icons.money_outlined),
            selectedIcon: Icon(Icons.money),
            label: 'Caisse',
          ),
        ],
      ),
    );
  }
}

