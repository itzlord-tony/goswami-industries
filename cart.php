<?php
require_once 'includes/db.php';

// Handle Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($action === 'add' && $product_id > 0) {
        $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $qty;
        } else {
            $_SESSION['cart'][$product_id] = $qty;
        }
        header("Location: cart.php");
        exit();
    } elseif ($action === 'update' && isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $pid => $q) {
            $pid = (int)$pid;
            $q = (int)$q;
            if ($q > 0) {
                $_SESSION['cart'][$pid] = $q;
            } else {
                unset($_SESSION['cart'][$pid]);
            }
        }
        header("Location: cart.php");
        exit();
    } elseif ($action === 'remove' && $product_id > 0) {
        unset($_SESSION['cart'][$product_id]);
        header("Location: cart.php");
        exit();
    }
}

include 'includes/header.php';
?>

<section class="cart-page">
    <h1 class="section-title">Your Cart</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div style="text-align:center; padding: 4rem;">
            <i class="fas fa-shopping-cart" style="font-size:5rem; color:var(--text-muted); margin-bottom:1.5rem;"></i>
            <h2 style="color:var(--text-muted);">Your cart is currently empty.</h2>
            <br>
            <a href="products.php" class="btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <form action="cart.php" method="POST">
            <input type="hidden" name="action" value="update">
            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        $ids = array_keys($_SESSION['cart']);
                        if(!empty($ids)){
                            $ids_str = implode(',', $ids);
                            $sql = "SELECT * FROM products WHERE id IN ($ids_str)";
                            $result = $conn->query($sql);
                            while($row = $result->fetch_assoc()){
                                $pid = $row['id'];
                                $qty = $_SESSION['cart'][$pid];
                                $subtotal = $row['price'] * $qty;
                                $total += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="cart-item-info">
                                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="img">
                                            <span style="font-weight:500;"><?php echo htmlspecialchars($row['name']); ?></span>
                                        </div>
                                    </td>
                                    <td style="color:var(--primary-color); font-weight:bold;">₹<?php echo number_format($row['price'], 2); ?></td>
                                    <td>
                                        <input type="number" name="quantities[<?php echo $pid; ?>]" value="<?php echo $qty; ?>" min="1" class="qty-input">
                                    </td>
                                    <td style="font-weight:bold;">₹<?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <button type="submit" formaction="cart.php" formmethod="POST" name="product_id" value="<?php echo $pid; ?>" onclick="this.form.elements['action'].value='remove';" class="btn btn-outline" style="padding:5px 10px; border-color:var(--danger); color:var(--danger);"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="cart-summary">
                <div class="cart-total">
                    <span>Grand Total:</span>
                    <span>₹<?php echo number_format($total, 2); ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                    <button type="submit" class="btn btn-outline"><i class="fas fa-sync"></i> Update Cart</button>
                    <a href="checkout.php" class="btn"><i class="fas fa-check-circle"></i> Proceed to Checkout</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
