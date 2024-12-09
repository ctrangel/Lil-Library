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

include 'navbar.php';
include 'actual_bot_logic.php';
?>



<!DOCTYPE html>
<html lang="en">

<style>
    body {
        font-family: Arial, sans-serif;
    }

    * {
        box-sizing: border-box;
    }


    .chat-popup {
        display: none;
        position: fixed;
        bottom: 0;
        right: 15px;
        border: 3px solid #f1f1f1;
        z-index: 9;
    }


    .form-container {
        max-width: 300px;
        padding: 10px;
        background-color: gray;
    }


    .form-container textarea {
        width: 100%;
        padding: 15px;
        margin: 5px 0 22px 0;
        border: none;
        background: #f1f1f1;
        resize: none;
        min-height: 200px;
    }


    .form-container textarea:focus {
        background-color: #ddd;
        outline: none;
    }

    /* Submit Button Style */
    .form-container .btn {
        background-color: #00ebc7;
        color: white;
        padding: 16px 20px;
        border: none;
        cursor: pointer;
        width: 100%;
        margin-bottom: 10px;
        opacity: 0.8;
    }

    .dark-light-toggle .btnToggle {
        background-color: rgba(0, 0, 0, 0.5);
        width: 10px;
        height: 10px;
        font-size: 10px;
        display: inline;
        margin: 0 auto;
        padding: 2px;
    }

    .btnClose {
        margin-left: 250px;
    }

    .dark-mode {
        background-color: 14161a;
        color: red;
    }

    .light-mode {
        background-color: #808080;
        color: blue;
    }
