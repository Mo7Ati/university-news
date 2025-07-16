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
            'SpaceX Successfully Launches New Satellite Constellation',
            'spacex-satellite-constellation',
            'SpaceX has successfully launched its latest satellite constellation, marking a significant milestone in space technology. The revolutionary satellite network promises to provide global internet coverage, bringing high-speed connectivity to remote areas around the world. This launch represents the culmination of years of research and development in satellite technology.',
            2, // Technology category
            'spacex-launch.jpg'
        ],
        [
            'New Climate Policy Framework Announced at Global Summit',
            'climate-policy-global-summit',
            'World leaders have agreed on ambitious new targets for carbon reduction, setting the stage for a sustainable future. The new climate policy framework includes comprehensive measures to reduce greenhouse gas emissions and promote renewable energy adoption worldwide.',
            1, // Politics category
            'climate-summit.jpg'
        ],
        [
            'Underdog Team Makes Historic Championship Victory',
            'underdog-team-championship-victory',
            'In an unexpected turn of events, the underdogs have claimed the championship title, creating one of the greatest sports stories of the year. The team overcame incredible odds to secure their first championship in franchise history.',
            3, // Sports category
            'championship-victory.jpg'
        ]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO articles (title, slug, content, category_id, image_url , published_date) VALUES (?, ?, ?, ?, ? , ?)");
    foreach ($sampleArticles as $article) {
        $stmt->execute([$article[0], $article[1], $article[2], $article[3], $article[4], date("Y-m-d H:i:s")]);
    }
}

// Initialize database on first run
if (!isset($_SESSION['db_initialized'])) {
    initializeDatabase();
    $_SESSION['db_initialized'] = true;
}
?>