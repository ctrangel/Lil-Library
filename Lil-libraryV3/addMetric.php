<?php
// Handle book addition (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    $id = htmlspecialchars($input['id']);
    $read_time = htmlspecialchars($input['read_time']);
    $page_length = htmlspecialchars($input['page_length']);
    $book_rating = floatval($input['book_rating']);

    // Data to send to the API
    $postData = [
        'id' => $id,
        'read_time' => $read_time,
        'page_length' => $page_length,
        'book_rating' => $book_rating
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
    $result = file_get_contents('http://localhost:4000/book_metrics', false, $context);

    if ($result === FALSE) {
        echo json_encode(['error' => 'Failed to add book metrics']);
    } else {
        echo json_encode(['message' => 'Metrics added successfully']);
    }
    exit;
}

// Handle invalid requests
echo json_encode(['error' => 'Invalid request']);
exit;

?>