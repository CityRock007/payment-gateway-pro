<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$host = 'localhost';
$db_name = 'payment_gateway_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Get raw POST data from Flutter App
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->amount)) {
    $email = $data->email;
    $amount = $data->amount * 100; // Convert to kobo/cents for the gateway
    $reference = "TXN_" . bin2hex(random_bytes(8)); // Generate unique reference

    $url = "https://api.paystack.co/transaction/initialize";
    $fields = [
        'email' => $email,
        'amount' => $amount,
        'reference' => $reference,
        'callback_url' => "https://yourdomain.com/verify.php" 
    ];

    $fields_string = http_build_query($fields);

    // Open CURL connection
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer YOUR_SECRET_KEY", // Placeholder for security
        "Cache-Control: no-cache",
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

    $result = curl_exec($ch);
    $response = json_decode($result, true);
    curl_close($ch);

    if ($response['status']) {
        $stmt = $conn->prepare("INSERT INTO transactions (email, amount, reference, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$email, $data->amount, $reference]);

        echo json_encode([
            "status" => "success",
            "authorization_url" => $response['data']['authorization_url'],
            "reference" => $reference
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gateway initialization failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Incomplete data provided"]);
}
?>
