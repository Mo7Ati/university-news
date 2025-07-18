<?php
require_once 'config.php';
session_start();

function register_user($username, $email, $password) {
    if (!$username || !$email || !$password) {
        return 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address.';
    }
    if (strlen($password) < 6) {
        return 'Password must be at least 6 characters.';
    }
    try {
        $pdo = getDBConnection();
        // Check if username or email exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            return 'Username or email already exists.';
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $hashed]);
        return true;
    } catch (Exception $e) {
        return 'Registration failed.';
    }
}

function login_user($usernameOrEmail, $password) {
    if (!$usernameOrEmail || !$password) {
        return 'All fields are required.';
    }
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        } else {
            return 'Invalid credentials.';
        }
    } catch (Exception $e) {
        return 'Login failed.';
    }
}

function logout_user() {
    session_unset();
    session_destroy();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
} 