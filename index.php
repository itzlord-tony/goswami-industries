<?php
require_once 'includes/db.php';
include 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1>Welcome to Goswami Industry</h1>
        <p>Discover premium products at unbeatable prices. Top quality guaranteed.</p>
        <a href="products.php" class="btn">Shop Now <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<section class="featured-products">
    <h2 class="section-title">Featured Products</h2>
    <div class="products-grid">
        <?php
        $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $row['id']; ?>" style="display:contents;">
                        <div class="product-img">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>
                    </a>
                    <div class="product-info">
                        <a href="product.php?id=<?php echo $row['id']; ?>"><h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3></a>
                        <div class="product-price">₹<?php echo number_format($row['price'], 2); ?></div>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="add">
                            <button type="submit" class="btn add-to-cart"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p style='text-align:center; width:100%; color:var(--text-muted);'>No featured products found.</p>";
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
