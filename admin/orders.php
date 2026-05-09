<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    header("Location: orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="index.php" class="logo">Goswami Admin</a>
            <ul class="admin-nav">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="add_product.php"><i class="fas fa-plus"></i> Add Product</a></li>
                <li><a href="add_category.php"><i class="fas fa-tags"></i> Add Category</a></li>
                <li><a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h2>Manage Orders</h2>
            </div>
            
            <div class="admin-card">
                <div class="cart-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Details</th>
                                <th>Items Ordered</th>
                                <th>Address</th>
                                <th>Total</th>
                                <th>Status / Update</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM orders ORDER BY id DESC";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0){
                                while($row = $result->fetch_assoc()){
                                    $order_id = $row['id'];
                                    
                                    // Fetch items for this order
                                    $items_sql = "SELECT oi.*, p.name as product_name FROM order_items oi 
                                                 JOIN products p ON oi.product_id = p.id 
                                                 WHERE oi.order_id = $order_id";
                                    $items_result = $conn->query($items_sql);
                                    
                                    echo "<tr>";
                                    echo "<td>#".$order_id."</td>";
                                    echo "<td>
                                            <strong>".htmlspecialchars($row['name'])."</strong><br>
                                            <small style='color:var(--text-muted);'>".htmlspecialchars($row['email'])."<br>".htmlspecialchars($row['phone'])."</small>
                                          </td>";
                                    echo "<td>
                                            <div style='max-width:250px; font-size:0.85rem;'>";
                                    while($item = $items_result->fetch_assoc()){
                                        echo "<div style='display:flex; justify-content:space-between; margin-bottom:5px; border-bottom:1px solid #333; padding-bottom:2px;'>
                                                <span>".htmlspecialchars($item['product_name'])." <span style='color:var(--primary-color)'>x".$item['quantity']."</span></span>
                                                <span>₹".number_format($item['price'] * $item['quantity'], 2)."</span>
                                              </div>";
                                    }
                                    echo "  </div>
                                          </td>";
                                    echo "<td><small>".htmlspecialchars($row['address'])."</small></td>";
                                    echo "<td style='font-weight:bold; color:var(--primary-color);'>₹".number_format($row['total_amount'], 2)."</td>";
                                    echo "<td>
                                            <form action='orders.php' method='POST' style='display:flex; gap:5px;'>
                                                <input type='hidden' name='order_id' value='".$row['id']."'>
                                                <select name='status' style='padding:5px; background:var(--bg-dark); color:var(--text-main); border:1px solid var(--border-color); border-radius:3px;'>
                                                    <option value='pending' ".($row['status']=='pending'?'selected':'').">Pending</option>
                                                    <option value='completed' ".($row['status']=='completed'?'selected':'').">Completed</option>
                                                    <option value='cancelled' ".($row['status']=='cancelled'?'selected':'').">Cancelled</option>
                                                </select>
                                                <button type='submit' name='update_status' class='btn btn-outline' style='padding:5px 10px; font-size:0.8rem;'><i class='fas fa-check'></i></button>
                                            </form>
                                          </td>";
                                    echo "<td><small>".date('M d, Y H:i', strtotime($row['created_at']))."</small></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No orders found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
