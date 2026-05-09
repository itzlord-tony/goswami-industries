<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Delete product
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $del_id");
    header("Location: products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="index.php" class="logo">Goswami Admin</a>
            <ul class="admin-nav">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php" class="active"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="add_product.php"><i class="fas fa-plus"></i> Add Product</a></li>
                <li><a href="add_category.php"><i class="fas fa-tags"></i> Add Category</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h2>Manage Products</h2>
                <a href="add_product.php" class="btn"><i class="fas fa-plus"></i> Add New Product</a>
            </div>
            
            <div class="admin-card">
                <div class="cart-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0){
                                while($row = $result->fetch_assoc()){
                                    echo "<tr>";
                                    echo "<td><img src='../".htmlspecialchars($row['image'])."' width='50' style='border-radius:5px;'></td>";
                                    echo "<td>".htmlspecialchars($row['name'])."</td>";
                                    echo "<td>₹".number_format($row['price'], 2)."</td>";
                                    echo "<td>".htmlspecialchars($row['cat_name'])."</td>";
                                    echo "<td class='action-btns'>
                                            <a href='#' class='text-primary' title='Edit (Not Implemented)'><i class='fas fa-edit'></i></a>
                                            <a href='products.php?delete=".$row['id']."' class='text-danger' onclick='return confirm(\"Are you sure?\");' title='Delete'><i class='fas fa-trash'></i></a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No products found.</td></tr>";
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
