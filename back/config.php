<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'news_website');

// Create database connection
function getDBConnection()
{
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Initialize database tables
function initializeDatabase()
{
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        slug VARCHAR(50) UNIQUE NOT NULL,
        icon VARCHAR(50) DEFAULT 'fas fa-newspaper'
    )");

    // Create articles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        content LONGTEXT,
        category_id INT,
        author_id INT,
        is_featured BOOLEAN DEFAULT FALSE,
        is_breaking BOOLEAN DEFAULT FALSE,
        image_url VARCHAR(255),
        published_date TIMESTAMP NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    // Create comments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        comment_id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        user_id INT NOT NULL,
        comment_text TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Insert default categories
    $categories = [
        ['Politics', 'politics', 'fas fa-balance-scale'],
        ['Technology', 'technology', 'fas fa-rocket'],
        ['Sports', 'sports', 'fas fa-futbol'],
        ['Entertainment', 'entertainment', 'fas fa-film'],
        ['Business', 'business', 'fas fa-chart-line'],
        ['Health', 'health', 'fas fa-heartbeat']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, icon) VALUES (?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }

    // Create default admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@news.com', $adminPassword, 'admin']);

    // Insert sample articles
    $sampleArticles = [
        [
            'BREAKING: Major Earthquake Strikes Pacific Coast',
            'breaking-major-earthquake-pacific-coast',
            'A powerful 7.2 magnitude earthquake has struck the Pacific Coast, causing widespread damage and triggering tsunami warnings. Emergency services are responding to multiple locations as aftershocks continue to rattle the region. The earthquake epicenter was located 50 miles offshore, and coastal communities are being evacuated as a precaution.',
            1, // Politics category
            'earthquake.jpg',
            true, // Breaking news
            true  // Featured
        ],
        [
            'BREAKING: Global Tech Giant Announces Revolutionary AI Breakthrough',
            'breaking-ai-breakthrough-announcement',
            'A major technology company has announced a groundbreaking artificial intelligence breakthrough that could revolutionize multiple industries. The new AI system demonstrates unprecedented capabilities in natural language processing and problem-solving, marking a significant leap forward in artificial intelligence development.',
            2, // Technology category
            'ai-breakthrough.jpg',
            true, // Breaking news
            true  // Featured
        ],
        [
            'Revolutionary Quantum Computing Milestone Achieved',
            'quantum-computing-milestone-achieved',
            'Scientists have achieved a major milestone in quantum computing, successfully demonstrating quantum supremacy in a practical application. This breakthrough represents a significant step forward in the development of quantum computing technology and its potential applications in cryptography, drug discovery, and climate modeling.',
            2, // Technology category
            'quantum-computing.jpg',
            false, // Not breaking
            true   // Featured
        ],
        [
            'New Climate Policy Framework Announced at Global Summit',
            'climate-policy-global-summit-announcement',
            'World leaders have agreed on ambitious new targets for carbon reduction, setting the stage for a sustainable future. The new climate policy framework includes comprehensive measures to reduce greenhouse gas emissions and promote renewable energy adoption worldwide.',
            1, // Politics category
            'climate-summit.jpg',
            false, // Not breaking
            true   // Featured
        ],
        [
            'Underdog Team Makes Historic Championship Victory',
            'underdog-team-championship-victory',
            'In an unexpected turn of events, the underdogs have claimed the championship title, creating one of the greatest sports stories of the year. The team overcame incredible odds to secure their first championship in franchise history.',
            3, // Sports category
            'championship-victory.jpg',
            false, // Not breaking
            false  // Not featured
        ],
        [
            'Major Film Studio Announces Groundbreaking Virtual Reality Movie',
            'virtual-reality-movie-announcement',
            'A major Hollywood studio has announced plans to produce the first-ever feature-length virtual reality movie, marking a revolutionary moment in the entertainment industry. The project will allow viewers to experience the story from multiple perspectives and interact with the narrative in unprecedented ways.',
            4, // Entertainment category
            'vr-movie.jpg',
            false, // Not breaking
            true   // Featured
        ],
        [
            'Global Economy Shows Strong Recovery Signs',
            'global-economy-recovery-signs',
            'Recent economic data indicates that the global economy is showing strong signs of recovery, with growth rates exceeding expectations across major markets. This positive trend has been attributed to several factors, including increased consumer spending, technological innovation, and improved international trade relations.',
            5, // Business category
            'economy-recovery.jpg',
            false, // Not breaking
            false  // Not featured
        ],
        [
            'Breakthrough in Renewable Energy Storage Technology',
            'renewable-energy-storage-breakthrough',
            'Scientists have announced a major breakthrough in renewable energy storage technology that could solve one of the biggest challenges facing the clean energy industry. The new battery technology offers significantly higher energy density and longer lifespan than current solutions.',
            2, // Technology category
            'energy-storage.jpg',
            false, // Not breaking
            false  // Not featured
        ],
        [
            'New Study Reveals Benefits of Mediterranean Diet',
            'mediterranean-diet-study-reveals',
            'A comprehensive study has revealed significant health benefits associated with the Mediterranean diet, including reduced risk of heart disease and improved cognitive function. The research involved over 10,000 participants and provides strong evidence for the diet\'s effectiveness.',
            6, // Health category
            'mediterranean-diet.jpg',
            false, // Not breaking
            false  // Not featured
        ],
        [
            'Olympic Committee Announces New Sports for Next Games',
            'olympic-new-sports-announcement',
            'The International Olympic Committee has announced the addition of several new sports to the program for the next Olympic Games, reflecting the evolving nature of sports and the desire to appeal to younger audiences. The new additions include esports, skateboarding, and sport climbing.',
            3, // Sports category
            'olympic-sports.jpg',
            false, // Not breaking
            false  // Not featured
        ]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO articles (title, slug, content, category_id, image_url, is_breaking, is_featured, published_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($sampleArticles as $article) {
        $stmt->execute([$article[0], $article[1], $article[2], $article[3], $article[4], $article[5], $article[6], date("Y-m-d H:i:s")]);
    }
}

// Initialize database on first run
if (!isset($_SESSION['db_initialized'])) {
    initializeDatabase();
    $_SESSION['db_initialized'] = true;
}
?>