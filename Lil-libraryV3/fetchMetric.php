<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library</title>
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV3/styles/styles.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="fetchMetricData.js"></script>

</head>



<?php

include 'conn.php';

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