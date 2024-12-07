<?php

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Fetching Metrics
$sql = "SELECT id, read_time, page_length, book_rating FROM book_metrics ORDER BY id DESC";
$stmt = $pdo->query($sql);
$metrics = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Return data in JSON
echo json_encode($metrics);


} catch (PDOException $e)
{
    echo 'Connection failed: ' . $e->getMessage();
    exit(); //Stops if connection fails
}