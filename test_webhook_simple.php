<?php
// Simple test data
$testData = [
    'content' => 'Test message from PHP script'
];

// Initialize cURL
$ch = curl_init('https://hook.us1.make.com/3lwen6w2qhm4d302ww6dln4308qh26uf');

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

// Create a temporary file handle for CURL debug output
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

// Get CURL debug information
rewind($verbose);
$verboseLog = stream_get_contents($verbose);

// Print results
echo "Test Results:\n";
echo "=============\n\n";

echo "Request Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT);
echo "\n\n";

echo "HTTP Status Code: " . $httpCode . "\n\n";

echo "CURL Verbose Log:\n";
echo $verboseLog;
echo "\n\n";

if ($curlError) {
    echo "CURL Error:\n";
    echo $curlError;
    echo "\n\n";
}

echo "Response:\n";
echo $response;
echo "\n\n";

// Clean up
curl_close($ch);
fclose($verbose);

// Also try a direct file_get_contents test
echo "Testing with file_get_contents:\n";
echo "=============================\n\n";

$opts = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($testData)
    ]
];

$context = stream_context_create($opts);
$result = file_get_contents('https://hook.us1.make.com/3lwen6w2qhm4d302ww6dln4308qh26uf', false, $context);

echo "file_get_contents Response:\n";
echo $result;
echo "\n\n";

// Check if cURL is enabled
echo "PHP Configuration:\n";
echo "=================\n\n";
echo "cURL enabled: " . (function_exists('curl_version') ? 'Yes' : 'No') . "\n";
if (function_exists('curl_version')) {
    echo "cURL version: " . print_r(curl_version(), true) . "\n";
}
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Yes' : 'No') . "\n";
?> 