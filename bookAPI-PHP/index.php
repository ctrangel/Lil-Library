<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With");

include_once 'db.php';

$database = new Database();
$db = $database->connect();

// home page
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['id']) && !isset($_GET['users'])) {
    echo json_encode(['message' => 'Welcome to the book API']);
}

// GET all books
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['id'])) {
    $query = 'SELECT * FROM books';
    $stmt = $db->prepare($query);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
}

// GET book by ID
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $query = 'SELECT * FROM books WHERE id = :id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($book) {
        echo json_encode($book);
    } else {
        echo json_encode(['message' => 'Book not found']);
    }
}

// POST a new book
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $query = 'INSERT INTO books (title, author, shelves, avg_rating) VALUES (:title, :author, :shelves, :avg_rating)';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':author', $data->author);
    $stmt->bindParam(':shelves', $data->shelves);
    $stmt->bindParam(':avg_rating', $data->avg_rating);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Book added successfully']);
    } else {
        echo json_encode(['message' => 'Error adding book']);
    }
}

// PUT to update a book by ID
if ($_SERVER['REQUEST_METHOD'] == 'PUT' && isset($_GET['id'])) {
    $data = json_decode(file_get_contents("php://input"));

    $query = 'UPDATE books SET title = :title, author = :author, shelves = :shelves, avg_rating = :avg_rating WHERE id = :id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':author', $data->author);
    $stmt->bindParam(':shelves', $data->shelves);
    $stmt->bindParam(':avg_rating', $data->avg_rating);
    $stmt->bindParam(':id', $_GET['id']);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Book updated successfully']);
    } else {
        echo json_encode(['message' => 'Error updating book']);
    }
}

// DELETE a book by ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
    $query = 'DELETE FROM books WHERE id = :id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Book deleted successfully']);
    } else {
        echo json_encode(['message' => 'Error deleting book']);
    }
}

// Users

// GET all users
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['id']) && isset($_GET['users'])) {
    $query = 'SELECT * FROM users';
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
}



// Close connection
$db = null;
?>
