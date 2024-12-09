<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
    echo json_encode(['error' => 'Unauthorized access!']);
    exit;
}

// Function to call the book API
function callAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        default:
            if ($data)
                if (is_array($data) || is_object($data)) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
    }

    // Authentication and headers
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $_SESSION['token']
    ]);

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
}

// Process chat bot requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $question = strtolower(trim($input['question']));

    if (strpos($question, 'find book') !== false) {
        // Extract title or author from question if possible
        // This is a simplistic example; adjust regex as needed
        preg_match("/find book (.*)/", $question, $matches);
        $search = $matches[1] ?? '';

        $response = callAPI('GET', 'http://localhost:4000/books', ['search' => $search]);
        echo $response;
    } elseif (strpos($question, 'add book') !== false) {
        // Example adding a book
        $data = [
            'title' => 'Sample Title',
            'author' => 'Sample Author',
            'shelves' => 'Sample Shelf',
            'avg_rating' => 4.5
        ];
        $response = callAPI('POST', 'http://localhost:4000/books', $data);
        echo $response;
    } else {
        echo json_encode(['message' => 'I am not sure how to help with that.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

?>