<?php
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the search query
$query = trim($_GET['q'] ?? '');

// Validate query
if (empty($query)) {
    echo json_encode([
        'success' => false,
        'error' => 'No search query provided',
        'articles' => []
    ]);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Prepare the search query
    $searchTerm = "%{$query}%";
    
    $stmt = $pdo->prepare("
        SELECT 
            a.id, 
            a.title, 
            a.content, 
            a.published_date as date,
            u.username as author,
            c.name as category_name
        FROM articles a
        LEFT JOIN users u ON a.author_id = u.id
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.title LIKE ? OR a.content LIKE ?
        ORDER BY a.published_date DESC
        LIMIT 20
    ");
    
    $stmt->execute([$searchTerm, $searchTerm]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return results
    echo json_encode([
        'success' => true,
        'query' => $query,
        'count' => count($articles),
        'articles' => $articles
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'articles' => []
    ]);
}
