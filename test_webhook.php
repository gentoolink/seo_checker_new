<?php
// Test data
$testData = [
    'content' => "Generate personalized local SEO advice for a restaurant business in Vancouver (population: medium-sized). " .
                "The business has claimed their Google Business Profile and has 5 reviews. " .
                "Provide specific, actionable advice that considers the local market size and competition.",
    'businessInfo' => [
        'city' => 'Vancouver',
        'businessCategory' => 'restaurant',
        'gbpStatus' => 'yes',
        'reviewCount' => 5,
        'citySize' => 'medium-sized'
    ],
    'timestamp' => date('c'),
    'requestId' => uniqid('req_', true)
];

// Initialize cURL
$ch = curl_init('https://hook.us1.make.com/3lwen6w2qhm4d302ww6dln4308qh26uf');

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
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

echo "Raw JSON being sent:\n";
echo json_encode($testData);
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
?> 