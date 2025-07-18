-- Global News Network Database
-- Created for the news website project
-- This file contains the complete database structure and sample data

-- Create database
CREATE DATABASE IF NOT EXISTS news_website;
USE news_website;

-- Drop existing tables if they exist (for clean installation)
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-newspaper',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create articles table
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT,
    summary TEXT,
    excerpt TEXT,
    category_id INT,
    author_id INT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_breaking BOOLEAN DEFAULT FALSE,
    image_url VARCHAR(255),
    views INT DEFAULT 0,
    published_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create comments table
CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, slug, icon, description) VALUES
('Politics', 'politics', 'fas fa-balance-scale', 'Latest political news, government updates, and policy changes'),
('Technology', 'technology', 'fas fa-rocket', 'Tech innovations, software updates, and digital trends'),
('Sports', 'sports', 'fas fa-futbol', 'Sports news, match results, and athlete updates'),
('Entertainment', 'entertainment', 'fas fa-film', 'Movie reviews, celebrity news, and entertainment updates'),
('Business', 'business', 'fas fa-chart-line', 'Business news, market updates, and economic analysis'),
('Health', 'health', 'fas fa-heartbeat', 'Health news, medical research, and wellness tips');

-- Create default admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@news.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Create sample users
INSERT INTO users (username, email, password, role) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample articles
INSERT INTO articles (title, slug, content, summary, category_id, author_id, is_featured, is_breaking, image_url, published_date) VALUES
(
    'SpaceX Successfully Launches New Satellite Constellation',
    'spacex-satellite-constellation',
    'SpaceX has successfully launched its latest satellite constellation, marking a significant milestone in space technology. The revolutionary satellite network promises to provide global internet coverage, bringing high-speed connectivity to remote areas around the world. This launch represents the culmination of years of research and development in satellite technology.\n\nThe constellation consists of over 1,000 satellites that will work together to provide internet access to even the most remote locations on Earth. This technology has the potential to bridge the digital divide and bring internet access to billions of people who currently lack reliable connectivity.\n\nElon Musk, CEO of SpaceX, stated that this launch represents a major step forward in the company\'s mission to make space technology more accessible and beneficial to humanity. The satellites are equipped with advanced technology that allows them to communicate with ground stations and provide high-speed internet access.\n\nEnvironmental groups have raised concerns about the impact of satellite constellations on astronomical observations, but SpaceX has implemented measures to reduce the reflectivity of their satellites and minimize their impact on the night sky.\n\nThe successful launch has been met with enthusiasm from the technology community, with many experts predicting that this could revolutionize internet access worldwide. The constellation is expected to be fully operational within the next few months.',
    'SpaceX launches revolutionary satellite constellation to provide global internet coverage, marking a major milestone in space technology.',
    2, 1, TRUE, TRUE, 'spacex-launch.jpg', NOW() - INTERVAL 2 HOUR
),
(
    'New Climate Policy Framework Announced at Global Summit',
    'climate-policy-global-summit',
    'World leaders have agreed on ambitious new targets for carbon reduction, setting the stage for a sustainable future. The new climate policy framework includes comprehensive measures to reduce greenhouse gas emissions and promote renewable energy adoption worldwide.\n\nThe summit, attended by representatives from over 190 countries, resulted in a historic agreement that commits nations to reducing their carbon emissions by 50% by 2030. This represents a significant increase from previous targets and demonstrates a renewed commitment to addressing climate change.\n\nKey provisions of the new framework include:\n- Mandatory carbon pricing for major industries\n- Increased investment in renewable energy infrastructure\n- Stricter regulations on fossil fuel emissions\n- Support for developing nations in their transition to clean energy\n\nEnvironmental activists have praised the agreement as a major step forward, while some industry representatives have expressed concerns about the economic impact of the new regulations. However, most experts agree that the long-term benefits of addressing climate change far outweigh the short-term costs.\n\nThe framework also includes provisions for regular review and adjustment of targets based on scientific evidence and technological advances. This flexible approach ensures that the agreement can adapt to changing circumstances and new developments in climate science.',
    'Global leaders agree on ambitious new climate targets, committing to 50% carbon reduction by 2030.',
    1, 1, TRUE, FALSE, 'climate-summit.jpg', NOW() - INTERVAL 4 HOUR
),
(
    'Underdog Team Makes Historic Championship Victory',
    'underdog-team-championship-victory',
    'In an unexpected turn of events, the underdogs have claimed the championship title, creating one of the greatest sports stories of the year. The team overcame incredible odds to secure their first championship in franchise history.\n\nThe victory came after a thrilling final game that went into overtime, with the winning goal scored in the final seconds of the match. The team\'s journey to the championship was marked by determination, teamwork, and an unshakeable belief in their ability to succeed against all odds.\n\nCoach Sarah Johnson, who led the team to victory, credited the win to the players\' dedication and the support of their loyal fans. "This victory belongs to everyone who believed in us when no one else did," she said in her post-game interview.\n\nThe team\'s success has inspired sports fans around the world and serves as a reminder that anything is possible with hard work and determination. The victory parade is scheduled for next week, with thousands of fans expected to attend the celebration.\n\nThis championship victory has also had a significant impact on the local community, with businesses reporting increased sales and a renewed sense of pride among residents. The team\'s success has brought the community together and created lasting memories for fans of all ages.',
    'Underdog team defies all odds to win their first championship in franchise history.',
    3, 2, FALSE, TRUE, 'championship-victory.jpg', NOW() - INTERVAL 6 HOUR
),
(
    'Revolutionary AI Breakthrough in Medical Diagnosis',
    'ai-medical-diagnosis-breakthrough',
    'Scientists have announced a groundbreaking development in artificial intelligence that could revolutionize medical diagnosis. The new AI system has demonstrated unprecedented accuracy in detecting various diseases from medical imaging, potentially saving countless lives through early detection.\n\nThe AI system, developed by a team of researchers from leading universities and medical institutions, uses advanced machine learning algorithms to analyze medical images such as X-rays, MRIs, and CT scans. In clinical trials, the system achieved accuracy rates of over 95% in detecting various conditions, including cancer, heart disease, and neurological disorders.\n\nDr. Emily Chen, lead researcher on the project, explained that the AI system works by learning from millions of medical images and their corresponding diagnoses. "The system can identify patterns and anomalies that might be missed by human doctors, especially in early stages of disease," she said.\n\nThe technology has already been approved for use in several hospitals and is expected to be widely adopted within the next few years. However, researchers emphasize that the AI system is designed to assist doctors rather than replace them, providing a second opinion and helping to reduce diagnostic errors.\n\nPrivacy and security measures have been implemented to ensure that patient data is protected. The system operates under strict guidelines and is compliant with all relevant healthcare regulations.',
    'New AI system achieves 95% accuracy in medical diagnosis, potentially revolutionizing healthcare.',
    2, 3, TRUE, FALSE, 'ai-medical.jpg', NOW() - INTERVAL 8 HOUR
),
(
    'Major Film Studio Announces Groundbreaking Virtual Reality Movie',
    'virtual-reality-movie-announcement',
    'A major Hollywood studio has announced plans to produce the first-ever feature-length virtual reality movie, marking a revolutionary moment in the entertainment industry. The project, which has been in development for over two years, will allow viewers to experience the story from multiple perspectives and interact with the narrative in unprecedented ways.\n\nThe VR movie, titled "Beyond Reality," will be directed by acclaimed filmmaker James Cameron and will feature an all-star cast. The production will use cutting-edge VR technology to create an immersive experience that goes beyond traditional filmmaking.\n\n"Virtual reality represents the future of storytelling," said Cameron in a press conference. "This technology allows us to create experiences that are more engaging and emotionally powerful than anything we\'ve seen before."\n\nThe movie will be released in multiple formats, including traditional theaters for viewers who prefer the classic movie-going experience, and VR headsets for those who want the full immersive experience. The studio has partnered with major VR headset manufacturers to ensure compatibility across different platforms.\n\nProduction is scheduled to begin next month, with a release date set for the following year. The project has already generated significant buzz in the entertainment industry, with many experts predicting that it could change the way movies are made and experienced.',
    'Hollywood studio announces first feature-length VR movie, revolutionizing entertainment.',
    4, 2, FALSE, FALSE, 'vr-movie.jpg', NOW() - INTERVAL 10 HOUR
),
(
    'Global Economy Shows Strong Recovery Signs',
    'global-economy-recovery-signs',
    'Recent economic data indicates that the global economy is showing strong signs of recovery, with growth rates exceeding expectations across major markets. This positive trend has been attributed to several factors, including increased consumer spending, technological innovation, and improved international trade relations.\n\nKey indicators of the recovery include:\n- Rising employment rates in major economies\n- Increased consumer confidence and spending\n- Strong performance in technology and renewable energy sectors\n- Improved international trade flows\n\nEconomists have noted that the recovery has been particularly strong in emerging markets, which are showing resilience and adaptability in the face of global challenges. The technology sector continues to be a major driver of economic growth, with companies investing heavily in innovation and digital transformation.\n\nHowever, experts caution that the recovery is not uniform across all regions and sectors. Some areas continue to face challenges, and there are concerns about inflation and supply chain disruptions. Policymakers are working to address these issues while maintaining the momentum of the recovery.\n\nThe International Monetary Fund has revised its growth projections upward, reflecting the stronger-than-expected performance. This positive outlook has boosted investor confidence and contributed to strong performance in global financial markets.',
    'Global economy shows strong recovery with growth rates exceeding expectations.',
    5, 1, TRUE, FALSE, 'economy-recovery.jpg', NOW() - INTERVAL 12 HOUR
),
(
    'Breakthrough in Renewable Energy Storage Technology',
    'renewable-energy-storage-breakthrough',
    'Scientists have announced a major breakthrough in renewable energy storage technology that could solve one of the biggest challenges facing the clean energy industry. The new battery technology offers significantly higher energy density and longer lifespan than current solutions.\n\nThe breakthrough, developed by researchers at a leading university, uses a novel combination of materials and design principles to create batteries that can store more energy while lasting longer. This technology could make renewable energy sources like solar and wind more practical and cost-effective.\n\nDr. Michael Rodriguez, lead researcher on the project, explained that the new technology addresses the intermittency problem that has limited the widespread adoption of renewable energy. "With this breakthrough, we can store excess energy generated during peak production periods and use it when needed," he said.\n\nThe technology has already attracted interest from major energy companies and investors, with several partnerships announced to bring the technology to market. The researchers estimate that the new batteries could be commercially available within the next three to five years.\n\nEnvironmental groups have praised the development as a crucial step toward a sustainable energy future. The technology could significantly reduce reliance on fossil fuels and help meet global climate goals.',
    'New battery technology breakthrough could revolutionize renewable energy storage.',
    2, 3, FALSE, TRUE, 'energy-storage.jpg', NOW() - INTERVAL 14 HOUR
),
(
    'Olympic Committee Announces New Sports for Next Games',
    'olympic-new-sports-announcement',
    'The International Olympic Committee has announced the addition of several new sports to the program for the next Olympic Games, reflecting the evolving nature of sports and the desire to appeal to younger audiences. The new additions include esports, skateboarding, and sport climbing.\n\nThe decision was made after extensive consultation with athletes, sports federations, and the public. The new sports were chosen based on their global popularity, particularly among younger generations, and their potential to attract new audiences to the Olympic movement.\n\nEsports, which will make its Olympic debut, will feature popular video games that require skill, strategy, and teamwork. The inclusion of esports represents a recognition of the growing importance of digital sports and their appeal to a global audience.\n\nSkateboarding and sport climbing, which were introduced in the previous Olympic Games, will return with expanded programs. These sports have proven to be popular with audiences and have helped attract younger viewers to the Olympics.\n\nThe announcement has been met with enthusiasm from athletes and fans of the new sports, while some traditionalists have expressed concerns about the direction of the Olympic movement. However, Olympic officials emphasize that the addition of new sports helps keep the Games relevant and engaging for modern audiences.',
    'Olympic Committee adds esports and other new sports to the next Games program.',
    3, 2, FALSE, FALSE, 'olympic-sports.jpg', NOW() - INTERVAL 16 HOUR
),
(
    'Major Healthcare Reform Bill Passes in Congress',
    'healthcare-reform-bill-passes',
    'A comprehensive healthcare reform bill has been passed by Congress, marking a significant milestone in efforts to improve the nation\'s healthcare system. The bill includes provisions to expand access to healthcare, reduce costs, and improve the quality of care for millions of Americans.\n\nThe reform package includes several key components:\n- Expansion of healthcare coverage to more Americans\n- Measures to reduce prescription drug costs\n- Investment in healthcare infrastructure and technology\n- Provisions to address healthcare disparities\n\nSupporters of the bill argue that it will make healthcare more accessible and affordable for millions of people, particularly those who have been underserved by the current system. The bill also includes measures to address the rising cost of prescription drugs, which has been a major concern for many Americans.\n\nOpponents have raised concerns about the cost of the reforms and their potential impact on the healthcare industry. However, supporters point to studies showing that the long-term benefits of improved healthcare access outweigh the initial costs.\n\nThe bill\'s passage represents the culmination of years of debate and negotiation on healthcare reform. It is expected to have a significant impact on the healthcare landscape and will be closely watched by other countries considering similar reforms.',
    'Congress passes major healthcare reform bill to expand access and reduce costs.',
    1, 1, TRUE, FALSE, 'healthcare-reform.jpg', NOW() - INTERVAL 18 HOUR
),
(
    'Revolutionary Quantum Computing Milestone Achieved',
    'quantum-computing-milestone',
    'Researchers have achieved a major milestone in quantum computing, successfully demonstrating quantum supremacy in a practical application. This breakthrough represents a significant step forward in the development of quantum computing technology and its potential applications.\n\nThe achievement involved solving a complex computational problem that would take traditional supercomputers thousands of years to complete, while the quantum computer solved it in just a few minutes. This demonstration of quantum supremacy has been hailed as a historic moment in computer science.\n\nDr. Lisa Thompson, lead researcher on the project, explained that this breakthrough opens up new possibilities for solving complex problems in fields such as cryptography, drug discovery, and climate modeling. "Quantum computing has the potential to revolutionize many areas of science and technology," she said.\n\nThe research team used a quantum computer with 53 qubits to achieve this milestone. The technology is still in its early stages, but this demonstration shows that quantum computing is moving from theory to practical application.\n\nMajor technology companies and research institutions have already begun investing heavily in quantum computing research, recognizing its potential to transform various industries. The breakthrough has also attracted attention from government agencies and defense organizations.',
    'Quantum computing achieves major milestone, demonstrating practical quantum supremacy.',
    2, 3, FALSE, TRUE, 'quantum-computing.jpg', NOW() - INTERVAL 20 HOUR
);

