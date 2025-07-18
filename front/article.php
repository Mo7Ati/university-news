<?php
require_once '../back/config.php';
require_once '../back/get_articles.php';

$articleId = $_GET['id'] ?? null;
$article = null;
$error = '';

if ($articleId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT 
                a.*,
                u.username as author_name,
                c.name as category_name,
                c.slug as category_slug
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.id = ?
        ");
        $stmt->execute([$articleId]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$article) {
            $error = 'Article not found.';
        }
    } catch (Exception $e) {
        $error = 'Failed to load article.';
    }
} else {
    $error = 'No article specified.';
}

function getCategoryIcon($categoryName) {
    $icons = [
        'Politics' => 'fas fa-balance-scale',
        'Technology' => 'fas fa-rocket',
        'Sports' => 'fas fa-futbol',
        'Entertainment' => 'fas fa-film',
        'Business' => 'fas fa-chart-line',
        'Health' => 'fas fa-heartbeat'
    ];
    return $icons[$categoryName] ?? 'fas fa-newspaper';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article['title']) . ' - ' : ''; ?>Global News Network</title>
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
                    <input type="text" name="q" placeholder="Search articles..." required>
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
            <?php if ($error): ?>
                <div class="no-articles">
                    <i class="fas fa-exclamation-triangle" style="font-size:2.5rem;color:#e74c3c;margin-bottom:0.5rem;"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <a href="index.php" class="btn btn-primary" style="margin-top:1rem;">Back to Home</a>
                </div>
            <?php elseif ($article): ?>
                <!-- Article Header -->
                <div class="article-header">
                    <div class="article-category-badge">
                        <i class="<?php echo getCategoryIcon($article['category_name']); ?>"></i>
                        <span><?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?></span>
                    </div>
                    <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="article-meta">
                        <?php if (!empty($article['author_name'])): ?>
                            <span class="author">
                                <i class="fas fa-user"></i>
                                By <?php echo htmlspecialchars($article['author_name']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($article['published_date'])): ?>
                            <span class="date">
                                <i class="fas fa-calendar"></i>
                                <?php echo htmlspecialchars($article['published_date']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($article['views'])): ?>
                            <span class="views">
                                <i class="fas fa-eye"></i>
                                <?php echo htmlspecialchars($article['views']); ?> views
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Article Content -->
                <div class="article-content">
                    <?php if (!empty($article['excerpt'])): ?>
                        <div class="article-excerpt">
                            <p><?php echo nl2br(htmlspecialchars($article['excerpt'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="article-body">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>
                </div>

                <!-- Article Footer -->
                <div class="article-footer">
                    <div class="article-tags">
                        <span class="tag-label">Tags:</span>
                        <span class="tag"><?php echo htmlspecialchars($article['category_name'] ?? 'News'); ?></span>
                    </div>
                    <div class="article-actions">
                        <button class="btn btn-outline" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="btn btn-outline" onclick="shareArticle()">
                            <i class="fas fa-share"></i> Share
                        </button>
                    </div>
                </div>

                <!-- Related Articles (placeholder) -->
                <div class="related-articles">
                    <h3>Related Articles</h3>
                    <p>More articles from this category will appear here.</p>
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

    <script>
        function shareArticle() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo $article ? addslashes($article['title']) : ''; ?>',
                    url: window.location.href
                });
            } else {
                // Fallback: copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Article URL copied to clipboard!');
                });
            }
        }
    </script>
</body>
</html>