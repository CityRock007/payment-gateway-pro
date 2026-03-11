<?php
header("Content-Type: application/json");

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    echo json_encode(["status" => "error", "message" => "No reference provided"]);
    exit;
}

// Database Connection
$host = 'localhost';
$db_name = 'payment_gateway_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
} catch(PDOException $e) {
    die(json_encode(["status" => "error", "message" => "DB Connection failed"]));
}

// Verify with Paystack/Provider
$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer YOUR_SECRET_KEY",
    "Cache-Control: no-cache",
]);

$result = curl_exec($ch);
$response = json_decode($result, true);
curl_close($ch);

if ($response['status'] && $response['data']['status'] === 'success') {
    // Update DB status to success
    $stmt = $conn->prepare("UPDATE transactions SET status = 'success' WHERE reference = ?");
    $stmt->execute([$reference]);

    echo json_encode([
        "status" => "success",
        "message" => "Payment Verified",
        "data" => $response['data']
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Payment verification failed"]);
}
?>
