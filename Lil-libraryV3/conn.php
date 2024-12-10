<?php

//Supabase
/*
$host = 'aws-0-us-east-1.poller.supabase.com';
$port = 6543;
$dbname = 'postgres';
$user = 'postgres.etyhnplamagtxvxhrkqp';
$password = 'col#Page9415590';
*/

//Local Host
$host = 'localhost';
$port = '5432' ;
$dbname = 'books';
$user = 'postgres';
$password = 'GAmeP88raMD####';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e)
{
    echo 'Connection failed: ' . $e->getMessage();
}

?>