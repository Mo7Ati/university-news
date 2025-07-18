<?php
require_once '../back/config.php';
require_once '../back/user_auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $result = login_user($username, $password);
    if ($result === true) {
        header('Location: index.php');
        exit;
    } else {
        $message = '<div class="error">' . htmlspecialchars($result) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Global News Network</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .auth-form { max-width: 400px; margin: 3rem auto; background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 12px rgba(102,126,234,0.08); }
        .auth-form h2 { text-align: center; margin-bottom: 1.5rem; color: #667eea; }
        .auth-form label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .auth-form input { width: 100%; padding: 0.7rem; margin-bottom: 1.2rem; border: 1px solid #ccc; border-radius: 6px; }
        .auth-form button { width: 100%; background: #667eea; color: #fff; border: none; padding: 0.8rem; border-radius: 6px; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .auth-form button:hover { background: #4b5fc0; }
        .success { color: #27ae60; text-align: center; margin-bottom: 1rem; }
        .error { color: #e74c3c; text-align: center; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <form class="auth-form" method="post" autocomplete="off">
        <h2>Login</h2>
        <?php if ($message) echo $message; ?>
        <label for="username">Username or Email</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
        <p style="text-align:center;margin-top:1rem;">Don't have an account? <a href="register.php">Register</a></p>
    </form>
</body>
</html> 