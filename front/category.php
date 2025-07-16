<?php
require_once '../back/config.php';
require_once '../back/get_articles.php';

$newsManager = new NewsManager();
$categoryName = '';
$articles = [];

if (isset($_GET['category'])) {
    $articles = $newsManager->getArticlesByCategory($_GET['category']);
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
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-globe"></i>
                <span>Global News Network</span>
            </div>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#politics">Politics</a></li>
                    <li><a href="#technology">Technology</a></li>
                    <li><a href="#sports">Sports</a></li>
                    <li><a href="#entertainment">Entertainment</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
            <div class="header-right">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search articles..." id="searchInput">
                </div>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <main class="category-main">
        <section class="category-section">
            <h2 class="section-title">
                <?php echo $categoryName ? htmlspecialchars($categoryName) : 'Category'; ?> Articles
            </h2>
            <div class="featured-grid">
                <?php if (!empty($articles)): ?>
                    <?php foreach ($articles as $article): ?>
                        <article class="article-card">
                            <div class="article-image">
                                <i class="<?php echo getCategoryIcon($categoryName); ?>"></i>
                            </div>
                            <div class="article-content">
                                <div class="article-category"><?php echo htmlspecialchars($categoryName); ?></div>
                                <h3 class="article-title">
                                    <a href="article.php?id=<?php echo urlencode($article['slug']); ?>">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h3>
                                <div class="article-meta">
                                    <span><i class="fas fa-user"></i>
                                        <?php echo htmlspecialchars($article['author_name'] ?? 'Unknown'); ?></span>
                                    <span><i class="fas fa-clock"></i>
                                        <?php echo date('M j, Y', strtotime($article['published_date'])); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding:2rem; text-align:center; color:#888;">No articles found in this category.</div>
                <?php endif; ?>
            </div>
        </section>
    </main>
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
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#privacy">Privacy Policy</a></li>
                        <li><a href="#terms">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul>
                        <li><a href="#politics">Politics</a></li>
                        <li><a href="#technology">Technology</a></li>
                        <li><a href="#sports">Sports</a></li>
                        <li><a href="#entertainment">Entertainment</a></li>
                        <li><a href="#business">Business</a></li>
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
</body>

</html>