<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $success = "Category added successfully.";
        } else {
            $error = "Failed to add category to database.";
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category - Admin</title>
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
                <li><a href="add_category.php" class="active"><i class="fas fa-tags"></i> Add Category</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h2>Add New Category</h2>
                <a href="products.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Products</a>
            </div>
            
            <div class="admin-card" style="max-width: 600px;">
                <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
                
                <form action="add_category.php" method="POST">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" required placeholder="e.g., Furniture">
                    </div>
                    <button type="submit" class="btn" style="width:100%;"><i class="fas fa-save"></i> Save Category</button>
                </form>

                <div style="margin-top: 30px;">
                    <h3>Existing Categories</h3>
                    <ul style="list-style: none; padding: 0; margin-top: 15px;">
                        <?php
                        $cat_res = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                        while($cat = $cat_res->fetch_assoc()){
                            echo "<li style='padding: 10px; background: var(--bg-dark); margin-bottom: 5px; border-radius: 5px;'><i class='fas fa-tag' style='color: var(--primary);'></i> ".htmlspecialchars($cat['name'])."</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
