<?php
// Verify reCAPTCHA
$recaptcha_secret = "6LfjgiwrAAAAAGZiN4s9sP5BHYkVFHkS3W9RgAkX";
$recaptcha_response = $_POST['g-recaptcha-response'];

$verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
$response_data = json_decode($verify_response);

if (!$response_data->success) {
    header('Location: index.html?error=captcha');
    exit;
}

// Get form data
$business_name = $_POST['business-name'] ?? '';
$business_category = $_POST['business-category'] ?? '';
$city = $_POST['city'] ?? '';
$gbp = $_POST['gbp'] ?? '';
$review_count = $_POST['review-count'] ?? 0;

// Validate required fields
if (empty($business_name) || empty($business_category) || empty($city)) {
    header('Location: index.html?error=missing_fields');
    exit;
}

// Rest of your existing results.php code...
?> 