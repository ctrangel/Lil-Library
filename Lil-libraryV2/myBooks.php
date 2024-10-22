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

?>

<!DOCTYPE html>
<html lang="en">

<style>
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

@import url("https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap");

body {
  background: #f4f1ea;
  font-family: "Merriweather", serif !important;
  background-image: url("/Lil-libraryV2/media/hfn210qr.bmp"); /* update accordingly */
  background-size: cover;
  color: #000000;
  margin: 0;
  padding: 0;
}

.intro {
  font-size: 2.5rem;
  text-align: center;
  margin-top: 20px;
  background-color: #c6c8bb;
  padding: 10px;
  border-radius: 10px;
  width: 50%;
  margin: 0 auto;
  margin-top: 20px;
}

.intro p {
  font-size: 1rem;
  margin: 0;
  color: black;
}

h2 {
  text-align: center;
  margin-top: 1rem;
  color: white;
  font-family: "Merriweather", serif !important;
}

h3 {
  text-align: center;
  margin-top: 1rem;
  color: white;
  font-family: "Merriweather", serif !important;
}

/* Search Input */
#search {
  width: 50%; /* More reasonable width */
  padding: 0.75rem;
  border: 1px solid var(--main-border-color);
  border-radius: 0.25rem;
  font-size: 1rem;
  margin: 0 auto; /* Centering */
  display: block;
}

#search:focus {
  outline: none;
  border-color: var(--main-hover-color);
  box-shadow: 0 0 5px var(--main-hover-color);
}

/* Search Results Styling */
.search-results {
  max-height: 250px;
  overflow-y: auto;
  background-color: #fff;
  border: 1px solid var(--main-border-color);
  border-radius: 0.25rem;
  z-index: 1000;
  width: 50%; /* Same width as search bar */
  margin: 0 auto; /* Centering */
  position: relative;
  display: none; /* Hidden by default */
}



.search-results div {
  padding: 10px;
  cursor: pointer;
  background-color: var(--card-bg-color);
  border-bottom: 1px solid var(--main-border-color);
}

.search-results div:hover {
  background-color: var(--main-hover-color);
}

#addBookBtn {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  background-color: var(--main-border-color);
  border: none;
  border-radius: 0.375rem;
  margin-top: 15px;
  color: #fff;
  cursor: pointer;
  display: none;
  transition: background-color 0.3s ease;
  display: block;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}

#addBookBtn:hover {
  background-color: var(--main-hover-color);
  transform: scale(1.02);
}

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

.delete-btn {
  width: 100px;
  color: black;
  padding: 10px 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

#search-bar-add-label {
  display: block;
  margin: 0 auto;
  margin-top: 20px;
  font-size: 1.5rem;
  color: white;
}

.search-add-container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    justify-content: center;
   
    height: auto;
}

#addBookForm {
  width: 50%;
  margin: 0 auto;
  padding: 20px;
  background-color: #c6c8bb;
  border-radius: 10px;
  display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

}

#addBookForm label {
  font-size: 1.5rem;
  color: black;
}

.search-custom-library {
    width: 50%;
    margin: 0 auto;
    padding: 20px;
    background-color: #c6c8bb;
    border-radius: 10px;
    display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

    margin-bottom: 20px;
}

.search-custom-library label {
  font-size: 1.5rem;
  color: black;
}

.search-custom-library input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--main-border-color);
  border-radius: 0.25rem;
  font-size: 1rem;
}

.search-custom-library input:focus {
  outline: none;
  border-color: var(--main-hover-color);
  box-shadow: 0 0 5px var(--main-hover-color);
}

.search-custom-library button {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  background-color: var(--main-border-color);
  border: none;
  border-radius: 0.375rem;
  margin-top: 15px;
  color: black;
  cursor: pointer;
  display: none;
  transition: background-color 0.3s ease;
  display: block;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}

.oh-no-no-books {
    width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-size: 1.5rem;
    color: black;
    background-color: #c6c8bb;
    padding: 20px;
    border-radius: 10px;
    margin: 0 auto;
}

</style>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Books</title>
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV2/styles/homeStyles.css" />
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV2/styles/myBooks.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <nav class="menu">
        <div class="welcome-message">
            Welcome <span id="username-display"><?php echo $username; ?></span>!
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
        echo '<p>You haven\'t added any books yet.</p>';
        echo '<div class="noResults-img"> </div>';
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