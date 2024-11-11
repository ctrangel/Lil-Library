<?php
session_start();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from AJAX request

	$userInput = file_get_contents("php://input");

	//Validate JSON Data
	if ((bool)$userInput)
	{
		var_dump($userInput);
	}
	else
	{
		//PHP Equivalent to Exit
		die('No Valid Input');
	}
	
    $data = json_decode($userInput, true);
    $bookId = isset($data['id']) ? intval($data['id']) : 0;
    $title = $data['title'];
    $author = $data['author'];
    $shelves = $data['shelves'];
    $avg_rating = $data['avg_rating'];

    // Ensure the book ID and data are valid
    if ($bookId === 0 || isset($title) || isset($author) || isset($shelves) || isset($avg_rating)) {
        echo json_encode(["error" => "Invalid data"]);
        exit();
    }

    // Send the PUT request to the server API
    $apiUrl = 'http://localhost:4000/books/' . $bookId;
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response; // Echo API response back to AJAX request
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

?>