</style>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library</title>
    <link rel="stylesheet" href="Lil-Library/Lil-libraryV3/styles/styles.css" />
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
            echo '<a class="pagination-previous" ' . ($currentPage == 1 ? 'disabled' : '') . ' href="?page=1&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">First</a>';
            echo '<a class="pagination-previous" ' . ($currentPage == 1 ? 'disabled' : '') . ' href="?page=' . ($currentPage - 1) . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">Previous</a>';

            echo '<ul class="pagination-list" style="display: flex; gap: 0.5em;">';

            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            for ($i = $startPage; $i <= $endPage; $i++) {
                echo '<li><a class="pagination-link ' . ($i == $currentPage ? 'is-current' : '') . '" href="?page=' . $i . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">' . $i . '</a></li>';
            }

            echo '</ul>';

            echo '<a class="pagination-next" ' . ($currentPage == $totalPages ? 'disabled' : '') . ' href="?page=' . ($currentPage + 1) . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">Next</a>';
            echo '<a class="pagination-next" ' . ($currentPage == $totalPages ? 'disabled' : '') . ' href="?page=' . $totalPages . '&sort=' . $sortBy . '&search=' . urlencode($searchQuery) . '">Last</a>';
            echo '</div>';
            echo '</nav>';

            // "Sort by" dropdown right below pagination
            echo '<div class="field mt-5 has-text-centered">';
            echo '<label id="pageLabel" for="sort">Sort by:</label>';
            echo '<div class="control">';
            echo '<div class="select">';
            echo '<select id="sort" onchange="window.location.href=\'?page=1&sort=\' + this.value + \'&search=' . urlencode($searchQuery) . '\';">';
            echo '<option value="default" ' . ($sortBy == 'default' ? 'selected' : '') . '>Default</option>';
            echo '<option value="title" ' . ($sortBy == 'title' ? 'selected' : '') . '>Title</option>';
            echo '<option value="author" ' . ($sortBy == 'author' ? 'selected' : '') . '>Author</option>';
            echo '<option value="avg_rating" ' . ($sortBy == 'avg_rating' ? 'selected' : '') . '>Avg Rating</option>';
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
            <!-- ---------------------------------------------------------------------------------------------------------------------------------->
            <!-- Add Book Metrics Section -->
            <section class="section">
                <div class="container">
                    <div class="columns is-centered">
                        <div class="column is-half">
                            <h2 class="title is-4 add-header has-text-centered">Add Book Metrics</h2>

                            <!-- Add Book Form -->
                            <form class="box main-library-book-metric-add" id="addMetricForm">
                                <div class="field">
                                    <label class="label" for="id">ID</label>
                                    <div class="control">
                                        <input type="text" class="input" id="id" name="id" placeholder="Enter ID" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="author">Read Time</label>
                                    <div class="control">
                                        <input type="text" class="input" id="read_time" name="read_time" placeholder="Enter Read Time" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="shelves">Page Length</label>
                                    <div class="control">
                                        <input type="text" class="input" id="page_length" name="page_length" placeholder="Enter Page Length of Book" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="avg_rating">Book Rating</label>
                                    <div class="control">
                                        <input type="number" step="0.1" class="input" id="avg_rating" name="avg_rating" placeholder="Enter average rating" required>
                                    </div>
                                </div>

                                <div class="field is-grouped is-grouped-centered">
                                    <div class="control">
                                        <button type="submit" class="button is-primary">Add Book Metrics</button>
                                    </div>
                                </div>
                            </form>

                            <center>
                                <button type="submit" class="button is-primary" onclick="window.location.href='viewMetrics.php'">View Book Metrics</button>
                            </center>

                        </div>
                    </div>
                </div>
            </section>


        </container>
    </section>
    <!-- <div class="chat-popup" id="Chatbot-Form">
        <form action="" class="form-container">

            <body>
                <h1>ChatBot</h1> <button class="btnClose" onclick="closeForm()"><img src="icons/close.png"></i></button>
                <label for="question"><b>Lil-Librarian Here. How can I help you?</b></label>
                <textarea placeholder="Type Your Question Here" name="question" required></textarea>
                <button type="submit" id="buttonSend" class="btn">Send</button>
            </body>
        </form>
    </div> -->
    <!--
    <button class="darkToggle" id="drkTog" onclick="DarkFunction()"><img src="icons/moon_icon.ico"></i></button>
    <button class="lightToggle" id="lgtTog" onclick="LightFunction()"><img src="icons/sun_icon.ico"></i></button>

        -->

    <div class="chat-popup" id="Chatbot-Form">
        <form action="" class="form-container">
            <h1>ChatBot</h1>
            <button type="button" class="btnClose" onclick="closeForm()"><img src="icons/close.png"></button>
            <label for="question"><b>Lil-Librarian Here. How can I help you?</b></label>
            <textarea placeholder="Type your question here" name="question" required></textarea>
            <button type="submit" class="btn">Send</button>
        </form>
        <div class="chat-area" style="background: #f1f1f1; padding: 10px; height: 200px; width: 400px; overflow-y: auto; margin-top: 10px;"></div>
    </div>


    <script>
        document.querySelector('.form-container').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting traditionally
            processInput();
        });

        document.querySelector('textarea[name="question"]').addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault(); // Prevent the default action to avoid a newline
                processInput(); // Process the input as if the form was submitted
            }
        });

        function processInput() {
            let textarea = document.querySelector('textarea[name="question"]');
            let question = textarea.value.toLowerCase();
            let response = getResponse(question);

            displayMessage(`You: ${question}`);
            displayMessage(`Bot: ${response}`);

            textarea.value = ''; // Clear the textarea after the response is displayed
        }

        function getResponse(question) {
            if (/what are your opening hours|library hours/.test(question)) {
                return "Our library is open from 9 AM to 8 PM on weekdays, and from 10 AM to 5 PM on weekends.";
            } else if (/how long can i borrow a book|borrowing policies/.test(question)) {
                return "Books can be borrowed for two weeks at a time. Renewals are allowed once unless there's a hold.";
            } else if (/what are the late fees|overdue books/.test(question)) {
                return "Late fees are $0.25 per day for each book. Fees can accumulate up to the cost of the book.";
            } else if (/is (.+) available for checkout|book availability/.test(question)) {
                return `You asked about the availability of a book. Please visit our catalog to search for specific titles.`;
            } else if (/what events are scheduled|event information/.test(question)) {
                return "Check our website's event calendar for all scheduled library events, including author talks and workshops.";
            } else if (/how can i reserve a study room|room reservations/.test(question)) {
                return "You can reserve a study room online through our website or by calling the front desk.";
            } else if (/what is the wifi password|wifi access/.test(question)) {
                return "The Wi-Fi password is 'ReadMoreBooks'. Enjoy your browsing!";
            } else if (/do you offer help with research|support services/.test(question)) {
                return "Yes, we offer research support services. Please approach the information desk for assistance.";
            } else if (/can you recommend a good mystery novel|book recommendations/.test(question)) {
                return "I recommend 'The Hound of the Baskervilles' by Arthur Conan Doyle, a classic mystery full of suspense.";
            } else if (/how much does it cost to print documents|printing services/.test(question)) {
                return "Printing costs $0.10 per black and white page and $0.25 per color page.";
            } else if (/how can i donate books|donation guidelines/.test(question)) {
                return "Thank you for considering a donation! Books can be dropped off at the main desk during regular hours.";
            } else if (/how do i apply for a library card|library cards/.test(question)) {
                return "You can apply for a library card online or at any of our service desks with a valid ID.";
            } else if (/can we play a guessing game|play a game/.test(question)) {
                const randomNumber = Math.floor(Math.random() * 100) + 1;
                sessionStorage.setItem('gameNumber', randomNumber);
                return `I'm thinking of a number between 1 and 100. Guess it by typing 'guess' followed by your number!`;
            } else {
                return "I'm not sure how to help with that. Here are some questions you can ask me:\n" +
                    "- What are your opening hours?\n" +
                    "- How long can I borrow a book?\n" +
                    "- What are the late fees for overdue books?\n" +
                    "- Is [book title] available for checkout?\n" +
                    "- What events are scheduled this month?\n" +
                    "- How can I reserve a study room?\n" +
                    "- What is the wifi password?\n" +
                    "- Do you offer help with research?\n" +
                    "- Can you recommend a good book?\n" +
                    "- How much does it cost to print documents?\n" +
                    "- How can I donate books?\n" +
                    "- How do I apply for a library card?\n" +
                    "- Can we play a guessing game?";
            }
        }

        function displayMessage(message) {
            const chatArea = document.querySelector('.chat-area');
            const messageDiv = document.createElement('div');
            messageDiv.textContent = message;
            chatArea.appendChild(messageDiv);
            chatArea.scrollTop = chatArea.scrollHeight; // Scroll to the bottom of the chat area
        }

        function closeForm() {
            document.getElementById('Chatbot-Form').style.display = 'none';
        }

        function closeForm() {
            document.getElementById('Chatbot-Form').style.display = 'none';
        }


        function closeForm() {
            document.getElementById('Chatbot-Form').style.display = 'none';
        }

        function closeForm() {
            document.getElementById('Chatbot-Form').style.display = 'none';
        }


        function showChatBot() {

            document.getElementById("Chatbot-Form").style.display = "block";
        }

        setTimeout("showChatBot()", 3000);

        function openForm() {
            document.getElementById("Chatbot-Form").style.display = "block";
        }

        function closeForm() {
            document.getElementById("Chatbot-Form").style.display = "none";
        }

        // var x = document.getElementById("myAudio");

        // function playAudio()
        // {
        // const button = document.getElementById("buttonSend");
        // const audio = document.getElementById("notificationButtonSound");

        // }

        // function DarkFunction()
        // {
        // var element = document.getElementById("Chatbot-Form");
        // element.classList.toggle("dark-mode");

        // const darkToggle = document.getElementById('darkToggle');

        // }

        // function LightFunction()
        // {
        // var element = document.getElementById("Chatbot-Form");
        // element.classList.toggle("light-mode");
        // }


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
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        document.getElementById('addMetricForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Get the form data
            const formData = new FormData(this);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            // Send data to addMetric.php via AJAX
            fetch('addMetric.php', {
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
                        Swal.fire('Success!', 'Book Metric added successfully!', 'success');
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