<?php
header('Content-Type: application/json');

// Verify reCAPTCHA
$recaptcha_secret = "6LfjgiwrAAAAAGZiN4s9sP5BHYkVFHkS3W9RgAkX";
$recaptcha_response = $_POST['g-recaptcha-response'];

$verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
$response_data = json_decode($verify_response);

if (!$response_data->success) {
    echo json_encode([
        'success' => false,
        'message' => 'Please complete the reCAPTCHA verification.'
    ]);
    exit;
}

// Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$business = $_POST['business'] ?? '';
$message = $_POST['message'] ?? '';

// Validate required fields
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields.'
    ]);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

// Prepare email
$to = "ken@gentoolinkwebservices.com";
$subject = "New Contact Form Submission from " . htmlspecialchars($name);
$headers = "From: " . htmlspecialchars($email) . "\r\n";
$headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$email_content = "Name: " . htmlspecialchars($name) . "\n";
$email_content .= "Email: " . htmlspecialchars($email) . "\n";
if (!empty($business)) {
    $email_content .= "Business: " . htmlspecialchars($business) . "\n";
}
$email_content .= "\nMessage:\n" . htmlspecialchars($message);

// Send email
$mail_sent = mail($to, $subject, $email_content, $headers);

if ($mail_sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message. We will get back to you soon!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again later.'
    ]);
}
?> 