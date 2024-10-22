<?php
session_start();

// Verify if POST request has the required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['username']) && isset($_POST['usertype'])) {
    $_SESSION['user_id'] = $_POST['user_id'];
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['usertype'] = $_POST['usertype'];

    // Return a JSON response to indicate the session has been set
    echo json_encode(['success' => true, 'message' => 'Session set']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to set session']);
}

?>