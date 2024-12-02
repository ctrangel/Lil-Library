<?php
// Handle book deletion (DELETE request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookId'])) {
    $bookId = $_POST['bookId'];

    if ($bookId !== false) {
        $apiUrl = 'http://localhost:4000/books/' . $bookId;

        // Initialize a cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the DELETE request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false || $httpCode !== 200) {
            $error = curl_error($ch);
           // echo json_encode(['error' => $error ?: 'Failed to delete book']);

           if ((bool)$error) 
           {
            echo json_encode(['error' => htmlspecialchars($error, ENT_QUOTES, 'UTF-8')]);
           }
        } 
        else 
        {
            // Ensure we return the correct API response in JSON format
            header('Content-Type: application/json');
            echo $response;
        }

        curl_close($ch);
    } else {
        echo json_encode(['error' => 'Invalid book ID']);
    }
    exit;
}

// Handle invalid requests
echo json_encode(['error' => 'Invalid request']);
exit;
?>