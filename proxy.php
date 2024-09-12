<?php
$apiKey = "pub_532903b5836e44ccd9c572337a848321ecd0f";
$apiUrl = "https://newsdata.io/api/1/latest?country=jp&apikey=$apiKey";

// Initialize cURL session
$ch = curl_init();

// Set the URL of the API endpoint
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Set the User-Agent header
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'User-Agent: YourAppName/1.0'
));

// Execute the request and fetch the response
$response = curl_exec($ch);

// Check for cURL errors
if ($response === FALSE) {
    echo json_encode(array(
        "status" => "error",
        "message" => curl_error($ch)
    ));
} else {
    // Output the response from the API
    echo $response;
}

// Close cURL session
curl_close($ch);
