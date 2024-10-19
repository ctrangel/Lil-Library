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

        .delete-btn {
            width: 100px;
            color: black;
            padding: 10px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;

        }
    </style>
    <link rel="stylesheet" href="/Lil-libraryV2/styles/homeStyles.css" /> <!-- Update the path accordingly -->
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

    <h2 class="add-header" >Add a New Book</h2>

    <!-- Add Book Form -->
    <form class="main-library-book-add" id="addBookForm">
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


    <script>
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
        document.getElementById('addBookForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Get the form data
            const formData = new FormData(this);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            // Send data to addBook.php via AJAX
            fetch('addBook.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire('Error!', data.error, 'error');
                    } else {
                        Swal.fire('Success!', 'Book added successfully!', 'success');
                        //reload page
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                    console.error('Error:', error);
                });
        });
        document.querySelector('.logout-btn').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission

            // SweetAlert confirmation for logout
            Swal.fire({
                title: 'Are you sure you want to log out?',
                text: "You will need to log back in to access your account. Remember your password! We don't want to deal with allat!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#452f24',
                cancelButtonColor: '#851d1e',
                confirmButtonText: 'Yes, log me out'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with logout if confirmed
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





</body>

</html>