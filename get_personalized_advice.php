<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = ['city', 'businessCategory', 'gbpStatus', 'reviewCount'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Missing required fields',
        'missingFields' => $missingFields
    ]);
    exit;
}

// Extract data
$city = $data['city'];
$businessCategory = $data['businessCategory'];
$gbpStatus = $data['gbpStatus'];
$reviewCount = (int)$data['reviewCount'];

// Get city population data
$citySize = getCitySize($city);

// Prepare data for Make.com webhook
$webhookData = [
    'businessInfo' => [
        'city' => $city,
        'businessCategory' => $businessCategory,
        'gbpStatus' => $gbpStatus,
        'reviewCount' => $reviewCount,
        'citySize' => $citySize
    ],
    'prompt' => "Generate personalized local SEO advice for a {$businessCategory} business in {$city} (population: {$citySize}). " .
               "The business has " . ($gbpStatus === 'yes' ? 'claimed' : 'not claimed') . " their Google Business Profile " .
               "and has {$reviewCount} reviews. " .
               "Provide specific, actionable advice that considers the local market size and competition.",
    'timestamp' => date('c'),
    'requestId' => uniqid('req_', true)
];

// Send data to Make.com webhook
$ch = curl_init('https://hook.us1.make.com/3lwen6w2qhm4d302ww6dln4308qh26uf');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode([
        'success' => true,
        'data' => $webhookData
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to send data to webhook',
        'details' => [
            'webhookStatusCode' => $httpCode,
            'timestamp' => date('c'),
            'requestId' => $webhookData['requestId']
        ]
    ]);
}

// Helper function to get city size (placeholder)
function getCitySize($city) {
    // This should be replaced with actual city population data
    // You could use a database or API like:
    // - Census data
    // - City population API
    // - Local business directory API
    
    // For now, return a placeholder
    return "medium-sized";
}
?> 