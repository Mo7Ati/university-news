<?php
// Prevent any HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Start session
session_start();

// Set JSON header
header('Content-Type: application/json');

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Include files with correct paths
require_once './config.php';
require_once './user_auth.php';

// Check admin role
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_article':
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $article = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($article) {
                    echo json_encode($article);
                } else {
                    echo json_encode(['error' => 'Article not found']);
                }
            } else {
                echo json_encode(['error' => 'Article ID required']);
            }
            break;
            
        case 'get_category':
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($category) {
                    echo json_encode($category);
                } else {
                    echo json_encode(['error' => 'Category not found']);
                }
            } else {
                echo json_encode(['error' => 'Category ID required']);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?> 