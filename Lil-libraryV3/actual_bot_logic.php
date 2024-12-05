<?php
//Database Connection

$host = "localhost";
$port = "5432";
$dbname = "questions_answers";
$password = "GAmeP88raMD####";
$username = "postgres";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

if (isset($_POST['user_input']))
{
    $user_input = trim($_POST['user_input']);
    $stmt = $pdo->prepare("SELECT answers FROM questions_answers WHERE questions ILIKE :questions");
    $stmt->execute([':questions' => '%' . $user_input . '%']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //Cond Stmt
    if ($result)
    {
        echo $result['answer'];
    }
    else
    {
        $audioFile = "/audio/metalocalypse-censor.mp3";
        echo '<audio autoplay="true" style="display:none;">  <source src="'.$myAudioFile.'" type="audio/wav">
      </audio>';
        echo 'What the $%&* are you talking about!?!?"';
    }
}
?>