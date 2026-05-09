<?php
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();
include 'includes/header.php';
?>

<section class="product-detail">
    <div class="product-detail-container">
        <div class="product-detail-img">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-detail-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
            <p class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <form action="cart.php" method="POST" style="margin-top: 2rem; background: var(--bg-light); padding: 2rem; border-radius: 10px; border: 1px solid var(--border-color);">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="add">
                <div style="display:flex; align-items:center; gap:15px; margin-bottom:1.5rem;">
                    <label for="qty" style="color:var(--text-main); font-weight:bold;">Quantity:</label>
                    <input type="number" id="qty" name="quantity" value="1" min="1" class="qty-input">
                </div>
                <button type="submit" class="btn" style="width:100%; font-size:1.1rem; padding:1rem;"><i class="fas fa-cart-plus"></i> Add to Cart</button>
            </form>
        </div>
    </div>
</section>

<!-- Related Products -->
<section>
    <h2 class="section-title">Related Products</h2>
    <div class="products-grid">
        <?php
        $rel_stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
        $rel_stmt->bind_param("ii", $product['category_id'], $id);
        $rel_stmt->execute();
        $rel_res = $rel_stmt->get_result();

        if($rel_res->num_rows > 0){
            while($row = $rel_res->fetch_assoc()){
                ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $row['id']; ?>" style="display:contents;">
                        <div class="product-img">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>
                    </a>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="product-price">₹<?php echo number_format($row['price'], 2); ?></div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p style='color:var(--text-muted);'>No related products found.</p>";
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
