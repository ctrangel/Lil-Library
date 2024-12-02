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
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchQuery = isset($_GET['search']) && trim($_GET['search']) !== '' ? strtolower(trim($_GET['search'])) : 'fiction'; // Default to 'fiction' if empty
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
$response = @file_get_contents($apiUrl); // Suppress errors to handle them manually
$booksData = (boolean)$response ? json_decode($response, true) : null;

// Check if books were found and handle errors
$totalBooks = isset($booksData['totalItems']) ? (int)$booksData['totalItems'] : 0;
$booksToShow = isset($booksData['items']) ? $booksData['items'] : [];
$totalPages = max(1, (int) ceil($totalBooks / $booksPerPage));

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Browse Books</title>
    <link rel="stylesheet" href="/Lil-libraryV3/styles/homeStyles.css" />
    <link rel="stylesheet" href="/Lil-libraryV3/styles/browseBooks.css" />
    <link rel="stylesheet" href="/Lil-libraryV3/styles/styles.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <section class="section">
        <div class="container">
            <h1 class="title has-text-centered">Browse Books</h1>

            <!-- Search Form -->
            <form method="GET" action="" class="field has-addons mb-6">
                <div class="control is-expanded">
                    <input class="input" type="text" name="search" placeholder="Search books..."
                        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
                </div>
                <div class="control">
                    <button class="button is-link" type="submit">Search</button>
                </div>
            </form>

            <?php
            if ($totalBooks === 0) {
                echo '<div class="notification is-warning has-text-centered">';
                echo '<p>No books found for your search :(</p>';
                echo '</div>';
            } else {
                echo '<div id="book-container" class="columns is-multiline is-centered">';
                foreach ($booksToShow as $book) {
                    echo '<div class="column is-one-quarter" style="max-width: 400px;">'; // Each card max width 400px
                    echo '<div class="card" style="height: 400px;">'; // Fixed height per card
                    echo '<div class="card-content">';

                    // Book Title
                    echo '<p class="title is-5">' . htmlspecialchars($book['volumeInfo']['title']) . '</p>';

                    // Book Author(s)
                    if (isset($book['volumeInfo']['authors'])) {
                        echo '<p class="subtitle is-6">Author: ' . implode(', ', $book['volumeInfo']['authors']) . '</p>';
                    }

                    // Book Cover Image
                    if (isset($book['volumeInfo']['imageLinks']['thumbnail'])) {
                        echo '<figure class="image is-128x128" style="margin: 0 auto;">';
                        echo '<img src="' . htmlspecialchars($book['volumeInfo']['imageLinks']['thumbnail']) . '" alt="Book cover">';
                        echo '</figure>';
                    }

                    // Average Rating
                    if (isset($book['volumeInfo']['averageRating'])) {
                        echo '<p class="has-text-weight-semibold">Avg Rating: ' . htmlspecialchars($book['volumeInfo']['averageRating']) . '</p>';
                    }

                    echo '</div>'; // End card-content
                    echo '</div>'; // End card
                    echo '</div>'; // End column
                }
                echo '</div>'; // End columns
            }

            // Pagination controls
            if ($totalBooks > 0) {
                echo '<nav class="pagination is-centered is-medium" role="navigation" aria-label="pagination" style="width: 70%; margin: 20px auto;">';

                // First and Previous buttons
                echo '<a class="pagination-previous" ' . ($currentPage === 1 ? 'disabled' : '') . ' href="?page=1&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '">First</a>';
                echo '<a class="pagination-previous" ' . ($currentPage === 1 ? 'disabled' : '') . ' href="?page=' . max(1, $currentPage - 1) . '&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '">Previous</a>';

                // Page number links with a range of 10 pages
                echo '<ul class="pagination-list">';
                $startPage = max(1, $currentPage - 5);
                $endPage = min($totalPages, $currentPage + 5);
                for ($i = $startPage; $i <= $endPage; $i++) {
                    echo '<li><a class="pagination-link ' . ($i === $currentPage ? 'is-current' : '') . '" href="?page=' . $i . '&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '">' . $i . '</a></li>';
                }
                echo '</ul>';

                // Next and Last buttons
                echo '<a class="pagination-next" ' . ($currentPage === $totalPages ? 'disabled' : '') . ' href="?page=' . min($totalPages, $currentPage + 1) . '&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '">Next</a>';
                echo '<a class="pagination-next" ' . ($currentPage === $totalPages ? 'disabled' : '') . ' href="?page=' . $totalPages . '&sort=' . urlencode($sortBy) . '&search=' . urlencode($searchQuery) . '">Last</a>';

                echo '</nav>';

            }
            ?>
        </div>
    </section>

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