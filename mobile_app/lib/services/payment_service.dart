import 'dart:convert';
import 'package:http/http.dart' as http;

class PaymentService {
  static const String baseUrl = "https://payment-gateway-pro/api";

  Future<Map<String, dynamic>> initializeTransaction(String email, double amount) async {
    final url = Uri.parse('$baseUrl/initialize.php');
    
    try {
      final response = await http.post(
        url,
        headers: {"Content-Type": "application/json"},
        body: jsonEncode({
          "email": email,
          "amount": amount,
        }),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {"status": "error", "message": "Server error: ${response.statusCode}"};
      }
    } catch (e) {
      return {"status": "error", "message": "Connection failed: $e"};
    }
  }

  Future<Map<String, dynamic>> verifyTransaction(String reference) async {
    final url = Uri.parse('$baseUrl/verify.php?reference=$reference');

    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {"status": "error", "message": "Verification failed"};
      }
    } catch (e) {
      return {"status": "error", "message": "Connection error: $e"};
    }
  }
}
