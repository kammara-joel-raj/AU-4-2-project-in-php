<?php 
$pageTitle = "AU // REGISTER";
include 'includes/header.php'; 
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $success = "REGISTRATION SUCCESSFUL // PLEASE LOGIN";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "EMAIL ALREADY EXISTS IN ARCHIVES";
        } else {
            $error = "SYSTEM ERROR";
        }
    }
}
?>
<style>
    .auth-container { max-width: 400px; margin: 6rem auto; padding: 2rem; border: var(--border-thick); background: #fff; }
    input { width: 100%; padding: 15px; margin-bottom: 1rem; border: 1px solid #ccc; font-family: var(--font-tech); }
    .msg { font-family: var(--font-tech); margin-bottom: 1rem; display: block; }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="auth-container">
    <h1 class="display-text" style="font-size: 2.5rem; margin-bottom: 2rem; text-align: center;">NEW ENTRY</h1>
    
    <?php if($error): ?>
        <span class="msg" style="color: red;"><?php echo $error; ?></span>
    <?php endif; ?>
    <?php if($success): ?>
        <span class="msg" style="color: green;"><?php echo $success; ?></span>
    <?php endif; ?>

    <form method="POST">
        <label style="font-family: var(--font-tech); font-size: 0.8rem;">FULL NAME</label>
        <input type="text" name="name" required>

        <label style="font-family: var(--font-tech); font-size: 0.8rem;">EMAIL ID</label>
        <input type="email" name="email" required>
        
        <label style="font-family: var(--font-tech); font-size: 0.8rem;">PASSWORD</label>
        <input type="password" name="password" required>
        
        <button type="submit" class="btn" style="width: 100%;">CREATE PROFILE</button>
    </form>

    <div style="margin-top: 2rem; text-align: center; font-family: var(--font-tech); font-size: 0.9rem;">
        ALREADY REGISTERED? <a href="login.php" style="text-decoration: underline; color: var(--au-blue);">LOGIN HERE</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>