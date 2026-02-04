<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($phone) || empty($email) || empty($service)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    
    // Email details (configure these for your server)
    $to = "info@villagefeller.co.za";
    $subject = "New Quote Request from Village Feller Website";
    
    $emailBody = "
    <html>
    <head>
        <title>New Quote Request</title>
    </head>
    <body>
        <h2>New Quote Request</h2>
        <p><strong>Name:</strong> $firstName $lastName</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Service:</strong> $service</p>
        <p><strong>Message:</strong> $message</p>
        <p><em>This email was sent from the Village Feller website contact form.</em></p>
    </body>
    </html>
    ";
    
    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Village Feller Website <noreply@villagefeller.co.za>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    
    // Try to send email
    if (mail($to, $subject, $emailBody, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Thank you for your request! We will contact you shortly.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
