<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $business = $_POST['business'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    // Prepare email content
    $to = "ken@gentoolinkwebservices.com";
    $subject = "New Contact Form Submission from " . htmlspecialchars($name);
    
    $email_content = "Name: " . htmlspecialchars($name) . "\n";
    $email_content .= "Email: " . htmlspecialchars($email) . "\n";
    if (!empty($business)) {
        $email_content .= "Business: " . htmlspecialchars($business) . "\n";
    }
    $email_content .= "\nMessage:\n" . htmlspecialchars($message);

    // Email headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    if (mail($to, $subject, $email_content, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Thank you for your message. We will get back to you soon!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?> 