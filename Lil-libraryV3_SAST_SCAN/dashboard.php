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

// @phpstan-ignore include.fileNotFound
include 'navbar.php';
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library</title>
    <link rel="stylesheet" href="/Lil-libraryV3/styles/styles.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <section class="section">
        <container class="container">
            <div class="container">
                <!-- Edit Modal -->
                <div id="editBookModal" class="modal">
                    <div class="modal-background"></div>
                    <div class="modal-card">
                        <header class="modal-card-head">
                            <p class="modal-card-title">Edit Book</p>
                            <button class="delete" aria-label="close" onclick="closeEditModal()"></button>
                        </header>
                        <section class="modal-card-body">
                            <form id="editBookForm">
                                <input type="hidden" id="editBookId">
                                <div class="field">
                                    <label class="label">Title</label>
                                    <div class="control">
                                        <input class="input" type="text" id="editTitle" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Author</label>
                                    <div class="control">
                                        <input class="input" type="text" id="editAuthor" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Shelves</label>
                                    <div class="control">
                                        <input class="input" type="text" id="editShelves" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Avg Rating</label>
                                    <div class="control">
                                        <input class="input" type="number" step="0.1" id="editAvgRating" required>
                                    </div>
                                </div>
                            </form>
                        </section>
                        <footer class="modal-card-foot">
                            <button class="button is-success" onclick="submitEditForm()">Save changes</button>
                            <button class="button" onclick="closeEditModal()">Cancel</button>
                        </footer>
                    </div>
                </div>
            </div>
            <div class="columns is-centered">

                <div class="column is-half">
                    <h1 class="title">Lil Library</h1>
                    <form method="GET" action="" class="field has-addons">
                        <div class="control is-expanded">
                            <input class="input" type="text" name="search" placeholder="Search books..."
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                        </div>
                        <div class="control">
                            <button class="button is-link" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>





            <?php
            $booksPerPage = 5;
            $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'default';
            $searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

            $apiUrl = 'http://localhost:4000/books';
            $booksData = file_get_contents($apiUrl);

            //Check if book data is a valid string

            if (is_string($booksData))
			{
				$booksData = json_decode($booksData, true);
			}
			else
			{
				echo 'Error: Invalid JSON Data';
			}
			

            if ((boolean)$searchQuery) {
                $booksData = array_filter($booksData, function ($book) use ($searchQuery) {
                    return strpos(strtolower($book['title']), $searchQuery) !== false || strpos(strtolower($book['author']), $searchQuery) !== false;
                });
            }

            if ($sortBy !== 'default') {
                usort($booksData, function ($a, $b) use ($sortBy) {
                    return strcmp($a[$sortBy], $b[$sortBy]);
                });
            }

            $totalBooks = count($booksData);
            $totalPages = ceil($totalBooks / $booksPerPage);
            $startIndex = ($currentPage - 1) * $booksPerPage;
            $booksToShow = array_slice($booksData, $startIndex, $booksPerPage);

            if ($totalBooks === 0) {
                echo '<div class="box has-text-centered">';
                echo '<h1 class="is-size-5">No Results :(</h1>';
                echo '<figure class="image" style="width: 500px; height: 500px; margin: 0 auto;">';
                echo '<img src="/Lil-libraryV3/media/noResults.png" alt="No Results" style="width: 100%; height: 100%;">';
                echo '</figure>';
                echo '</div>';
            }


            // Card container with 5 cards horizontally
            echo '<div class="columns is-multiline is-centered">';
            foreach ($booksToShow as $book) {
                echo '<div class="column" style="flex: 1 0 20%; max-width: 500px;">';
                echo '<div class="box" style="height: 400px;">';
                echo '<div class="content"><strong>Title:</strong> ' . htmlspecialchars($book['title']) . '</div>';
                echo '<div class="content"><strong>Author:</strong> ' . htmlspecialchars($book['author']) . '</div>';
                echo '<div class="content"><strong>Shelves:</strong> ' . htmlspecialchars($book['shelves']) . '</div>';
                echo '<div class="content"><strong>Avg Rating:</strong> ' . htmlspecialchars($book['avg_rating']) . '</div>';
                echo '</div>';
                echo '<footer class="card-footer">';
                echo '<button class="button is-primary" onclick="openEditModal(' . htmlspecialchars($book['id']) . ')">Edit</button>';
                echo '<button class="button" id="delete-btn" data-id="' . htmlspecialchars($book['id']) . '">Delete</button>';
                echo '</footer>';
                echo '</div>';
            }
            echo '</div>';

            echo '<nav class="pagination is-centered" role="navigation" aria-label="pagination" style="justify-content: center;">';
            echo '<div style="display: flex; align-items: center; gap: 1em;">';
            echo '<a class="pagination-previous" ' . ($currentPage === 1 ? 'disabled' : '') . ' href="?page=1&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">First</a>';
            echo '<a class="pagination-previous" ' . ($currentPage === 1 ? 'disabled' : '') . ' href="?page=' . ($currentPage - 1) . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">Previous</a>';

            echo '<ul class="pagination-list" style="display: flex; gap: 0.5em;">';

            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            for ($i = $startPage; $i <= $endPage; $i++) {
                echo '<li><a class="pagination-link ' . ($i === $currentPage ? 'is-current' : '') . '" href="?page=' . $i . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">' . $i . '</a></li>';
            }

            echo '</ul>';

            echo '<a class="pagination-next" ' . ((float)$currentPage === $totalPages ? 'disabled' : '') . ' href="?page=' . ($currentPage + 1) . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">Next</a>';
            echo '<a class="pagination-next" ' . ((float)$currentPage === $totalPages ? 'disabled' : '') . ' href="?page=' . $totalPages . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">Last</a>';
            echo '</div>';
            echo '</nav>';

            // "Sort by" dropdown right below pagination
            echo '<div class="field mt-5 has-text-centered">';
            echo '<label id="pageLabel" for="sort">Sort by:</label>';
            echo '<div class="control">';
            echo '<div class="select">';
            echo '<select id="sort" onchange="window.location.href=\'?page=1&sort=\' + this.value + \'&search=' . urlencode($searchQuery) . '\';">';
            echo '<option value="default" ' . ($sortBy === 'default' ? 'selected' : '') . '>Default</option>';
            echo '<option value="title" ' . ($sortBy === 'title' ? 'selected' : '') . '>Title</option>';
            echo '<option value="author" ' . ($sortBy === 'author' ? 'selected' : '') . '>Author</option>';
            echo '<option value="avg_rating" ' . ($sortBy === 'avg_rating' ? 'selected' : '') . '>Avg Rating</option>';
            echo '</select>';
            echo '</div>';
            echo '</div>';
            echo '</div>';




            ?>




            <!-- Add Book Section -->
            <section class="section">
                <div class="container">
                    <div class="columns is-centered">
                        <div class="column is-half">
                            <h2 class="title is-4 add-header has-text-centered">Add a New Book</h2>

                            <!-- Add Book Form -->
                            <form class="box main-library-book-add" id="addBookForm">
                                <div class="field">
                                    <label class="label" for="title">Title</label>
                                    <div class="control">
                                        <input type="text" class="input" id="title" name="title" placeholder="Enter book title" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="author">Author</label>
                                    <div class="control">
                                        <input type="text" class="input" id="author" name="author" placeholder="Enter author name" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="shelves">Shelves</label>
                                    <div class="control">
                                        <input type="text" class="input" id="shelves" name="shelves" placeholder="Enter shelves category" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="avg_rating">Average Rating</label>
                                    <div class="control">
                                        <input type="number" step="0.1" class="input" id="avg_rating" name="avg_rating" placeholder="Enter average rating" required>
                                    </div>
                                </div>

                                <div class="field is-grouped is-grouped-centered">
                                    <div class="control">
                                        <button type="submit" class="button is-primary">Add Book</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>


        </container>
    </section>

    <script>
        document.querySelectorAll('#delete-btn').forEach(button => {
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

        // Modal functionality
        async function openEditModal(bookId) {
            // Fetch book data
            const response = await fetch(`http://localhost:4000/books/${bookId}`);
            const book = await response.json();

            // Fill form fields with data
            document.getElementById("editBookId").value = bookId;
            document.getElementById("editTitle").value = book.title;
            document.getElementById("editAuthor").value = book.author;
            document.getElementById("editShelves").value = book.shelves;
            document.getElementById("editAvgRating").value = book.avg_rating;

            // Show modal
            document.getElementById("editBookModal").classList.add("is-active");
        }

        function closeEditModal() {
            document.getElementById("editBookModal").classList.remove("is-active");
        }

        async function submitEditForm() {
            const bookId = document.getElementById("editBookId").value;
            const title = document.getElementById("editTitle").value;
            const author = document.getElementById("editAuthor").value;
            const shelves = document.getElementById("editShelves").value;
            const avgRating = document.getElementById("editAvgRating").value;

            try {
                // Send the updated data to editBook.php
                const response = await fetch("editBook.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id: bookId,
                        title: title,
                        author: author,
                        shelves: shelves,
                        avg_rating: avgRating
                    })
                });

                const result = await response.json();

                if (result.error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.error
                    });
                } else {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Book updated successfully."
                    }).then(() => {
                        closeEditModal();
                        window.location.reload();
                    });
                }
            } catch (error) {
                console.error("Error updating book:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Something went wrong while updating the book."
                });
            }
        }


        // Nav bar functionality
        document.addEventListener('DOMContentLoaded', () => {
            const navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

            if (navbarBurgers.length > 0) {
                navbarBurgers.forEach(el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);
                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        });
    </script>

</body>

</html>