<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library</title>
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <h1>Lil Library</h1>

    <!-- Search Form -->
    <form method="GET" action="" class="search-bar">
        <input class="search-input" type="text" name="search" placeholder="Search books..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
        <button class="search-btn" type="submit">Search</button>
    </form>

    <?php
    // Pagination variables
    $booksPerPage = 5;
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $searchQuery = isset($_GET['search']) ? urlencode(trim($_GET['search'])) : '';
    $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'default';
    $apiUrl = "http://localhost:4000/books?limit=$booksPerPage&page=$currentPage&sort=$sortBy&search=$searchQuery";

    // Fetch books from the API
    $booksData = file_get_contents($apiUrl);
    $booksData = json_decode($booksData, true);
    $totalBooks = $booksData['totalBooks']; // Adjust API to return total books
    $totalPages = ceil($totalBooks / $booksPerPage);
    $booksToShow = $booksData['books']; // Adjust API to return paginated books as 'books'

    // Display the books or show 'no results' message
    if ($totalBooks === 0) {
        echo '<div class="no-results">';
        echo '<p>Nothing matches your search :(</p>';
        echo '<div class="noResults-img"></div>';
        echo '</div>';
    } else {
        echo '<div id="book-container" class="book-container">';
        foreach ($booksToShow as $book) {
            echo '<div class="book-card">';
            echo '<div class="book-title">' . htmlspecialchars($book['title']) . '</div>';
            echo '<div class="book-author">' . htmlspecialchars($book['author']) . '</div>';
            echo '<div class="book-shelves"><strong>Shelves:</strong> ' . htmlspecialchars($book['shelves']) . '</div>';
            echo '<div class="book-rating"><strong>Avg Rating:</strong> ' . htmlspecialchars($book['avg_rating']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    // Pagination controls
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

    <!-- Sort Dropdown -->
    <div class="controls">
        <label id="pageLabel" for="sort">Sort by:</label>
        <select id="sort" onchange="window.location.href='?page=1&sort=' + this.value + '&search=<?php echo urlencode($searchQuery); ?>';">
            <option value="default" <?php echo $sortBy == 'default' ? 'selected' : ''; ?>>Default</option>
            <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Title</option>
            <option value="author" <?php echo $sortBy == 'author' ? 'selected' : ''; ?>>Author</option>
            <option value="avg_rating" <?php echo $sortBy == 'avg_rating' ? 'selected' : ''; ?>>Avg Rating</option>
        </select>
    </div>

</body>

</html>