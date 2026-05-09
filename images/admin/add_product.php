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
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);
    $image_path = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80'; // Default placeholder

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/' . $file_name;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, GIF, WEBP files are allowed.";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $category_id, $name, $description, $price, $image_path);
        if ($stmt->execute()) {
            $success = "Product added successfully.";
        } else {
            $error = "Failed to add product to database.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
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
                <li><a href="add_product.php" class="active"><i class="fas fa-plus"></i> Add Product</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> View Website</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h2>Add New Product</h2>
                <a href="products.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            
            <div class="admin-card" style="max-width: 600px;">
                <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
                
                <form action="add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required style="width: 100%; padding: 0.8rem; background: var(--bg-dark); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 5px; outline: none;">
                            <?php
                            $cat_res = $conn->query("SELECT * FROM categories");
                            while($cat = $cat_res->fetch_assoc()){
                                echo "<option value='".$cat['id']."'>".htmlspecialchars($cat['name'])."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" accept="image/*" style="background:transparent; border:none; padding:0;">
                        <small style="color:var(--text-muted);">Leave empty to use a placeholder image.</small>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn" style="width:100%;"><i class="fas fa-save"></i> Save Product</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
