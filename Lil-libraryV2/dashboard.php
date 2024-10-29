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
<script src="./javascript/newsPopUp.js"></script>

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

.box-popup 
{
  background:white;
  text-align: center;
  animation:forwards;#452f24;
  width: 550px;

}

.container {
  dispaly: flex;
  flex-direction:column-reverse;
}

.controls {
  text-align: center;
  margin: 20px 0;
}

.form-input
{
  padding: 10px;
  margin-bttom: 20px;
  border: 1px solid #d3c7b5;
  border-radius: 8px;
  font-size: 20px;
  width: 100%;
  box-sizing: border-box;
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

.news-btn {

  padding: 10px;
  font-size: 1rem;
  background-color: limegreen;
  right: 100px; 
  position: fixed;
  padding: 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
  
}

.newsform-container
{
  display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
}

.newsform-container.show{
  display: flex;
  opacity: 1;
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

.newsletterinfo
{
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100px;
  height: 100%;
  justify-content: center;
  align-items: center;
}

.newsletterinfo.show
{
  display: flex;
  opacity: 1;
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



        /*
        .btn-open-popup:hover {
            background-color: #4caf50;
        }
            */

        .overlay-container {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;
        }

        .popup-box {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
            width: 320px;
            text-align: center;
            opacity: 0;
            transform: scale(0.8);
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .form-container {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            margin-bottom: 10px;
            font-size: 16px;
            color: #444;
            text-align: left;
        }

        .form-input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-submit,
        .close {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-submit {
            background-color: green;
            color: #fff;
        }

        .close {
            position:fixed;
            top:10px;
            right:10px;
            background-color: grey;
            color: white;
        }

        .btn-submit:hover,
        .close:hover {
            background-color: #4caf50;
        }

        /* Animations */
       
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .overlay-container.show {
            display: flex;
            opacity: 1;
        }


    </style>
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV2//styles/homeStyles.css" /> <!-- Update the path accordingly -->
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

    <button class="news-btn" onclick="togglePopup()">SignUp For Newsletter</button>


    <div id="popupOverlay" 
         class="overlay-container">
        <div class="popup-box">
            <h2 style="color: green;">Enter Email to Receive Newsletter Forms</h2>
            <form class="form-container" method="post" action = "emailScript.php">

                <label class="form-label"  for="email">Email:</label>
                <input class="form-input"
                       type="email" 
                       id="email" 
                       name="email" required>

                <button class="btn-submit" 
                        type="submit">
                  Sign Up
                  </button>
            </form>

            <button class="close" 
                    onclick="togglePopup()">
              X
              </button>
        </div>
    </div>
  


  </div>

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