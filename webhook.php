<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OpenAI API configuration
$OPENAI_API_KEY = 'YOUR_API_KEY_HERE'; // Replace with your actual API key
$OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';

// Log function
function logError($message, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $message;
    if ($data) {
        $log .= "\nData: " . print_r($data, true);
    }
    error_log($log . "\n", 3, "webhook_errors.log");
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get the raw POST data
$rawData = file_get_contents('php://input');
logError("Received raw data", $rawData);

$data = json_decode($rawData, true);
logError("Decoded data", $data);

// Validate the data
if (!$data) {
    logError("JSON decode error: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit();
}

if (!isset($data['data']['content'])) {
    logError("Missing content in data", $data);
    http_response_code(400);
    echo json_encode(['error' => 'Missing content field']);
    exit();
}

// Extract the content
$content = $data['data']['content'];
logError("Extracted content", $content);

// Prepare the OpenAI API request
$openaiData = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are an SEO expert specializing in local business optimization. Provide specific, actionable advice.'
        ],
        [
            'role' => 'user',
            'content' => $content
        ]
    ],
    'temperature' => 0.7,
    'max_tokens' => 500
];

// Initialize cURL session
$ch = curl_init($OPENAI_API_URL);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($openaiData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $OPENAI_API_KEY
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    logError("cURL error: " . curl_error($ch));
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to communicate with OpenAI API',
        'details' => curl_error($ch)
    ]);
    exit();
}

curl_close($ch);

// Process the OpenAI response
$openaiResponse = json_decode($response, true);

if ($httpCode !== 200 || !isset($openaiResponse['choices'][0]['message']['content'])) {
    logError("OpenAI response error", $openaiResponse);
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to get response from OpenAI',
        'details' => $openaiResponse['error']['message'] ?? 'Unknown error'
    ]);
    exit();
}

// Extract the generated advice
$advice = $openaiResponse['choices'][0]['message']['content'];

// Prepare the final response
$finalResponse = [
    'status' => 'success',
    'message' => 'Advice generated successfully',
    'data' => [
        'advice' => $advice,
        'timestamp' => date('Y-m-d H:i:s'),
        'model' => 'gpt-3.5-turbo'
    ]
];

logError("Sending response", $finalResponse);
echo json_encode($finalResponse);
?> 