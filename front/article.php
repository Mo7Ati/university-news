<?php
$category = $_GET['category'];
require_once '../back/config.php';
require_once '../back/get_articles.php';

// Fetch article by ID or slug from GET
$newsManager = new NewsManager();
$article = null;
$comments = [];

if (isset($_GET['id'])) {
    $article = $newsManager->getArticleBySlug($_GET['id']); // You may want to use getArticleById if you have it
}

// Fetch comments for this article (placeholder, implement actual logic as needed)
if ($article && isset($article['id'])) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = ? ORDER BY c.timestamp DESC");
    $stmt->execute([$article['id']]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle comment submission (if you want to allow it)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($article['id'])) {
    $commentText = trim($_POST['comment_text'] ?? '');
    $userId = 1; // Placeholder: set to logged-in user ID or 0 for anonymous
    if ($commentText !== '') {
        $stmt = $pdo->prepare("INSERT INTO comments (article_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$article['id'], $userId, $commentText]);
        header("Location: article.php?id=" . urlencode($article['slug']));
        exit;
    }
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
    <main class="article-main">
        <?php if ($article): ?>
            <article class="article-detail">
                <div class="article-header">
                    <span class="article-category">
                        <i class="<?php echo getCategoryIcon($article['category_name']); ?>"></i>
                        <?php echo htmlspecialchars($article['category_name']); ?>
                    </span>
                    <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="article-meta">
                        <span><i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($article['author_name'] ?? 'Unknown'); ?></span>
                        <span><i class="fas fa-clock"></i>
                            <?php echo date('M j, Y', strtotime($article['published_date'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo number_format($article['views']); ?> views</span>
                    </div>
                </div>
                <div class="article-content">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
            </article>
            <section class="comments-section">
                <h2>Comments</h2>
                <?php if (!empty($comments)): ?>
                    <ul class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <li class="comment">
                                <div class="comment-meta">
                                    <span class="comment-user"><i class="fas fa-user"></i>
                                        <?php echo htmlspecialchars($comment['username'] ?? 'Anonymous'); ?></span>
                                    <span
                                        class="comment-date"><?php echo date('M j, Y H:i', strtotime($comment['timestamp'])); ?></span>
                                </div>
                                <div class="comment-text">
                                    <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No comments yet. Be the first to comment!</p>
                <?php endif; ?>
                <form class="comment-form" method="post" action="">
                    <textarea name="comment_text" placeholder="Write your comment..." required></textarea>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>
            </section>
        <?php else: ?>
            <div style="padding:2rem; text-align:center; color:#888;">Article not found.</div>
        <?php endif; ?>
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
    <!-- <script>
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
    </script> -->
</body>

</html>