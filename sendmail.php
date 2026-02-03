<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = ['success' => false, 'message' => ''];

try {
    // Get form data
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $service = isset($_POST['service']) ? trim($_POST['service']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($phone) || empty($email) || empty($service)) {
        throw new Exception('Please fill in all required fields.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address.');
    }
    
    // Service options mapping
    $serviceOptions = [
        'tree-felling' => 'Tree Felling / Removal',
        'stump-removal' => 'Stump Removal',
        'tree-trimming' => 'Tree Trimming / Pruning',
        'palm-shaving' => 'Palm Shaving',
        'site-clearing' => 'Plot & Site Clearing',
        'garden-cleanup' => 'Garden Clean-ups'
    ];
    
    $serviceName = isset($serviceOptions[$service]) ? $serviceOptions[$service] : $service;
    
    // Email configuration
    $to = 'info@villagefeller.co.za'; // Change to your email
    $subject = 'New Quote Request - Village Feller Website';
    
    // Email content
    $emailContent = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #2e7d32; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #2e7d32; }
            .value { margin-top: 5px; }
            .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>New Quote Request</h1>
            <p>Village Feller Website</p>
        </div>
        
        <div class='content'>
            <div class='field'>
                <div class='label'>Client Information:</div>
                <div class='value'>$firstName $lastName</div>
            </div>
            
            <div class='field'>
                <div class='label'>Contact Details:</div>
                <div class='value'>
                    Email: $email<br>
                    Phone: $phone
                </div>
            </div>
            
            <div class='field'>
                <div class='label'>Requested Service:</div>
                <div class='value'>$serviceName</div>
            </div>
            
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Submission Date:</div>
                <div class='value'>" . date('F j, Y \a\t g:i A') . "</div>
            </div>
        </div>
        
        <div class='footer'>
            <p>This email was sent from the Village Feller website contact form.</p>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Village Feller Website <noreply@villagefeller.co.za>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    if (mail($to, $subject, $emailContent, $headers)) {
        $response['success'] = true;
        $response['message'] = 'Thank you for your request! We will contact you shortly.';
        
        // Optional: Send confirmation to client
        $clientSubject = 'Thank you for your quote request - Village Feller';
        $clientMessage = "
        Dear $firstName $lastName,
        
        Thank you for contacting Village Feller. We have received your request for $serviceName.
        
        Our team will review your request and contact you within 24-48 hours at the phone number or email address you provided.
        
        Request Details:
        - Service: $serviceName
        - Submitted: " . date('F j, Y \a\t g:i A') . "
        
        If you have any urgent questions, please call us at +27 78 936 6509.
        
        Best regards,
        The Village Feller Team
        info@villagefeller.co.za
        +27 78 936 6509
        ";
        
        $clientHeaders = "From: Village Feller <info@villagefeller.co.za>\r\n";
        mail($email, $clientSubject, $clientMessage, $clientHeaders);
        
    } else {
        throw new Exception('Failed to send email. Please try again later.');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
