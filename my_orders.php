<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';

$user_id = $_SESSION['user_id'];
?>

<section class="my-orders" style="padding: 5rem 5%;">
    <h1 class="section-title">My Orders</h1>
    
    <div class="cart-table-wrapper" style="max-width: 1000px; margin: 0 auto; background: var(--bg-light); padding: 2rem; border-radius: 10px; border: 1px solid var(--border-color);">
        <table class="cart-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $order_id = $row['id'];
                        
                        // Fetch items for this order
                        $items_sql = "SELECT oi.*, p.name as product_name FROM order_items oi 
                                     JOIN products p ON oi.product_id = p.id 
                                     WHERE oi.order_id = ?";
                        $item_stmt = $conn->prepare($items_sql);
                        $item_stmt->bind_param("i", $order_id);
                        $item_stmt->execute();
                        $items_result = $item_stmt->get_result();
                        
                        echo "<tr>";
                        echo "<td>#$order_id</td>";
                        echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                        echo "<td>";
                        while ($item = $items_result->fetch_assoc()) {
                            echo "<div style='font-size:0.9rem; margin-bottom:5px;'>";
                            echo htmlspecialchars($item['product_name']) . " <span style='color:var(--primary-color)'>x" . $item['quantity'] . "</span>";
                            echo "</div>";
                        }
                        echo "</td>";
                        echo "<td style='font-weight:bold; color:var(--primary-color);'>₹" . number_format($row['total_amount'], 2) . "</td>";
                        echo "<td>";
                        $status_class = '';
                        if($row['status'] == 'pending') $status_class = 'text-primary';
                        else if($row['status'] == 'completed') $status_class = 'text-success';
                        else if($row['status'] == 'cancelled') $status_class = 'text-danger';
                        
                        echo "<span class='$status_class' style='text-transform: capitalize; font-weight:600;'>" . $row['status'] . "</span>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 2rem;'>You haven't placed any orders yet. <br><br> <a href='products.php' class='btn btn-outline'>Start Shopping</a></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
