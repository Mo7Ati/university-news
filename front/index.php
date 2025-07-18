<?php
require_once '../back/config.php';
require_once '../back/get_articles.php';
require_once '../back/user_auth.php';
$newsManager = new NewsManager();
$featuredArticles = $newsManager->getFeaturedArticles(4);
$breakingNews = $newsManager->getBreakingNews();
$latestNews = $newsManager->getLatestNews();
$trendingArticles = $newsManager->getTrendingArticles(6);
$categories = $newsManager->getCategories();
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
            <!-- Breaking News -->
            <?php if (!empty($breakingNews)): ?>
                <section class="breaking-news">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="breaking-news-content">
                        <h2>BREAKING: <?php echo htmlspecialchars($breakingNews[0]['title']); ?></h2>
                    </div>
                </section>
            <?php endif; ?>
            <!-- Featured Articles -->
            <?php if (!empty($featuredArticles)): ?>
                <section class="featured-section">
                    <h2 class="section-title">Featured Articles</h2>
                    <div class="featured-grid">
                        <?php foreach ($featuredArticles as $article): ?>
                            <article class="article-card" onclick="window.location.href='article.php?id=<?php echo $article['id']; ?>'">
                                <div class="article-image">
                                    <i
                                        class="<?php echo htmlspecialchars($article['category_name'] ? getCategoryIcon($article['category_name']) : 'fas fa-newspaper'); ?>"></i>
                                </div>
                                <div class="article-content">
                                    <div class="article-category">
                                        <?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                                    </div>
                                    <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                                    <div class="article-meta">
                                        <span><i class="fas fa-clock"></i>
                                            <?php echo "2022-04-10"; ?></span>
                                        <!-- $newsManager->timeAgo($article['published_date']) -->
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Latest News -->
            <section class="latest-news">
                <h2 class="section-title">Latest News</h2>
                <div class="news-list">
                    <?php foreach ($latestNews as $article): ?>
                        <article class="news-item" onclick="window.location.href='article.php?id=<?php echo $article['id']; ?>'">
                            <div class="news-thumbnail">
                                <i class="<?php echo getCategoryIcon($article['category_name']); ?>"></i>
                            </div>
                            <div class="news-content">
                                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <!-- Categories -->
            <section class="categories-section">
                <h2 class="section-title">News Categories</h2>
                <div class="category-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card"
                            onclick="window.location.href='category.php?category=<?php echo $category['slug']; ?>'">
                            <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
        <!-- Sidebar -->
        <aside>
            <!-- Trending News -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">Trending Now</h3>
                <ul class="trending-list">
                    <?php foreach ($trendingArticles as $article): ?>
                        <li onclick="window.location.href='article.php?id=<?php echo $article['id']; ?>'"><?php echo htmlspecialchars($article['title']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- Advertisement Section -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">Sponsored Content</h3>
                <div class="ad-section">
                    <i class="fas fa-ad"></i>
                    <h4>Advertisement Space</h4>
                    <p>Your ad could be here</p>
                    <button class="btn btn-primary">Learn More</button>
                </div>
            </div>
        </aside>
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
  
</body>

</html>