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

// Handle book deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book_id'])) {
    $bookId = intval($_POST['delete_book_id']);

    $deleteData = [
        'username' => $username,
        'bookId' => $bookId
    ];

    $deleteOptions = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($deleteData),
        ],
    ];

    $deleteContext = stream_context_create($deleteOptions);
    file_get_contents('http://localhost:4000/deleteUserBook', false, $deleteContext);

    // Redirect to avoid form resubmission
    header('Location: myBooks.php');
    exit();
}

include 'navbar.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Books</title>
    <link rel="stylesheet" href="/Lil-libraryV3/styles/homeStyles.css" />
    <link rel="stylesheet" href="/Lil-libraryV3/styles/myBooks.css" />
    <link rel="stylesheet" href="/Lil-libraryV3/styles/styles.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>


    <div class="intro">
        <h1><?php echo $username; ?>'s Lil Library!</h1>
        <p>Here you can add books to your profile and view them below.</p>
    </div>

    <!-- Search and Add Books Section -->
    <div class="search-add-container">
        <h2>Add New Books to Your Library</h2>
        <form method="POST" id="addBookForm">
            <label id="search-bar-add-label" for="search">Search for a Book to Add:</label>
            <input type="text" id="search" name="search" placeholder="Type to search..." autocomplete="off" />
            <div id="searchResults" class="search-results" style="display: none;"></div>
            <input type="hidden" id="book_id" name="book_id" />
            <button type="submit" style="display: none;" id="addBookBtn">Add Book</button>
        </form>
    </div>


    <!-- List of User's Books with Pagination and Sorting -->
    <h2>Your Books</h2>

    <?php
    // Pagination and search settings for user's books
    $booksPerPage = 5;
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'default';
    $searchQuery = isset($_GET['user_search']) ? strtolower(trim($_GET['user_search'])) : '';

    // Fetch user-specific books from the API
    $apiUrl = 'http://localhost:4000/userBooks?username=' . urlencode($username);
    $userBooksData = file_get_contents($apiUrl);
    $userBooksData = json_decode($userBooksData, true);

    // Filter books by user search query
    if ($searchQuery) {
        $userBooksData = array_filter($userBooksData, function ($book) use ($searchQuery) {
            return strpos(strtolower($book['title']), $searchQuery) !== false || strpos(strtolower($book['author']), $searchQuery) !== false;
        });
    }

    // Sort books if needed
    if ($sortBy != 'default') {
        usort($userBooksData, function ($a, $b) use ($sortBy) {
            return strcmp($a[$sortBy], $b[$sortBy]);
        });
    }

    // Pagination calculations
    $totalBooks = count($userBooksData);
    $totalPages = ceil($totalBooks / $booksPerPage);
    $startIndex = ($currentPage - 1) * $booksPerPage;
    $booksToShow = array_slice($userBooksData, $startIndex, $booksPerPage);

    if ($totalBooks === 0) {
        echo '<div class="oh-no-no-books">';
        echo '<h1 class="is-size-5">You have not added any books yet :/ </h1>';
        echo '<figure class="image" style="width: 500px; height: 500px; margin: 0 auto;">';
        echo '<img src="/Lil-libraryV3/media/noResults.png" alt="No Results" style="width: 100%; height: 100%;">';
        echo '</figure>';
        echo '</div>';
    } else {
        echo '<div id="book-container" class="book-container">';
        foreach ($booksToShow as $book) {
            echo '<div class="book-card-container">';
            echo '<div class="book-card">';
            echo '<div class="book-title">' . $book['title'] . '</div>';
            echo '<div class="book-author">' . $book['author'] . '</div>';
            echo '<div class="book-shelves"><strong>Shelves:</strong> ' . $book['shelves'] . '</div>';
            echo '<div class="book-rating"><strong>Avg Rating:</strong> ' . $book['avg_rating'] . '</div>';
            echo '</div>';
            echo '<button id="delete-btn" class="delete-btn" data-id="' . $book['id'] . '">Delete</button>';
            echo '</div>';
        }
        echo '</div>';
    }

    // Pagination Controls
    if ($totalBooks > 0) {
        echo '<div class="controls">';
        echo '<button id="firstBtn" ' . ($currentPage == 1 ? 'disabled' : '') . ' onclick="window.location.href=\'?page=1&sort=' . $sortBy . '&user_search=' . urlencode($searchQuery) . '\'">First</button>';
        echo '<div class="pagination">';
        echo '<button ' . ($currentPage == 1 ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . ($currentPage - 1) . '&sort=' . $sortBy . '&user_search=' . urlencode($searchQuery) . '\'">Previous</button>';
        echo '<span class="page-info">Page ' . $currentPage . ' of ' . $totalPages . '</span>';
        echo '<button ' . ($currentPage == $totalPages ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . ($currentPage + 1) . '&sort=' . $sortBy . '&user_search=' . urlencode($searchQuery) . '\'">Next</button>';
        echo '</div>';
        echo '<button id="lastBtn"' . ($currentPage == $totalPages ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . $totalPages . '&sort=' . $sortBy . '&user_search=' . urlencode($searchQuery) . '\'">Last</button>';
        echo '</div>';
    }
    ?>
    <!-- Sort Controls -->
    <div class="controls">
        <label id="pageLabel" for="sort">Sort by:</label>
        <select id="sort" onchange="window.location.href='?page=1&sort=' + this.value + '&user_search=<?php echo urlencode($searchQuery); ?>';">
            <option value="default" <?php echo $sortBy == 'default' ? 'selected' : ''; ?>>Default</option>
            <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Title</option>
            <option value="author" <?php echo $sortBy == 'author' ? 'selected' : ''; ?>>Author</option>
        </select>
    </div>
    <!-- Search Bar for Your Books -->
    <h3>Search Within Your Books</h3>
    <form class="search-custom-library" method="GET" action="myBooks.php">
        <input type="text" name="user_search" placeholder="Search your books..." value="<?php echo htmlspecialchars($searchQuery); ?>" />
        <button type="submit">Search</button>
    </form>



    <script>
        // Search for books to add (Autocomplete)
        document.getElementById('search').addEventListener('input', function() {
            const query = this.value;
            if (query.length > 1) { // Only search if the query is at least 2 characters long
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

        // SweetAlert confirmation for logout
        document.querySelector('.logout-btn').addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to log out?',
                text: "You will need to log back in to access your account.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#452f24',
                cancelButtonColor: '#851d1e',
                confirmButtonText: 'Yes, log me out'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('logout.php', {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Logged Out!', data.message, 'success').then(() => {
                                window.location.href = 'landingPage.php';
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                            console.error('Error:', error);
                        });
                }
            });
        });
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.getAttribute('data-id');

                // SweetAlert for confirmation
                Swal.fire({
                    title: 'Are you completely, positively, 100% sure you are sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#452f24',
                    cancelButtonColor: '#851d1e',
                    confirmButtonText: 'Yes, begone with it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with deletion if confirmed
                        const formData = new FormData();
                        formData.append('bookId', bookId);

                        for (let [key, value] of formData.entries()) {
                            console.log(`${key}: ${value}`);
                        }

                        fetch('deleteBook.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.message) {
                                    // SweetAlert success message
                                    Swal.fire(
                                        'Deleted!',
                                        'Your book has been deleted.',
                                        'success'
                                    );
                                    this.parentElement.remove(); // Remove the book card from UI
                                    // Reload the page to reflect the changes
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    // SweetAlert error message
                                    Swal.fire(
                                        'Error!',
                                        data.error || 'Failed to delete the book',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                                console.error('Error:', error);
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>