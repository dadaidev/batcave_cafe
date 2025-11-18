<!DOCTYPE html> 
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img class="logo" src="images/logo.png">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="booking.php">Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>

                    <li class="nav-item">
                        <a class="announcement" href="index.php"><i class="bi bi-bell"></i></a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="customer_login.php" role="button"
                        <?php if (isset($_SESSION['username'])): ?>
                            data-bs-toggle="dropdown"
                        <?php endif; ?>
                        aria-expanded="false">
                            <i class="bi bi-person-fill"></i>
                        </a>

                        <?php if (isset($_SESSION['username'])): ?>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        <?php endif; ?>
                    </li>

                </ul>

                <?php if ($currentPage == 'menu.php'): ?>
                <form class="btn d-flex" action="menu.php" method="GET">
                    <input class="form-control me-2" 
                        type="search" 
                        name="search"
                        placeholder="Search menu..." 
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn-search" type="submit">Search</button>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
