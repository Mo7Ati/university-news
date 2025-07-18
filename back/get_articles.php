<?php
require_once 'config.php';
class NewsManager
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDBConnection();
    }

    // Get featured articles
    public function getFeaturedArticles($limit = 4)
    {
        try {
            $limit = (int) $limit;
            $stmt = $this->pdo->prepare("
                SELECT articles.*, categories.name as category_name, categories.slug as category_slug, users.username as author_name
                FROM articles 
                LEFT JOIN categories ON articles.category_id = categories.id
                LEFT JOIN users  ON articles.author_id = users.id
                WHERE articles.is_featured = 1
                ORDER BY articles.published_date DESC
                LIMIT $limit
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return $e;
        }
    }

    // Get breaking news
    public function getBreakingNews()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, c.name as category_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                where is_breaking = 1
                ORDER BY a.published_date DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
            // return [];
        }
    }

    // Get latest news with pagination
    public function getLatestNews()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                ORDER BY a.published_date DESC
            ");
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $articles;
        } catch (Exception $e) {
            return $e;
        }
    }

    // Search articles
    public function searchArticles($query, $page = 1, $perPage = 10)
    {
        try {
            $offset = ($page - 1) * $perPage;
            $searchTerm = "%$query%";

            $stmt = $this->pdo->prepare("
                SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.title LIKE ? OR a.excerpt LIKE ? OR a.content LIKE ?
                ORDER BY a.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $perPage, $offset]);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count for pagination
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM articles 
                WHERE title LIKE ? OR excerpt LIKE ? OR content LIKE ?
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $total = $stmt->fetchColumn();

            return [
                'articles' => $articles,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'current_page' => $page,
                'query' => $query
            ];
        } catch (Exception $e) {
            return ['articles' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1, 'query' => $query];
        }
    }

    // Get articles by category
    public function getArticlesByCategory($categorySlug)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                WHERE c.slug = ?
                ORDER BY a.published_date DESC
            ");
            $stmt->execute([$categorySlug]);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'articles' => $articles,
                'category' => $categorySlug
            ];
        } catch (Exception $e) {
            return $e ;//['articles' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1, 'category' => $categorySlug];
        }
    }

    // Get single article by slug
    public function getArticleBySlug($slug)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.slug = ?
            ");
            $stmt->execute([$slug]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($article) {
                // Increment view count
                $stmt = $this->pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
                $stmt->execute([$article['id']]);
            }

            return $article;
        } catch (Exception $e) {
            return null;
        }
    }

    // Get all categories
    public function getCategories()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get trending articles (most viewed)
    public function getTrendingArticles($limit = 3)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, c.name as category_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                ORDER BY a.published_date DESC
                LIMIT $limit
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return $e;
        }
    }

    // Format time ago
    public function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $time);
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $newsManager = new NewsManager();

    switch ($_GET['action']) {
        case 'search':
            $query = $_GET['q'] ?? '';
            $page = $_GET['page'] ?? 1;
            $result = $newsManager->searchArticles($query, $page);
            echo json_encode($result);
            exit;

        case 'category':
            $category = $_GET['category'] ?? '';
            $result = $newsManager->getArticlesByCategory($category);
            echo json_encode($result);
            exit;

        case 'latest':
            $page = $_GET['page'] ?? 1;
            $result = $newsManager->getLatestNews($page);
            echo json_encode($result);
            exit;
    }
}
?>