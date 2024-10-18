<?php
session_start();
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
    header('Location: landingPage.php');
    exit();
}

// Retrieve the username from the session
$username = htmlspecialchars($_SESSION['username']);

// Handle book addition (via form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $bookId = intval($_POST['book_id']);

    // Send the data to the API to save the book
    $postData = [
        'username' => $username,
        'bookId' => $bookId
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($postData),
        ],
    ];

    $context  = stream_context_create($options);
    file_get_contents('http://localhost:4000/userBooks', false, $context);

    // Redirect to avoid form resubmission
    header('Location: myBooks.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Books</title>
    <link rel="stylesheet" href="dashboardStyles.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="myBooks.css" />
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

    <h1>Welcome, <?php echo $username; ?>, to your Lil Library!</h1>
    <p>Here you can add books to your profile and view them below.</p>

    <!-- Search Form for Autocomplete -->
    <form method="POST" id="addBookForm">
        <label for="search">Search for a Book:</label>
        <input type="text" id="search" name="search" placeholder="Type to search..." autocomplete="off" />
        <div id="searchResults" class="search-results" style="display: none;"></div>
        <input type="hidden" id="book_id" name="book_id" />
        <button type="submit" style="display: none;" id="addBookBtn">Add Book</button>
    </form>

    <h2>Your Books</h2>

    <?php
    // Fetch user-specific books
    $apiUrl = 'http://localhost:4000/userBooks?username=' . urlencode($username);
    $userBooksData = file_get_contents($apiUrl);
    $userBooksData = json_decode($userBooksData, true);

    if (empty($userBooksData)) {
        echo '<p>You haven\'t added any books yet.</p>';
    } else {
        echo '<div class="book-container">';
        foreach ($userBooksData as $book) {
            echo '<div class="book-card">';
            echo '<div class="book-title">' . htmlspecialchars($book['title']) . '</div>';
            echo '<div class="book-author">' . htmlspecialchars($book['author']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    ?>

    <script>
        document.getElementById('search').addEventListener('input', function() {
            const query = this.value;
            if (query.length > 2) { // Only search if the query is at least 3 characters long
                fetch('http://localhost:4000/books')
                    .then(response => response.json())
                    .then(books => {
                        const results = books.filter(book => book.title.toLowerCase().includes(query.toLowerCase()) || book.author.toLowerCase().includes(query.toLowerCase()));
                        displayResults(results);
                    });
            } else {
                document.getElementById('searchResults').style.display = 'none';
            }
        });

        function displayResults(books) {
            const resultsContainer = document.getElementById('searchResults');
            resultsContainer.innerHTML = '';
            if (books.length > 0) {
                books.forEach(book => {
                    const resultDiv = document.createElement('div');
                    resultDiv.textContent = `${book.title} by ${book.author}`;
                    resultDiv.dataset.bookId = book.id;

                    resultDiv.addEventListener('click', function() {
                        document.getElementById('search').value = resultDiv.textContent;
                        document.getElementById('book_id').value = resultDiv.dataset.bookId;
                        document.getElementById('addBookBtn').style.display = 'block'; // Show the Add Book button
                        resultsContainer.style.display = 'none'; // Hide the search results
                    });

                    resultsContainer.appendChild(resultDiv);
                });
                resultsContainer.style.display = 'block';
            } else {
                resultsContainer.style.display = 'none';
            }
        }
    </script>
</body>

</html>