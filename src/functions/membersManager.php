<?php

/**
 * Checks if a given user ID exists in the users JSON file.
 *
 * @param string $id The user ID to check.
 * @return bool True if the user is a member, false otherwise.
 */
function isMember($id) {
    // Decode the JSON file into an associative array
    $users = json_decode(file_get_contents('./DB/users.json'), true);

    // Loop through each user in the users array
    foreach ($users as $user) {
        // Check if the current user's ID matches the provided ID
        if ($user['id'] == $id) {
            // If a match is found, return true
            return true;
        }
    }
    // If no match is found after looping through all users, return false
    return false;
}

/**
 * Adds a new user to the users JSON file.
 *
 * @param string $id The user ID to add.
 */
function addNewUser($id)
{
    // Decode the JSON file into an associative array
    $users = json_decode(file_get_contents('./DB/users.json'), true);

    // Create a new user array with the provided ID, current date/time, and default plan
    $user = [
        'id' => $id,
        'date' => date('Y-m-d h:i:sa', time()), // Get the current date and time
        'plan' => 'free' // Set the default plan to 'free'
    ];

    // Add the new user to the users array
    $users[] = $user;

    // Encode the updated users array back to JSON format
    $jsonData = json_encode($users, JSON_PRETTY_PRINT);

    // Save the updated JSON data back to the file
    file_put_contents('./DB/users.json', $jsonData);
}