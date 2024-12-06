<?php

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Fetching Metrics
$sql = "SELECT metric_read_time, metric_page_length, metric_book_rating FROM bookmetrics ORDER BY created_AT DESC";
$stmt = $pdo->query($sql);
$metrics = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Return data in JSON
echo json_encode($metrics);


} catch (PDOException $e)
{
    echo 'Connection failed: ' . $e->getMessage();
    exit(); //Stops if connection fails
}