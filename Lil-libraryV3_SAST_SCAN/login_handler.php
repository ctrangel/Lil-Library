<?php
session_start();

// Verify if POST request has the required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['username'])) {
    $_SESSION['user_id'] = $_POST['user_id'];
    $_SESSION['username'] = $_POST['username'];

    // Return a JSON response to indicate the session has been set
    echo json_encode(['success' => true, 'message' => 'Session set']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to set session']);
}

?>