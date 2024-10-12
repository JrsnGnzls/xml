<nav class="navbar navbar-expand-lg bg-white shadow sticky-top">

  <div class="container">

  <div class="col-md-2">
    <a class="navbar-brand" href="index.php" style="background-color: #ffb82e; padding: 6px 12px; border-radius: 18px; font-size: 24px;">
        <i class="fas fa-gamepad"></i>
        Game Review
    </a>
    </div>

    <div class="col-md-4 my-auto">
      <form action="searchpage.php" method="GET" role="search">
        <div class="input-group">
          <input type="search" name="search" value="" placeholder="Search" class="form-control" />
            <button class="btn" type="submit" style="background-color: #ffb82e;">
            <i class="fa fa-search"></i>
            </button>
        </div>
      </form>
    </div>

    <div class="col-md-6">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0" style="font-size: 16px; font-weight: 500;">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Game News</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="add-to-favorites.php">My Favorites</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="myfeedback.php">My Comments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="https://www.gameinformer.com/reviews.xml">RSS</a>
        </li>
        <?php if(isset($_SESSION['username'])) { ?>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo $_SESSION['username']; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="change_password.php">Change Password</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
             </li>
        </ul>
        <?php } else { ?>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link nav-link-4" href="login.php">Login</a>
            </li>
        </ul>
        <?php } ?>
      </ul>

    </div>
    </div>
  </div>
</nav>