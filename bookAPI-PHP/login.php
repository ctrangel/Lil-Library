<?php
include_once 'db.php';
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $query = 'SELECT * FROM users WHERE username = :username';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $data->username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($data->password, $user['password_hash'])) {
        echo json_encode(['message' => 'Login successful', 'user_id' => $user['id']]);
    } else {
        echo json_encode(['message' => 'Login failed']);
    }
}

?>