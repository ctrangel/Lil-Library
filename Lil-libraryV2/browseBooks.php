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

// Define constants
$booksPerPage = 5;
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : 'fiction'; // Default search query if no input
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
$startIndex = ($currentPage - 1) * $booksPerPage;

// Google Books API sorting options
$sortOptions = [
    'relevance' => 'relevance',
    'title' => 'intitle',
    'author' => 'inauthor',
    'rating' => 'rating'
];

// Build API URL with sorting
$apiUrl = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($searchQuery) . "&startIndex=" . $startIndex . "&maxResults=" . $booksPerPage . "&orderBy=" . $sortBy;
$response = file_get_contents($apiUrl);
$booksData = json_decode($response, true);

// Check if books were found
if (!isset($booksData['items'])) {
    $totalBooks = 0;
} else {
    $totalBooks = $booksData['totalItems'];
    $booksToShow = $booksData['items'];
}

$totalPages = ceil($totalBooks / $booksPerPage);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Browse Books</title>
    <style>
        .book-card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            padding: 20px;
            height: auto;
        }

        .book-card {
            margin: 10px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .book-card img {
            max-width: 100%;
            height: auto;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination button {
            padding: 10px 15px;
            margin: 0 5px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
    <link rel="stylesheet" href="/Lil-libraryV2/styles/homeStyles.css" />
    <link rel="stylesheet" href="/Lil-libraryV2/styles/browseBooks.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <form class="logout-form">
            <button type="button" name="logout" class="logout-btn">Logout</button>
        </form>
    </nav>

    <h1>Browse Books</h1>

    <form method="GET" action="" class="search-bar">
        <input class="search-input" type="text" name="search" placeholder="Search books..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
        <button class="search-btn" type="submit">Search</button>
    </form>

   

    <?php
    if ($totalBooks === 0) {
        echo '<div class="no-results">';
        echo '<p>No books found for your search :(</p>';
        echo '</div>';
    } else {
        echo '<div id="book-container" class="book-container">';
        foreach ($booksToShow as $book) {
            echo '<div class="book-card-container">';
            echo '<div class="book-card">';
            echo '<div class="book-title"><strong>' . $book['volumeInfo']['title'] . '</strong></div>';
            if (isset($book['volumeInfo']['authors'])) {
                echo '<div class="book-author">Author: ' . implode(', ', $book['volumeInfo']['authors']) . '</div>';
            }
            if (isset($book['volumeInfo']['imageLinks']['thumbnail'])) {
                echo '<img src="' . $book['volumeInfo']['imageLinks']['thumbnail'] . '" alt="Book cover">';
            }
            if (isset($book['volumeInfo']['averageRating'])) {
                echo '<div class="book-rating"><strong>Avg Rating:</strong> ' . $book['volumeInfo']['averageRating'] . '</div>';
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    // Pagination controls
    if ($totalBooks > 0) {
        echo '<div class="controls">';
        echo '<button ' . ($currentPage == 1 ? 'disabled' : '') . ' onclick="window.location.href=\'?page=1&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '\'">First</button>';
        echo '<button ' . ($currentPage == 1 ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . ($currentPage - 1) . '&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '\'">Previous</button>';
        echo '<span>Page ' . $currentPage . ' of ' . $totalPages . '</span>';
        echo '<button ' . ($currentPage == $totalPages ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . ($currentPage + 1) . '&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '\'">Next</button>';
        echo '<button ' . ($currentPage == $totalPages ? 'disabled' : '') . ' onclick="window.location.href=\'?page=' . $totalPages . '&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '\'">Last</button>';
        echo '</div>';
    }
    ?>

    <script>
        document.querySelector('.logout-btn').addEventListener('click', function(event) {
            event.preventDefault();

            // SweetAlert confirmation for logout
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
                            Swal.fire(
                                'Logged Out!',
                                data.message,
                                'success'
                            ).then(() => {
                                // Redirect to the landing page after logout
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
    </script>

</body>

</html>