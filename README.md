# SecurePay: PHP & Flutter Payment Gateway

A professional full-stack integration demonstrating a secure payment flow using a PHP backend API and a Flutter mobile application.

## 🚀 Features
* **Secure Initialization:** Backend-driven transaction requests to prevent client-side tampering.
* **Real-time Verification:** Automatic payment status confirmation via server-to-server API calls.
* **Database Logging:** Persistent storage of transaction history using MySQL.
* **Mobile UI:** Clean, modern Flutter checkout interface.

## 📁 Project Structure
* `/backend`: PHP API scripts (`initialize.php`, `verify.php`) and SQL schema.
* `/mobile_app`: Flutter source code, including services and UI screens.

## 🛠️ Setup Instructions

### Backend
1. Import `database.sql` into your MySQL database.
2. Update database credentials in `initialize.php` and `verify.php`.
3. Add your API Secret Key in the Authorization headers.

### Mobile (Flutter)
1. Navigate to `mobile_app/lib/services/payment_service.dart`.
2. Update `baseUrl` to point to your hosted PHP backend.
3. Run `flutter pub get` and then `flutter run`.

## 🛡️ Security Note
This repository uses placeholders for API keys and database credentials. Always use environment variables (`.env`) for production deployments.
