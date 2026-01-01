import 'package:flutter/material.dart';

class VendeurSalesScreen extends StatelessWidget {
  const VendeurSalesScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Ventes'),
      ),
      body: const Center(
        child: Text('Écran Ventes - À implémenter'),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // TODO: Navigate to create sale screen
        },
        child: const Icon(Icons.add),
      ),
    );
  }
}

