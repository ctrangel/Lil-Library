<?php
session_start();
ob_start();

// Check if the user is not logged in

if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
    header('Location: landingPage.php');
    exit();
}

// Display the username from the session
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library</title>
    <link rel="stylesheet" href="dashboardStyles.css" />

</head>

<body>
    <nav class="menu">
        <div class="welcome-message">
            Welcome, <span id="username-display"><?php echo $username; ?></span>!
        </div>
        <ul class="menu-list">
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="myBooks.php">My Books</a></li>
            <li><a href="browseBooks.php">Browse</a></li>
        </ul>
        <form method="POST" action="" class="logout-form">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </nav>

    <h1>Lil Library</h1>

    <form method="GET" action="" class="search-bar">
        <input class="search-input" type="text" name="search" placeholder="Search books..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
        <button class="search-btn" type="submit">Search</button>
    </form>

    <?php
    $booksPerPage = 5;
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'default';
    $searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

    $apiUrl = 'http://localhost:4000/books';
    $booksData = file_get_contents($apiUrl);
    $booksData = json_decode($booksData, true);

    if ($searchQuery) {
        $booksData = array_filter($booksData, function ($book) use ($searchQuery) {
            return strpos(strtolower($book['title']), $searchQuery) !== false || strpos(strtolower($book['author']), $searchQuery) !== false;
        });
    }

    if ($sortBy != 'default') {
        usort($booksData, function ($a, $b) use ($sortBy) {
            return strcmp($a[$sortBy], $b[$sortBy]);
        });
    }

    $totalBooks = count($booksData);
    $totalPages = ceil($totalBooks / $booksPerPage);
    $startIndex = ($currentPage - 1) * $booksPerPage;
    $booksToShow = array_slice($booksData, $startIndex, $booksPerPage);

    if ($totalBooks === 0) {
        echo '<div class="no-results">';
        echo '<p>Nothing matches your search :(</p>';
        echo '<div class="noResults-img"> </div>';
        echo '</div>';
    }

    echo '<div id="book-container" class="book-container">';
    foreach ($booksToShow as $book) {
        echo '<div class="book-card">';
        echo '<div class="book-title">' . $book['title'] . '</div>';
        echo '<div class="book-author">' . $book['author'] . '</div>';
        echo '<div class="book-shelves"><strong>Shelves:</strong> ' . $book['shelves'] . '</div>';
        echo '<div class="book-rating"><strong>Avg Rating:</strong> ' . $book['avg_rating'] . '</div>';
        echo '<button class="delete-btn" data-id="' . $book['id'] . '">Delete</button>'; 
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="controls">';
    echo '<button id="firstBtn" ' . ($currentPage == 1 ? 'disabled' : '') . ' onclick="window.location.href=\'?page=1&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '\'">First</button>';
    echo '<div class="pagination">';
    echo '<button ' . ($currentPage == 1 ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . ($currentPage - 1) . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '\'">Previous</button>';
    echo '<span class="page-info">Page ' . $currentPage . ' of ' . $totalPages . '</span>';
    echo '<button ' . ($currentPage == $totalPages ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . ($currentPage + 1) . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '\'">Next</button>';
    echo '</div>';
    echo '<button id="lastBtn"' . ($currentPage == $totalPages ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . $totalPages . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '\'">Last</button>';
    echo '</div>';
    ?>

    <div class="controls">
        <label id="pageLabel" for="sort">Sort by:</label>
        <select id="sort" onchange="window.location.href='?page=1&sort=' + this.value + '&search=<?php echo urlencode($searchQuery); ?>';">
            <option value="default" <?php echo $sortBy == 'default' ? 'selected' : ''; ?>>Default</option>
            <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Title</option>
            <option value="author" <?php echo $sortBy == 'author' ? 'selected' : ''; ?>>Author</option>
            <option value="avg_rating" <?php echo $sortBy == 'avg_rating' ? 'selected' : ''; ?>>Avg Rating</option>
        </select>
    </div>

    <h2>Add a New Book</h2>

    <!-- Add Book Form -->
    <form method="POST" action="dashboard.php">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter book title" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" class="form-control" id="author" name="author" placeholder="Enter author name" required>
        </div>
        <div class="form-group">
            <label for="shelves">Shelves:</label>
            <input type="text" class="form-control" id="shelves" name="shelves" placeholder="Enter shelves category" required>
        </div>
        <div class="form-group">
            <label for="avg_rating">Average Rating:</label>
            <input type="number" step="0.1" class="form-control" id="avg_rating" name="avg_rating" placeholder="Enter average rating" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Book</button>
    </form>

    <?php
    // Handle book addition (POST request)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = htmlspecialchars($_POST['title']);
        $author = htmlspecialchars($_POST['author']);
        $shelves = htmlspecialchars($_POST['shelves']);
        $avg_rating = floatval($_POST['avg_rating']);

        // Data to send to the API
        $postData = [
            'title' => $title,
            'author' => $author,
            'shelves' => $shelves,
            'avg_rating' => $avg_rating
        ];

        // Set up the request to your API
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($postData),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents('http://localhost:4000/books', false, $context);

        if ($result === FALSE) {
            echo '<p>Failed to add the book.</p>';
        } else {
            echo '<p>Book added successfully!</p>';
        }
    }
    ?>

    <!-- <script>
        // Check if token exists and display username
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('authToken');
            if (!token) {
                window.location.href = 'landingPage.php'; // Redirect if no token
            } else {
                // Optionally, decode token to get username and display it
                const username = localStorage.getItem('username'); // Assuming you stored it
                document.getElementById('username-display').textContent = username;
            }

            // Handle logout
            document.getElementById('logout-btn').addEventListener('click', function() {
                localStorage.removeItem('authToken');
                localStorage.removeItem('username');
                window.location.href = 'landingPage.php';
            });
        });
    </script> -->

    <?php


    // Handle logout

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('Location: landingPage.php');
        ob_end_flush();
        exit();
    }


    ?>



</body>

</html>