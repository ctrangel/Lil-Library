<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$username = $_SESSION['username'] ?? 'User'; // Ensure session is started at the top of your script
?>

<nav class="navbar is-spaced is-light" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="dashboard.php">
            <img src="/Lil-libraryV3/media/logo.webp" alt="Lil Library Logo" width="30" height="30">
        </a>
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarMenu" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item <?php echo $currentPage == 'dashboard.php' ? 'is-active' : ''; ?>" href="dashboard.php">Home</a>
            <a class="navbar-item <?php echo $currentPage == 'myBooks.php' ? 'is-active' : ''; ?>" href="myBooks.php">My Books</a>
            <a class="navbar-item <?php echo $currentPage == 'browseBooks.php' ? 'is-active' : ''; ?>" href="browseBooks.php">Browse</a>
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <iframe src="https://tunein.com/embed/player/s22142/" style="width:100%; height:100px;" scrolling="no" frameborder="no"></iframe>
            </div>
            <div class="navbar-item">
                <iframe src="https://tunein.com/embed/player/s28361/" style="width:100%; height:100px;" scrolling="no" frameborder="no"></iframe>
            </div>
            <span class="navbar-item">
                Welcome, <strong><?php echo $username; ?></strong>
            </span>
            <div class="navbar-item">
                <div class="buttons">
                    <button type="button" name="logout" class="button logout-btn">Logout</button>
                </div>
            </div>
        </div>
    </div>
</nav>