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

<style>
    

.controls {
  text-align: center;
  margin: 20px 0;
}

.controls button {
  background-color: #c6c8bb;
  border: none;
  padding: 10px;
  margin: 5px;
  cursor: pointer;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.controls button:hover {
  background-color: #c6c8bb73;
}

.controls button:disabled {
  background-color: #f0f0f0;
  cursor: not-allowed;
}

.controls span {
  font-size: 1rem;
  font-family: "Merriweather", serif;
  color: white;
}

.sort-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 20px 0;
  border: none;

}

.sort-controls select {
  padding: 10px;
  font-size: 1rem;
  margin: 10px;
  border: none;
  background-color: #c6c8bb;
 
}

.sort-controls select:focus {
  outline: none;
}

.sort-controls button {
  background-color: #c6c8bb;
  border: none;
  padding: 10px;
  margin: 5px;
  cursor: pointer;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.sort-controls button:hover {
  background-color: #c6c8bb73;
}

.sort-controls label {
  font-size: 1rem;
  font-family: "Merriweather", serif;
  color: white;
}

</style>

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

        body {
  font-family: "Arial", sans-serif;
  background-color: #f4f1ea;
  color: #000000;
  margin: 0;
  padding: 0;
  background-image: url("/Lil-libraryV2/media/hfn210qr.bmp"); /* update accordingly */
  background-size: cover;
}
h1 {
  text-align: center;
  font-size: 2.5rem;
  margin-top: 20px;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
  width: 50%;
  margin: 0 auto;
  margin-top: 20px;
}
.book-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  padding: 20px;
  height: auto;
}

.book-card-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  padding: 20px;
  height: auto;
}
.book-card {
  background-color: #dee0d2; /* Book cover */
  width: 200px;
  height: 250px;
  margin: 15px;
  padding: 15px;
  position: relative;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2); /* Shadow for depth */
  border-radius: 4px; /* Slight rounding for book shape */
  text-align: center;
  transform-style: preserve-3d;
  transform: perspective(1000px) rotateY(0deg);
  transition: transform 0.4s ease, box-shadow 0.4s ease;
  overflow: hidden;
  font-size: 1rem;
}



/* Spine on the left */
.book-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 30px; /* Spine width */
  height: 100%;
  background-color: #d3c7b5; /* Spine color */
  box-shadow: inset 2px 0px 8px rgba(0, 0, 0, 0.1);
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

/* Inner pages look */
.book-card::after {
  content: "";
  position: absolute;
  top: 5px;
  left: 32px;
  width: calc(100% - 35px);
  height: calc(100% - 10px);
  background: linear-gradient(
    90deg,
    #f9f9f9 10%,
    transparent 10%
  ); /* Simulates pages */
  background-size: 3px 100%;
  z-index: 0;
}

/* Title and content inside the book */
.book-title {
  font-size: 1.3rem;
  font-weight: bold;
  margin: 20px 0;
  z-index: 1;
  position: relative;
}

.book-author {
  font-size: 1rem;
  color: #555;
  z-index: 1;
  position: relative;
}

.book-shelves,
.book-rating {
  font-size: 0.9rem;
  margin-top: 5px;
  z-index: 1;
  position: relative;
}

/* Hover effect for opening book */
.book-card:hover {
  transform: perspective(1000px) rotateY(-10deg);
  box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3); /* More depth on hover */
}

.book-card:hover {
  transform: scale(1.05);
}
.book-title {
  font-size: 1.2rem;
  font-weight: bold;
  margin: 10px 0;
}
.book-author {
  font-size: 1rem;
  color: #555;
}
.book-shelves,
.book-rating {
  font-size: 0.9rem;
  margin-top: 5px;
}
.controls {
  text-align: center;
  margin: 20px 0;
}
.pagination {
  display: inline-block;
  margin: 0 10px;
}
.pagination button {
  background-color: #c6c8bb;
  border: none;
  padding: 10px;
  margin: 5px;
  cursor: pointer;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s;
}
.pagination button:hover {
  background-color: #c6c8bb73;
}
.pagination button:disabled {
  background-color: #f0f0f0;
  cursor: not-allowed;
}
select {
  padding: 10px;
  font-size: 1rem;
  margin: 10px;
}
.page-info {
  display: inline-block;
  margin-left: 20px;
  font-size: 1rem;
}

#lastBtn {
  background-color: #c6c8bb;
  border: none;
  padding: 10px;
  margin: 5px;
  cursor: pointer;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s;
}

#lastBtn:hover {
  background-color: #c6c8bb73;
}

#firstBtn {
  background-color: #c6c8bb;
  border: none;
  padding: 10px;
  margin: 5px;
  cursor: pointer;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s;
}

#firstBtn:hover {
  background-color: #c6c8bb73;
}

#pageInfo {
  display: inline-block;
  margin: 20px;
  font-size: 1rem;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
}

#sort {
  display: inline-block;
  font-size: 1rem;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
}

.page-info {
  display: inline-block;
  margin: 10px;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
}

#pageLabel {
  display: inline-block;
  margin: 10px;
  font-size: 1rem;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
}

.search-bar {
  display: flex;
  justify-content: center;
  margin: 20px;
}

.search-input {
  padding: 10px;
  font-size: 1rem;
  width: 50%;
  margin-right: 10px;
}

.search-btn {
  padding: 10px;
  font-size: 1rem;
  background-color: #c6c8bb;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.no-results {
  text-align: center;
  margin: 20px;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
  width: 50%;
  margin: 0 auto;
}
.no-results p {
  font-size: 1.5em;
  color: #666;
}

.noResults-img {
  display: block;
  margin: 0 auto;
  height: 300px;
  width: 300px;
  background-image: url("/Lil-libraryV2/media/n1att21p.bmp");
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}

/* Menu Styling */
nav.menu {
  background-color: #c6c8bb;
  padding: 10px;
  display: flex;
  justify-content: left;
  align-items: center;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 0 0 10px 10px;
}

.menu-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  gap: 15px;
}

.menu-list li {
  display: inline;
}

.menu-list a {
  text-decoration: none;
  color: #000;
  padding: 10px 15px;
  font-size: 1.2rem;
  background-color: #dee0d2;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

.menu-list a:hover {
  background-color: #b5b7a3;
}

.menu-list a.active {
  background-color: #b5b7a3;
}

.welcome-message {
  font-size: 1.2rem;
  background-color: #dee0d2;
  padding: 10px;
  border-radius: 5px;
  margin-right: 20px;
}

.logout-btn {
  background-color: #c6c8bb;
  border: none;
  padding: 10px;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
  margin-right: 0;

}

.add-header {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 20px;
    color: white;
    font-family: "merriweather", serif;
}

.main-library-book-add {
    max-width: 500px;
    margin: 0 auto;
    padding: 30px;
    background-color: #c6c8bb;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    font-family: "merriweather", serif;
    margin-bottom: 20px;
}

.main-library-book-add .form-group {
    margin-bottom: 15px;
}

.main-library-book-add .form-group label {
    font-size: 1rem;
    color: #555;
    display: block;
    margin-bottom: 5px;
}

.main-library-book-add .form-control {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

.main-library-book-add .form-control:focus {
    
    outline: none;
}

.main-library-book-add .btn-primary {
    width: 100%;
    padding: 12px;
    background-color: #443f33;
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.5s ease;
}

.main-library-book-add .btn-primary:hover {
    background-color: #f9fbfd;
    color: #443f33;
}
    </style>
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV2/styles/homeStyles.css" />
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV2/styles/browseBooks.css" />
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