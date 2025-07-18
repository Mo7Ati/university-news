<?php
require_once '../back/config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get search query
$query = trim($_GET['q'] ?? '');
$articles = [];
$error = '';
$success = false;

if (!empty($query)) {
    try {
        // Try different URL formats to connect to backend
        $possibleUrls = [
            '../back/search.php?q=' . urlencode($query),
            '/uni/back/search.php?q=' . urlencode($query),
            'http://localhost/uni/back/search.php?q=' . urlencode($query),
            'http://127.0.0.1/uni/back/search.php?q=' . urlencode($query)
        ];
        
        $jsonResponse = false;
        $backendUrl = '';
        
        foreach ($possibleUrls as $url) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            if ($response !== false) {
                $jsonResponse = $response;
                $backendUrl = $url;
                break;
            }
        }
        
        if ($jsonResponse === false) {
            $error = 'Failed to connect to search service. Please check your server configuration.';
        } else {
            $data = json_decode($jsonResponse, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error = 'Invalid response from search service.';
            } elseif (isset($data['success']) && $data['success']) {
                $articles = $data['articles'] ?? [];
                $success = true;
            } else {
                $error = $data['error'] ?? 'Search failed.';
            }
        }
    } catch (Exception $e) {
        $error = 'Search error: ' . $e->getMessage();
    }
} else {
    $error = 'Please enter a search term.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Global News Network</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="title-search">
            <div class="logo">
                <i class="fas fa-globe"></i>
                <span>Global News Network</span>
            </div>
            <div class="header-right">
                <form class="search-bar" method="get" action="search.php">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Search articles..." value="<?php echo htmlspecialchars($query); ?>" required>
                </form>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="category.php?category=Politics">Politics</a></li>
                <li><a href="category.php?category=Technology">Technology</a></li>
                <li><a href="category.php?category=Sports">Sports</a></li>
                <li><a href="category.php?category=Entertainment">Entertainment</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <main>
            <div class="category-header">
                <h1><i class="fas fa-search"></i> Search Results</h1>
                <?php if (!empty($query)): ?>
                    <p>Searching for: "<?php echo htmlspecialchars($query); ?>"</p>
                <?php endif; ?>
            </div>

            <?php if ($error): ?>
                <div class="no-articles">
                    <i class="fas fa-exclamation-triangle" style="font-size:2.5rem;color:#e74c3c;margin-bottom:0.5rem;"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <a href="index.php" class="btn btn-primary" style="margin-top:1rem;">Back to Home</a>
                </div>
            <?php elseif (empty($articles)): ?>
                <div class="no-articles">
                    <i class="fas fa-search" style="font-size:2.5rem;color:#667eea;margin-bottom:0.5rem;"></i>
                    <p>No articles found for "<?php echo htmlspecialchars($query); ?>"</p>
                    <p style="font-size:0.9rem;color:#666;margin-top:0.5rem;">Try different keywords or check your spelling.</p>
                </div>
            <?php else: ?>
                <div class="search-results-info">
                    <p>Found <?php echo count($articles); ?> article(s) for "<?php echo htmlspecialchars($query); ?>"</p>
                </div>
                <div class="articles-list">
                    <?php foreach ($articles as $article): ?>
                        <div class="article-card" onclick="window.location.href='article.php?id=<?php echo $article['id']; ?>'">
                            <h2 class="article-title">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </h2>
                            <div class="article-meta">
                                <?php if (!empty($article['author'])): ?>
                                    <span class="author">By <?php echo htmlspecialchars($article['author']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($article['category_name'])): ?>
                                    <span class="category"> | <?php echo htmlspecialchars($article['category_name']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($article['date'])): ?>
                                    <span class="date"> | <?php echo htmlspecialchars($article['date']); ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="article-summary">
                                <?php 
                                $content = $article['content'] ?? '';
                                $summary = mb_substr($content, 0, 200);
                                echo nl2br(htmlspecialchars($summary));
                                if (mb_strlen($content) > 200) {
                                    echo '...';
                                }
                                ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Global News Network</h3>
                    <p>Your trusted source for the latest news, breaking stories, and in-depth analysis from around the world.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Global News Network. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html> 