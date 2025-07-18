<?php
require_once '../back/config.php';
require_once '../back/get_articles.php';
require_once '../back/user_auth.php';

$newsManager = new NewsManager();
$categoryName = '';
$articles = [];


function getCategoryIcon($categoryName)
{
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

if (isset($_GET['category'])) {
    $result = $newsManager->getArticlesByCategory($_GET['category']);
    $articles = $result['articles'];
    $categoryName = $_GET['category'];
} else {
    echo "Articles For This Category Not Found";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global News Network - Latest Breaking News</title>
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
                <?php if (is_logged_in()): ?>
                    <div class="auth-buttons">
                        <span class="user-welcome">Welcome,
                            <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="logout.php" class="btn btn-outline">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-outline">Login</a>
                        <a href="register.php" class="btn btn-primary">Register</a>
                    </div>
                <?php endif; ?>
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
            <?php if (!empty($articles)): ?>
                <div class="category-header">
                    <h1><i class="<?php echo getCategoryIcon($categoryName); ?>"></i>
                        <?php echo htmlspecialchars($categoryName); ?></h1>
                </div>
                <div class="articles-list">
                    <?php foreach ($articles as $article): ?>
                        <div class="article-card" onclick="window.location.href='article.php?id=<?php echo $article['id']; ?>'">
                            <h2 class="article-title">
                                <?php echo htmlspecialchars($article['title'] ?? 'Untitled'); ?>
                            </h2>
                            <div class="article-meta">
                                <?php if (!empty($article['author'])): ?>
                                    <span class="author">By <?php echo htmlspecialchars($article['author']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($article['date'])): ?>
                                    <span class="date"> | <?php echo htmlspecialchars($article['date']); ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="article-summary">
                                <?php echo nl2br(htmlspecialchars($article['summary'] ?? $article['content'] ?? '')); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-articles">
                    <i class="fas fa-folder-open" style="font-size:2.5rem;color:#667eea;margin-bottom:0.5rem;"></i>
                    <p>No articles found for this category.</p>
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
                    <p>Your trusted source for the latest news, breaking stories, and in-depth analysis from around the
                        world.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul>
                        <li><a href="category.php?category=Politics">Politics</a></li>
                        <li><a href="category.php?category=Technology">Technology</a></li>
                        <li><a href="category.php?category=Sports">Sports</a></li>
                        <li><a href="category.php?category=Entertainment">Entertainment</a></li>
                        <li><a href="category.php?category=Business">Business</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <ul>
                        <li><i class="fas fa-envelope"></i> info@globalnews.com</li>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 News Street, Media City</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Global News Network. All rights reserved. | Designed with <i class="fas fa-heart"
                        style="color: #e74c3c;"></i> for quality journalism</p>
            </div>
        </div>
    </footer>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = 'index.php?search=' + encodeURIComponent(query);
                }
            }
        });
    </script>
</body>

</html>