-- Insert sample comments
INSERT INTO comments (article_id, user_id, comment_text, is_approved, timestamp) VALUES
(1, 2, 'This is incredible! The potential for global internet access is amazing.', TRUE, NOW() - INTERVAL 1 HOUR),
(1, 3, 'I wonder how this will affect existing internet providers.', TRUE, NOW() - INTERVAL 30 MINUTE),
(2, 2, 'Finally, some real action on climate change!', TRUE, NOW() - INTERVAL 2 HOUR),
(3, 3, 'What an inspiring story! Never give up on your dreams.', TRUE, NOW() - INTERVAL 3 HOUR),
(4, 2, 'AI in healthcare is the future. This could save so many lives.', TRUE, NOW() - INTERVAL 4 HOUR),
(5, 3, 'VR movies sound amazing! Can\'t wait to experience this.', TRUE, NOW() - INTERVAL 5 HOUR);

-- Create indexes for better performance
CREATE INDEX idx_articles_category ON articles(category_id);
CREATE INDEX idx_articles_author ON articles(author_id);
CREATE INDEX idx_articles_published ON articles(published_date);
CREATE INDEX idx_articles_featured ON articles(is_featured);
CREATE INDEX idx_articles_breaking ON articles(is_breaking);
CREATE INDEX idx_comments_article ON comments(article_id);
CREATE INDEX idx_comments_user ON comments(user_id);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_categories_slug ON categories(slug);

