<?php
// Handle book addition (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$fgc = file_get_contents('php://input');

	if ($fgc === FALSE)
	{
		return FALSE;
	}
	
    $input = json_decode($fgc);

    if (!is_array($input)) {
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    $title = htmlspecialchars($input['title']);
    $author = htmlspecialchars($input['author']);
    $shelves = htmlspecialchars($input['shelves']);
    $avg_rating = floatval($input['avg_rating']);

    // Data to send to the API
    $postData = [
        'title' => $title,
        'author' => $author,
        'shelves' => $shelves,
        'avg_rating' => $avg_rating
    ];

    // Set up the request to your API
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($postData),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost:4000/books', false, $context);

    if ($result === FALSE) {
        echo json_encode(['error' => 'Failed to add the book']);
    } else {
        echo json_encode(['message' => 'Book added successfully']);
    }
    exit;
}
// Handle invalid requests
echo json_encode(['error' => 'Invalid request']);
exit;

?>