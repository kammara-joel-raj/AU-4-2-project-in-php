<?php 
$pageTitle = "AU // ACCESS";
include 'includes/header.php'; 
require_once 'includes/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        header("Location: index.php");
        exit;
    } else {
        $error = "INVALID CREDENTIALS // ACCESS DENIED";
    }
}
?>
<style>
    .auth-container { max-width: 400px; margin: 6rem auto; padding: 2rem; border: var(--border-thick); background: #fff; }
    input { width: 100%; padding: 15px; margin-bottom: 1rem; border: 1px solid #ccc; font-family: var(--font-tech); }
    .error-msg { color: red; font-family: var(--font-tech); margin-bottom: 1rem; display: block; }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="auth-container">
    <h1 class="display-text" style="font-size: 2.5rem; margin-bottom: 2rem; text-align: center;">STUDENT LOGIN</h1>
    
    <?php if($error): ?>
        <span class="error-msg"><?php echo $error; ?></span>
    <?php endif; ?>

    <form method="POST">
        <label style="font-family: var(--font-tech); font-size: 0.8rem;">EMAIL ID</label>
        <input type="email" name="email" required>
        
        <label style="font-family: var(--font-tech); font-size: 0.8rem;">PASSWORD</label>
        <input type="password" name="password" required>
        
        <button type="submit" class="btn" style="width: 100%; background: var(--au-blue); color: var(--au-gold);">AUTHENTICATE</button>
    </form>

    <div style="margin-top: 2rem; text-align: center; font-family: var(--font-tech); font-size: 0.9rem;">
        NEW STUDENT? <a href="register.php" style="text-decoration: underline; color: var(--au-blue);">REGISTER HERE</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>