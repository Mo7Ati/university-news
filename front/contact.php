<?php
require_once '../back/config.php';
require_once '../back/user_auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $message_text) {
        // In a real application, you would send an email or save to database
        $message = '<div class="success">Thank you for your message! We will get back to you soon.</div>';
    } else {
        $message = '<div class="error">Please fill in all fields.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Global News Network</title>
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
            <div class="contact-header">
                <h1><i class="fas fa-envelope"></i> Contact Us</h1>
                <p>Get in touch with our team. We'd love to hear from you!</p>
            </div>

            <div class="contact-content">
                <div class="contact-grid">
                    <!-- Contact Form -->
                    <div class="contact-form-section">
                        <h2><i class="fas fa-paper-plane"></i> Send us a Message</h2>
                        <?php if ($message) echo $message; ?>
                        <form class="contact-form" method="post">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <input type="text" id="subject" name="subject" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" rows="6" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-info-section">
                        <h2><i class="fas fa-info-circle"></i> Get in Touch</h2>
                        
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h3>Email</h3>
                                <p>info@globalnews.com</p>
                                <p>support@globalnews.com</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h3>Phone</h3>
                                <p>+1 (555) 123-4567</p>
                                <p>+1 (555) 987-6543</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h3>Address</h3>
                                <p>123 News Street<br>Media City, MC 12345<br>United States</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h3>Office Hours</h3>
                                <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                                <p>Saturday: 10:00 AM - 4:00 PM</p>
                                <p>Sunday: Closed</p>
                            </div>
                        </div>

                        <div class="social-links">
                            <h3>Follow Us</h3>
                            <div class="social-icons">
                                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Section (Placeholder) -->
                <div class="map-section">
                    <h2><i class="fas fa-map"></i> Our Location</h2>
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt"></i>
                        <p>Interactive map will be displayed here</p>
                        <p>123 News Street, Media City, MC 12345</p>
                    </div>
                </div>
            </div>
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
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
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