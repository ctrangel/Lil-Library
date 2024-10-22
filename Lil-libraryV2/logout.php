<?php
session_start();

// Destroy the session and log the user out
session_unset();
session_destroy();

// Return a JSON response for the SweetAlert feedback
echo json_encode(['message' => 'You have been logged out successfully.']);
exit();

?>