 <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
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
             <a class="navbar-item <?php echo $currentPage === 'dashboard.php' ? 'is-active' : ''; ?>" href="dashboard.php">Home</a>
             <a class="navbar-item <?php echo $currentPage === 'myBooks.php' ? 'is-active' : ''; ?>" href="myBooks.php">My Books</a>
             <a class="navbar-item <?php echo $currentPage === 'browseBooks.php' ? 'is-active' : ''; ?>" href="browseBooks.php">Browse</a>
         </div>

         <div class="navbar-end">
             <span class="navbar-item">
                 Welcome, <strong><?php echo $username ?? 'User'; ?></strong>
             </span>
             <div class="navbar-item">
                 <div class="buttons">
                     <button type="button" name="logout" class="button logout-btn">Logout</button>
                 </div>
             </div>
         </div>
     </div>
 </nav>