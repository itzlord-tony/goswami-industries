<?php
require_once 'includes/db.php';
include 'includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$where = [];
if ($search !== '') {
    $where[] = "name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
if ($category > 0) {
    $where[] = "category_id = $category";
}

$where_clause = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

$sql = "SELECT * FROM products $where_clause ORDER BY id DESC";
$result = $conn->query($sql);
?>

<section class="products-page">
    <h1 class="section-title">Our Products</h1>
    
    <div class="search-container">
        <form action="products.php" method="GET" style="display:flex; width:100%; align-items:center;">
            <input type="text" name="search" class="search-input" placeholder="Search products... (or use voice)" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
            <button type="button" class="mic-btn" title="Voice Search"><i class="fas fa-microphone"></i></button>
        </form>
    </div>

    <!-- Category Filter -->
    <div style="text-align:center; margin-bottom: 3rem;">
        <a href="products.php" class="btn <?php echo $category == 0 ? '' : 'btn-outline'; ?>" style="margin:5px;">All</a>
        <?php
        $cat_sql = "SELECT * FROM categories";
        $cat_res = $conn->query($cat_sql);
        if($cat_res) {
            while($cat = $cat_res->fetch_assoc()) {
                $is_active = $category == $cat['id'] ? '' : 'btn-outline';
                echo '<a href="products.php?category='.$cat['id'].'" class="btn '.$is_active.'" style="margin:5px;">'.htmlspecialchars($cat['name']).'</a>';
            }
        }
        ?>
    </div>

    <div class="products-grid">
        <?php
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
            echo '<div style="text-align:center; width:100%; padding: 4rem 0;"><h2 style="color:var(--text-muted);"><i class="fas fa-box-open"></i> No products found.</h2><p style="margin-top:1rem; color:var(--text-muted);">Try adjusting your search or filters.</p></div>';
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
