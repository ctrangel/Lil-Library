<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        .welcome-message {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.2rem;
            background-color: #c6c8bb;
            padding: 10px;
            border-radius: 5px;
        }

        .logout-btn {
            padding: 8px 12px;
            font-size: 1rem;
            background-color: #c6c8bb;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-left: 20px;
        }

        .logout-btn:hover {
            background-color: #b5b7a3;
        }
    </style>
</head>

<body>
    <div class="welcome-message">
        Welcome, <span id="username-display">User</span>!
        <button id="logout-btn" class="logout-btn">Logout</button>
    </div>

    <h1>Lil Library</h1>

    <!--  Adding Book Data to the Lil-Library -->
     <form method="POST" action="" class="add-form">
        <label for ="id">ID: </label> <br>
        <input type = "text" class="add-form" id = "id" name="id"> <br><br>
        <label for ="read_time">Read Time: </label> <br>
        <input type = "text" class ="add-form" id = "read_time" name="read_time"> <br><br>
        <label for ="shelves">Page Length: </label> <br>
        <input type = "text" class = "add-form" id ="page_length" name="page_length"> <br><br>
        <label for ="average-rating">Avg. Rating: </label> <br>
        <input type = "text" class = "add-form" id = "average-rating" name="average-rating"> <br><br>

        <button class="add-btn" type="submit">Add Metrics</button>
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

    ?>

    <script>
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

    </script>

</body>

</html>