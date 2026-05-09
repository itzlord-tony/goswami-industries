<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goswami Industry | Premium E-Commerce</title>
    <meta name="description" content="Shop premium electronics, clothing, and home appliances at Goswami Industry.">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Loader -->
    <div id="loader">
        <div class="spinner"></div>
    </div>

    <!-- Navbar -->
    <header>
        <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 10px;">
            <img src="images/logo.jpeg" alt="Goswami Industry Logo" style="height: 50px; border-radius: 5px;">
            Goswami Industry
        </a>
        
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>

        <div class="nav-icons">
            <a href="products.php"><i class="fas fa-search"></i></a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php" title="Login"><i class="fas fa-user"></i></a>
            <?php endif; ?>

            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">
                    <?php 
                        $cart_count = 0;
                        if(isset($_SESSION['cart'])) {
                            foreach($_SESSION['cart'] as $qty) {
                                $cart_count += $qty;
                            }
                        }
                        echo $cart_count;
                    ?>
                </span>
            </a>
            
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
