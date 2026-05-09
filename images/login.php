<?php
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];
            if ($row['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Invalid credentials.";
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <h2><i class="fas fa-user-circle"></i> Login to Your Account</h2>
    <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    
    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="example@domain.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn" style="width:100%;"><i class="fas fa-sign-in-alt"></i> Login</button>
    </form>
    <p style="text-align:center; margin-top:1.5rem; color:var(--text-muted);">Don't have an account? <a href="signup.php" style="color:var(--primary-color); font-weight:bold;">Sign Up</a></p>
</div>

<?php include 'includes/footer.php'; ?>
