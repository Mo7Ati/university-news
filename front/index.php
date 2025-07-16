<?php
require_once '../back/config.php';
require_once '../back/get_articles.php';
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
                            <article class="article-card">
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
                        <article class="news-item">
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
                        <li><?php echo htmlspecialchars($article['title']); ?></li>
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
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = '?search=' + encodeURIComponent(query);
                }
            }
        });
        // Category filtering
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        const search = urlParams.get('search');
        if (category || search) {
            // Load filtered content via AJAX
            loadFilteredContent();
        }
        function loadFilteredContent() {
            const container = document.querySelector('.featured-section');
            const loadingHtml = '<div style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
            container.innerHTML = loadingHtml;
            let url = 'news.php?action=';
            if (category) {
                url += 'category&category=' + encodeURIComponent(category);
            } else if (search) {
                url += 'search&q=' + encodeURIComponent(search);
            }
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    displayArticles(data.articles, category ? 'Category: ' + category : 'Search Results for: ' + search);
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = '<div style="text-align: center; padding: 2rem; color: red;">Error loading content</div>';
                });
        }
        function displayArticles(articles, title) {
            const container = document.querySelector('.featured-section');
            let html = '<h2 class="section-title">' + title + '</h2>';
            html += '<div class="featured-grid">';
            if (articles.length === 0) {
                html += '<div style="text-align: center; padding: 2rem; color: #666;">No articles found</div>';
            } else {
                articles.forEach(article => {
                    html += `
                        <article class="article-card">
                            <div class="article-image">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div class="article-content">
                                <div class="article-category">${article.category_name || 'Uncategorized'}</div>
                                <h3 class="article-title">${article.title}</h3>
                                <div class="article-meta">
                                    <span><i class="fas fa-clock"></i> ${formatTimeAgo(article.published_date)}</span>
                                    <span><i class="fas fa-eye"></i> ${article.views} views</span>
                                </div>
                            </div>
                        </article>
                    `;
                });
            }
            html += '</div>';
            container.innerHTML = html;
        }
        function formatTimeAgo(datetime) {
            const time = new Date(datetime);
            const now = new Date();
            const diff = Math.floor((now - time) / 1000);
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + ' minute' + (Math.floor(diff / 60) > 1 ? 's' : '') + ' ago';
            if (diff < 86400) return Math.floor(diff / 3600) + ' hour' + (Math.floor(diff / 3600) > 1 ? 's' : '') + ' ago';
            if (diff < 2592000) return Math.floor(diff / 86400) + ' day' + (Math.floor(diff / 86400) > 1 ? 's' : '') + ' ago';
            return time.toLocaleDateString();
        }
    </script>
</body>

</html>