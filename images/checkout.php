<?php
require_once 'includes/db.php';

if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $error = "Please fill in all required fields.";
    } else {
        // Calculate total
        $total = 0;
        $ids = array_keys($_SESSION['cart']);
        $ids_str = implode(',', $ids);
        $sql = "SELECT id, price FROM products WHERE id IN ($ids_str)";
        $result = $conn->query($sql);
        $prices = [];
        while($row = $result->fetch_assoc()){
            $prices[$row['id']] = $row['price'];
            $total += $row['price'] * $_SESSION['cart'][$row['id']];
        }
        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
        
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, name, email, phone, address, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssd", $user_id, $name, $email, $phone, $address, $total);
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            // Insert items
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach($_SESSION['cart'] as $pid => $qty){
                $price = $prices[$pid];
                $item_stmt->bind_param("iiid", $order_id, $pid, $qty, $price);
                $item_stmt->execute();
            }
            $_SESSION['cart'] = []; // Clear cart
            $success = "Order placed successfully! Your Order ID is #$order_id.";
        } else {
            $error = "Error processing order. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<section class="checkout-page" style="padding: 5rem 5%;">
    <h1 class="section-title">Checkout</h1>
    
    <?php if($success): ?>
        <div class="alert alert-success" style="max-width:600px; margin: 0 auto; text-align:center; padding: 2rem;">
            <i class="fas fa-check-circle" style="font-size:3rem; margin-bottom:1rem;"></i>
            <h3><?php echo $success; ?></h3>
            <p style="margin-top:1rem;">Thank you for shopping with Goswami Industry. We will contact you soon regarding your delivery.</p>
            <br>
            <a href="index.php" class="btn">Return to Home</a>
        </div>
    <?php elseif(empty($_SESSION['cart'])): ?>
        <div class="alert alert-error">Your cart is empty.</div>
    <?php else: ?>
    
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">
        <div style="background:var(--bg-light); padding:2rem; border-radius:10px; border:1px solid var(--border-color);">
            <h3 style="margin-bottom:1.5rem; color:var(--primary-color); border-bottom:1px solid var(--border-color); padding-bottom:10px;">Shipping Details</h3>
            <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            
            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="address" rows="4" required placeholder="Enter full address..."></textarea>
                </div>
                <button type="submit" class="btn" style="width:100%; font-size:1.1rem; padding:1rem;"><i class="fas fa-lock"></i> Place Order Securely</button>
            </form>
        </div>
        
        <div>
            <h3 style="margin-bottom:1.5rem; color:var(--primary-color); border-bottom:1px solid var(--border-color); padding-bottom:10px;">Order Summary</h3>
            <div class="cart-summary" style="margin-top:0;">
                <table style="width:100%; border-collapse:collapse;">
                    <?php
                    $total = 0;
                    $ids = array_keys($_SESSION['cart']);
                    $ids_str = implode(',', $ids);
                    $sql = "SELECT * FROM products WHERE id IN ($ids_str)";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                        $qty = $_SESSION['cart'][$row['id']];
                        $sub = $row['price'] * $qty;
                        $total += $sub;
                        echo "<tr>";
                        echo "<td style='padding: 10px 0; border-bottom:1px solid var(--border-color);'>".htmlspecialchars($row['name'])." <span style='color:var(--text-muted);'>x $qty</span></td>";
                        echo "<td style='padding: 10px 0; border-bottom:1px solid var(--border-color); text-align:right;'>₹".number_format($sub, 2)."</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <div class="cart-total" style="margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1rem;">
                    <span>Total To Pay</span>
                    <span>₹<?php echo number_format($total, 2); ?></span>
                </div>
                <p style="text-align:center; color:var(--text-muted); margin-top:1rem; font-size:0.9rem;"><i class="fas fa-shield-alt"></i> Safe and secure checkout</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