-- Create views for common queries
CREATE VIEW featured_articles_view AS
SELECT 
    a.id,
    a.title,
    a.slug,
    a.summary,
    a.image_url,
    a.published_date,
    a.views,
    c.name as category_name,
    c.slug as category_slug,
    u.username as author_name
FROM articles a
LEFT JOIN categories c ON a.category_id = c.id
LEFT JOIN users u ON a.author_id = u.id
WHERE a.is_featured = TRUE
ORDER BY a.published_date DESC;

CREATE VIEW breaking_news_view AS
SELECT 
    a.id,
    a.title,
    a.slug,
    a.summary,
    a.image_url,
    a.published_date,
    c.name as category_name
FROM articles a
LEFT JOIN categories c ON a.category_id = c.id
WHERE a.is_breaking = TRUE
ORDER BY a.published_date DESC;

CREATE VIEW article_stats_view AS
SELECT 
    c.name as category_name,
    COUNT(a.id) as article_count,
    AVG(a.views) as avg_views,
    MAX(a.published_date) as latest_article
FROM categories c
LEFT JOIN articles a ON c.id = a.category_id
GROUP BY c.id, c.name
ORDER BY article_count DESC;

-- Insert additional sample data for testing
INSERT INTO articles (title, slug, content, summary, category_id, author_id, is_featured, is_breaking, image_url, published_date) VALUES
(
    'New Study Reveals Benefits of Mediterranean Diet',
    'mediterranean-diet-study',
    'A comprehensive study has revealed significant health benefits associated with the Mediterranean diet, including reduced risk of heart disease and improved cognitive function.',
    'Study shows Mediterranean diet reduces heart disease risk and improves brain health.',
    6, 2, FALSE, FALSE, 'mediterranean-diet.jpg', NOW() - INTERVAL 24 HOUR
),
(
    'Tech Giant Announces Revolutionary Smart Home System',
    'smart-home-system-announcement',
    'A major technology company has unveiled a revolutionary smart home system that integrates all household devices through artificial intelligence.',
    'New AI-powered smart home system promises to revolutionize home automation.',
    2, 3, FALSE, FALSE, 'smart-home.jpg', NOW() - INTERVAL 26 HOUR
),
(
    'Local Business Community Celebrates Record Growth',
    'local-business-record-growth',
    'The local business community is celebrating record growth this quarter, with new businesses opening and existing ones expanding their operations.',
    'Local businesses report record growth and expansion in latest quarter.',
    5, 1, FALSE, FALSE, 'business-growth.jpg', NOW() - INTERVAL 28 HOUR
);

-- Grant permissions (adjust as needed for your setup)
-- GRANT ALL PRIVILEGES ON news_website.* TO 'root'@'localhost';
-- FLUSH PRIVILEGES;

-- Display database information
SELECT 'Database created successfully!' as status;
SELECT COUNT(*) as total_articles FROM articles;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_categories FROM categories;
SELECT COUNT(*) as total_comments FROM comments; 