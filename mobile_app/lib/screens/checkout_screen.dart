import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart'; // Ensure you add this to pubspec.yaml
import '../services/payment_service.dart';

class CheckoutScreen extends StatefulWidget {
  @override
  _CheckoutScreenState createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _emailController = TextEditingController();
  final _amountController = TextEditingController();
  bool _isLoading = false;

  final PaymentService _paymentService = PaymentService();

  void _processPayment() async {
    setState(() => _isLoading = true);

    final email = _emailController.text.trim();
    final amount = double.tryParse(_amountController.text.trim()) ?? 0;

    if (email.isEmpty || amount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Please enter valid details")),
      );
      setState(() => _isLoading = false);
      return;
    }

    final response = await _paymentService.initializeTransaction(email, amount);

    setState(() => _isLoading = false);

    if (response['status'] == 'success') {
      final url = Uri.parse(response['authorization_url']);
      if (await canLaunchUrl(url)) {
        await launchUrl(url, mode: LaunchMode.externalApplication);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Could not open payment gateway")),
        );
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(response['message'] ?? "Initialization failed")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Secure Checkout")),
      body: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          children: [
            TextField(
              controller: _emailController,
              decoration: InputDecoration(labelText: "Email Address", border: OutlineInputBorder()),
              keyboardType: TextInputType.emailAddress,
            ),
            SizedBox(height: 20),
            TextField(
              controller: _amountController,
              decoration: InputDecoration(labelText: "Amount", border: OutlineInputBorder()),
              keyboardType: TextInputType.number,
            ),
            SizedBox(height: 30),
            _isLoading 
              ? CircularProgressIndicator() 
              : ElevatedButton(
                  onPressed: _processPayment,
                  child: Text("Pay Now"),
                  style: ElevatedButton.styleFrom(minimumSize: Size(double.infinity, 50)),
                ),
          ],
        ),
      ),
    );
  }
}
