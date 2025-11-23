<?php
    session_start();
    include "includes/db.php";

    $search = "";
    if (isset($_GET['search'])) {
        $search       = trim($_GET['search']);
        $stmt         = $conn->prepare("SELECT * FROM menu WHERE menu_name LIKE ?");
        $search_param = "%" . $search . "%";
        $stmt->bind_param("s", $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM menu");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bat Café</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <?php include "includes/navbar.php"; ?>

    <div class="menu-banner" id="menu">
        <div id="carouselExampleDark" class="carousel carousel-dark slide">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="10000">
            <img src="images/menu_1.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Try our Limited Frappe!</h5>
                <p>A cozy, curated café menu featuring fresh, flavorful bites and thoughtfully crafted drinks designed to delight every taste.</p>
            </div>
            </div>
            <div class="carousel-item" data-bs-interval="2000">
            <img src="images/menu_2.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Book an Event!</h5>
                <p>A smooth, hassle-free booking experience that lets you reserve your spot quickly, confidently, and with total ease.</p>
            </div>
            </div>
            <div class="carousel-item">
            <img src="images/menu_3.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Get a reservation for table!</h5>
                <p>Secure your perfect spot with a quick and easy table reservation made just for you</p>
            </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>
    </div>

    <div class="menu-filters">
        <button class="filter-btn active" data-filter="all">All Menu</button>
        <button class="filter-btn" data-filter="Specialty Coffee">Specialty Coffee</button>
        <button class="filter-btn" data-filter="Non Coffee">Non Coffee</button>
        <button class="filter-btn" data-filter="Refreshing">Refreshing</button>
        <button class="filter-btn" data-filter="Snacks">Snacks</button>
        <button class="filter-btn" data-filter="Meals">Meals</button>
        <button class="filter-btn" data-filter="Equipment">Equipment</button>
    </div>

    <div class="menu-bg">
        <div class="menu-container">
            <div class="menu-grid">

            <?php if ($result->num_rows > 0): ?>
                <?php while ($menu = $result->fetch_assoc()): ?>
                    <div class="menu-items" data-category="<?php echo $menu['menu_category']; ?>">
                        <div class="menu-image">
                            <img src="<?php echo $menu['menu_image']; ?>" alt="<?php echo $menu['menu_name']; ?>">
                        </div>
                        <div class="menu-details">
                            <h5 class="menu-category"><?php echo $menu['menu_category']; ?></h5>
                            <?php
                            if (strtolower(trim($menu['menu_category'])) == 'specialty coffee') {
                                echo '<p class="menu-hc hot-cold">Hot/Cold</p>';
                            }
                            ?>
                            <h3 class="menu-name"><?php echo $menu['menu_name']; ?></h3>
                            <p class="menu-description"><?php echo $menu['menu_description']; ?></p>
                            <p class="menu-price">₱<?php echo number_format($menu['menu_price'], 2); ?></p>
                            <div class="menu-buttons">
                                <input type="hidden" name="menu_id" value="<?php echo $menu['menu_id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn add-cart-btn">Add to Cart</button>
                                <a href="payment_order.php?menu_id=<?php echo $menu['menu_id']; ?>" class="btn buy-now-btn">
                                    Buy Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="menu-missing">No menu items found for "<?php echo htmlspecialchars($search); ?>"</p>
            <?php endif; ?>

            </div>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>
    <script>
    const filterButtons = document.querySelectorAll(".filter-btn");
    const menuItems = document.querySelectorAll(".menu-items");

    filterButtons.forEach(button => {
        button.addEventListener("click", () => {
            document.querySelector(".filter-btn.active").classList.remove("active");
            button.classList.add("active");
            let category = button.getAttribute("data-filter");
            menuItems.forEach(item => {
                let itemCategory = item.getAttribute("data-category");
                if (category === "all" || itemCategory === category) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });
    });
    </script>
    <script>
    const searchInput = document.querySelector('input[name="search"]');
    searchInput.addEventListener('input', () => {
        const value = searchInput.value.toLowerCase();
        const menuItems = document.querySelectorAll('.menu-items');
        menuItems.forEach(item => {
            const name = item.querySelector('.menu-name').textContent.toLowerCase();
            if (name.includes(value) || value === '') {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    </script>
</body>
</html>