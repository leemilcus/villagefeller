<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['firstName', 'lastName', 'phone', 'email', 'service'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Prepare email content
    $to = "info@villagefeller.co.za"; // Your email address
    $subject = "New Quote Request - Village Feller";
    
    $message = "New quote request received:\n\n";
    $message .= "Name: " . htmlspecialchars($input['firstName'] . ' ' . $input['lastName']) . "\n";
    $message .= "Phone: " . htmlspecialchars($input['phone']) . "\n";
    $message .= "Email: " . htmlspecialchars($input['email']) . "\n";
    $message .= "Service: " . htmlspecialchars($input['service']) . "\n";
    $message .= "Message: " . htmlspecialchars($input['message'] ?? 'No message') . "\n";
    $message .= "\nSubmitted: " . date('Y-m-d H:i:s');
    
    $headers = "From: website@villagefeller.co.za\r\n";
    $headers .= "Reply-To: " . htmlspecialchars($input['email']) . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    if (mail($to, $subject, $message, $headers)) {
        $response['success'] = true;
        $response['message'] = 'Email sent successfully';
        
        // Also save to a local file for backup
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $input
        ];
        file_put_contents('quote_requests.log', json_encode($logData) . "\n", FILE_APPEND);
    } else {
        throw new Exception('Failed to send email');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
