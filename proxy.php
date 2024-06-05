<?php
$apiKey = "82e5de587dec491794fe3291d88c9ef5";
$apiUrl = "https://newsapi.org/v2/top-headlines?country=jp&apiKey=$apiKey";

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
