<?php

// Require the 'EzTG.php' file from the 'EzTG' directory under SOURCE_PATH
require_once '../EzTG/PHP/Standard/EzTG.php';

// Main loop to continuously handle updates from Telegram
while (true) {
    // Define a callback function to handle updates received from Telegram
    $callback = function($update, $bot) {
        if ($update) {
            // Debugging to see what $update contains
            var_dump($update);

            // Initialize cURL session
            $ch = curl_init();
            
            // Set cURL options
            curl_setopt_array($ch, [
                CURLOPT_URL => 'http://localhost/telegram/tiktokUsers/src/main.php',
                CURLOPT_POST => 1,
                // CURLOPT_TIMEOUT => 1,
                CURLOPT_POSTFIELDS => json_encode($update), // Ensure $update is properly encoded
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            ]);

            // Execute cURL request and capture response
            $response = curl_exec($ch);
            echo $response;
            
            // Check for errors during cURL execution
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }

            // Close cURL session
            curl_close($ch);

            // Return cURL response or handle further as needed
            return $response;
        }

        // If $update is empty or false, handle appropriately or return null
        return null;
    };
    
    // Replace `$token` with your actual Telegram bot token
    $token = ''; // token

    // Create EzTG instance with specified options
    $bot = new EzTG([
        'token' => $token,
        'callback' => $callback,
        'objects' => false,
        'allow_only_telegram' => true,
        'throw_telegram_errors' => true,
        'magic_json_payload' => false
    ]);

    // This loop will continuously listen for updates from Telegram and handle them via the defined callback
    // Consider adding sleep() or other logic to control the rate of polling and prevent excessive API requests
}
