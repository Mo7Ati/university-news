<?php
// Suppress any HTML output
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once './config.php';
require_once './user_auth.php';

// Set JSON header immediately
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!is_logged_in()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = getDBConnection();

    // Check if user is admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }

    // Handle AJAX requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_article':
                if (isset($_GET['id'])) {
                    try {
                        $stmt = $pdo->prepare("
                            SELECT a.*, c.name as category_name
                            FROM articles a
                            LEFT JOIN categories c ON a.category_id = c.id
                            WHERE a.id = ?
                        ");
                        $stmt->execute([$_GET['id']]);
                        $article = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($article) {
                            echo json_encode($article);
                        } else {
                            echo json_encode(['error' => 'Article not found']);
                        }
                    } catch (Exception $e) {
                        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['error' => 'Article ID required']);
                }
                break;
                
            case 'get_category':
                if (isset($_GET['id'])) {
                    try {
                        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
                        $stmt->execute([$_GET['id']]);
                        $category = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($category) {
                            echo json_encode($category);
                        } else {
                            echo json_encode(['error' => 'Category not found']);
                        }
                    } catch (Exception $e) {
                        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['error' => 'Category ID required']);
                }
                break;
                
            default:
                echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?> 