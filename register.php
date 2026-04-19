<?php
require_once 'includes/bootstrap.php';

$pageTitle = 'AU // REGISTER';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $passwordValue = trim((string) ($_POST['password'] ?? ''));

    if ($name === '' || $email === '' || $passwordValue === '') {
        $error = 'ALL REQUIRED FIELDS MUST BE FILLED.';
    } else {
        $role = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn() === 0
            ? 'admin'
            : 'customer';

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (full_name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $name,
                $email,
                password_hash($passwordValue, PASSWORD_DEFAULT),
                $role,
                $phone !== '' ? $phone : null,
            ]);

            $success = $role === 'admin'
                ? 'REGISTRATION SUCCESSFUL // FIRST ACCOUNT PROMOTED TO ADMIN'
                : 'REGISTRATION SUCCESSFUL // PLEASE LOGIN';
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000'
                ? 'EMAIL ALREADY EXISTS IN ARCHIVES'
                : 'SYSTEM ERROR';
        }
    }
}

include 'includes/header.php';
?>
<style>
    .auth-container { max-width: 420px; margin: 6rem auto; padding: 2rem; border: var(--border-thick); background: #fff; }
    input { width: 100%; padding: 15px; margin-bottom: 1rem; border: 1px solid #ccc; font-family: var(--font-tech); }
    .msg { font-family: var(--font-tech); margin-bottom: 1rem; display: block; }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="auth-container">
    <h1 class="display-text" style="font-size: 2.5rem; margin-bottom: 2rem; text-align: center;">NEW ENTRY</h1>

    <?php if ($error): ?>
        <span class="msg" style="color: #a40000;"><?= h($error) ?></span>
    <?php endif; ?>
    <?php if ($success): ?>
        <span class="msg" style="color: #0b6e32;"><?= h($success) ?></span>
    <?php endif; ?>

    <form method="POST">
        <?= csrf_input() ?>
        <label style="font-family: var(--font-tech); font-size: 0.8rem;">FULL NAME</label>
        <input type="text" name="name" required>

        <label style="font-family: var(--font-tech); font-size: 0.8rem;">EMAIL ID</label>
        <input type="email" name="email" required>

        <label style="font-family: var(--font-tech); font-size: 0.8rem;">PHONE</label>
        <input type="text" name="phone" placeholder="Optional">

        <label style="font-family: var(--font-tech); font-size: 0.8rem;">PASSWORD</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn" style="width: 100%;">CREATE PROFILE</button>
    </form>

    <div style="margin-top: 2rem; text-align: center; font-family: var(--font-tech); font-size: 0.9rem;">
        ALREADY REGISTERED? <a href="login.php" style="text-decoration: underline; color: var(--au-blue);">LOGIN HERE</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
