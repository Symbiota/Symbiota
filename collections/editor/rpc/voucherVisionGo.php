<?php
// Pass-through API endpoint for Vouchervision-Go OCR and Transcription
// This allows the Vouchervision-Go API key to remain private

include_once('../../../config/symbini.php');

// Get the JSON data passed to this page, we'll just pass it on
$jsonData = file_get_contents('php://input');

// Set up the API request
$curl_req = curl_init($VOUCHERVISION_API_URL);
curl_setopt($curl_req, CURLOPT_POST, true);
curl_setopt($curl_req, CURLOPT_RETURNTRANSFER, true);

// Add headers to the request, including API key
curl_setopt($curl_req, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $VOUCHERVISION_API_KEY,
    'Content-Type: application/json'
]);

// Add our JSON data to the POST body
curl_setopt($curl_req, CURLOPT_POSTFIELDS, $jsonData);

// Query the API and collect the response
$response = curl_exec($curl_req);
$statusCode = curl_getinfo($curl_req, CURLINFO_HTTP_CODE);
curl_close($curl_req);

// Return the API response, with the status code and the appropriate headers
http_response_code($statusCode);
header('Content-Type: application/json');
echo $response;

?>