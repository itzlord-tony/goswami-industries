<?php
require_once '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Goswami Industry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="index.php" class="logo">Goswami Admin</a>
            <ul class="admin-nav">
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="add_product.php"><i class="fas fa-plus"></i> Add Product</a></li>
                <li><a href="add_category.php"><i class="fas fa-tags"></i> Add Category</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h2>Dashboard Overview</h2>
                <div style="color:var(--text-muted);"><i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></div>
            </div>
            
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <?php
                // Count products
                $res = $conn->query("SELECT COUNT(*) as c FROM products");
                $p_count = $res->fetch_assoc()['c'];
                
                // Count orders
                $res = $conn->query("SELECT COUNT(*) as c FROM orders");
                $o_count = $res->fetch_assoc()['c'];
                
                // Total revenue
                $res = $conn->query("SELECT SUM(total_amount) as s FROM orders WHERE status='completed'");
                $revenue = $res->fetch_assoc()['s'] ?? 0;

                // Graph Data for Last 7 Days
                $sales_data = [];
                $labels = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('M d', strtotime($date));
                    $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = ? AND status='completed'");
                    $stmt->bind_param("s", $date);
                    $stmt->execute();
                    $s_res = $stmt->get_result();
                    $s_row = $s_res->fetch_assoc();
                    $sales_data[] = $s_row['total'] ? (float)$s_row['total'] : 0;
                }
                ?>
                <div class="admin-card" style="text-align:center;">
                    <i class="fas fa-box" style="font-size:3rem; color:var(--primary-color); margin-bottom:1rem;"></i>
                    <h3><?php echo $p_count; ?></h3>
                    <p style="color:var(--text-muted);">Total Products</p>
                </div>
                <div class="admin-card" style="text-align:center;">
                    <i class="fas fa-shopping-bag" style="font-size:3rem; color:var(--success); margin-bottom:1rem;"></i>
                    <h3><?php echo $o_count; ?></h3>
                    <p style="color:var(--text-muted);">Total Orders</p>
                </div>
                <div class="admin-card" style="text-align:center;">
                    <i class="fas fa-rupee-sign" style="font-size:3rem; color:#f1c40f; margin-bottom:1rem;"></i>
                    <h3>₹<?php echo number_format($revenue, 2); ?></h3>
                    <p style="color:var(--text-muted);">Revenue (Completed)</p>
                </div>
            </div>
            
            <div class="admin-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom:1rem;">Sales Last 7 Days</h3>
                <div style="height: 300px; width: 100%;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            
            <div class="admin-card">
                <h3 style="margin-bottom:1rem;">Recent Orders</h3>
                <div class="cart-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM orders ORDER BY id DESC LIMIT 5";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0){
                                while($row = $result->fetch_assoc()){
                                    $order_id = $row['id'];
                                    
                                    // Fetch items summary
                                    $items_sql = "SELECT oi.quantity, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id";
                                    $items_res = $conn->query($items_sql);
                                    $items_str = "";
                                    while($i_row = $items_res->fetch_assoc()){
                                        $items_str .= $i_row['name'] . " (x" . $i_row['quantity'] . "), ";
                                    }
                                    $items_str = rtrim($items_str, ", ");

                                    $status_color = $row['status'] == 'completed' ? 'var(--success)' : ($row['status'] == 'cancelled' ? 'var(--danger)' : 'orange');
                                    echo "<tr>";
                                    echo "<td>#".$order_id."</td>";
                                    echo "<td>".htmlspecialchars($row['name'])."</td>";
                                    echo "<td><small>".htmlspecialchars($items_str)."</small></td>";
                                    echo "<td>₹".number_format($row['total_amount'], 2)."</td>";
                                    echo "<td style='color:$status_color; text-transform:capitalize; font-weight:bold;'>".$row['status']."</td>";
                                    echo "<td>".date('M d, Y', strtotime($row['created_at']))."</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No recent orders.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Sales (₹)',
                data: <?php echo json_encode($sales_data); ?>,
                borderColor: '#bb86fc',
                backgroundColor: 'rgba(187, 134, 252, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    ticks: { color: '#aaa' }
                },
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    ticks: { color: '#aaa' }
                }
            },
            plugins: {
                legend: { labels: { color: '#aaa' } }
            }
        }
    });
    </script>
</body>
</html>
