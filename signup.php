<?php
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    
    if(empty($name) || empty($email) || empty($password)){
        $error = "Please fill in all required fields.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email is already registered. Please login instead.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hash, $phone);
            if ($stmt->execute()) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <h2><i class="fas fa-user-plus"></i> Create an Account</h2>
    <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
            <br><br><a href="login.php" class="btn" style="width:100%; text-align:center;">Go to Login</a>
        </div>
    <?php else: ?>
    
    <form action="signup.php" method="POST">
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" required placeholder="John Doe">
        </div>
        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="email" required placeholder="example@domain.com">
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="Optional">
        </div>
        <div class="form-group">
            <label>Password *</label>
            <input type="password" name="password" required placeholder="Create a strong password">
        </div>
        <button type="submit" class="btn" style="width:100%;"><i class="fas fa-user-check"></i> Sign Up</button>
    </form>
    <p style="text-align:center; margin-top:1.5rem; color:var(--text-muted);">Already have an account? <a href="login.php" style="color:var(--primary-color); font-weight:bold;">Login</a></p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
