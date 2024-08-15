<?php

// Replace `$token` with your actual Telegram bot token
$token = ''; // token

/**
 * Telegram bot Function
 * 
 * This function interacts with the Telegram Bot API.
 *
 * @param string $method The Telegram Bot API method (sendMessage, getUpdates, etc.)
 * @param array $data Optional. Array of parameters to send with the API request.
 * @return object|null Returns decoded JSON response from the Telegram Bot API or null on failure.
 */
function bot($method, $data = []) {
    global $token; // Assuming $token is defined somewhere globally or within the script.

    $ch = curl_init(); // Initialize cURL session.

    // Set cURL options.
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.telegram.org/bot'.$token.'/'.$method,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => 1
    ]);

    $response = curl_exec($ch); // Execute cURL session.
    
    // Check for cURL errors.
    if(curl_error($ch)) {
        error_log('Curl error: ' . curl_error($ch)); // Log cURL error if any.
        curl_close($ch); // Close cURL session.
        return null; // Return null on error.
    }
    
    curl_close($ch); // Close cURL session.

    // Decode JSON response from Telegram API.
    $decoded = json_decode($response);

    if (!$decoded) {
        error_log('Invalid JSON received from Telegram API.'); // Log JSON decode error.
        return null; // Return null if JSON decoding fails.
    }

    return $decoded; // Return decoded JSON response.
}


