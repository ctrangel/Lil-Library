<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	//Check login input
	 $input = file_get_contents('php://input');
	
	if (is_string($input))
	{
		$input = json_decode($input, true);
	}
	else
	{
		echo 'Login Error';
	}
    
    $username = $input['username'];
    $password = $input['password'];

    // Prepare the API request to the Express.js login route
    $apiUrl = 'http://localhost:4000/login';
    $postData = json_encode(array(
        'username' => $username,
        'password' => $password
    ));

    // Send a POST request to the Express.js API
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen((string)$postData)
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, (string)$postData);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Decode the API response
    $response = json_decode((string)$apiResponse, true);

    if ($httpCode === 200 && isset($response['token'])) {
        // Set the session variables if login is successful
        $_SESSION['username'] = $username;
        $_SESSION['token'] = $response['token'];

        echo json_encode(array('success' => true, 'message' => 'Login successful.'));
    } else {
        // If the API returns an error
        http_response_code($httpCode);
        echo json_encode(array('success' => false, 'message' => $response['message'] ?? 'Invalid username or password.'));
    }
    // Debugging: Check if the username is set in the session
error_log("Username set in session: " . $username);
}

// Debugging: Check if the username is set in the session
//error_log("Username set in session: " . $username);

?>