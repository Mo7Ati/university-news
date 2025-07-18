<?php
require_once '../back/config.php';
require_once '../back/user_auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Global News Network</title>
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
            <div class="about-header">
                <h1><i class="fas fa-info-circle"></i> About Global News Network</h1>
                <p>Your trusted source for the latest news, breaking stories, and in-depth analysis from around the
                    world.</p>
            </div>

            <div class="about-content">
                <section class="about-section">
                    <h2><i class="fas fa-bullseye"></i> Our Mission</h2>
                    <p>At Global News Network, we are committed to delivering accurate, timely, and comprehensive news
                        coverage to our readers worldwide. Our mission is to provide unbiased reporting that empowers
                        individuals to make informed decisions about the world around them.</p>
                    <p>We believe in the power of journalism to inform, educate, and inspire positive change in society.
                        Through our dedicated team of journalists and editors, we strive to maintain the highest
                        standards of journalistic integrity and excellence.</p>
                </section>

                <section class="about-section">
                    <h2><i class="fas fa-history"></i> Our Story</h2>
                    <p>Founded in 2024, Global News Network has grown from a small local news outlet to a comprehensive
                        digital news platform serving millions of readers globally. Our journey began with a simple
                        vision: to create a news source that prioritizes truth, transparency, and trust.</p>
                    <p>Over the years, we have expanded our coverage to include politics, technology, sports,
                        entertainment, business, and health, ensuring that our readers have access to diverse
                        perspectives on the issues that matter most.</p>
                </section>

                <section class="about-section">
                    <h2><i class="fas fa-users"></i> Our Team</h2>
                    <div class="team-grid">
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3>Editorial Team</h3>
                            <p>Our experienced editors ensure that every story meets our high standards for accuracy and
                                quality.</p>
                        </div>
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-camera"></i>
                            </div>
                            <h3>Journalists</h3>
                            <p>Dedicated reporters working around the clock to bring you the latest news and breaking
                                stories.</p>
                        </div>
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-code"></i>
                            </div>
                            <h3>Technology Team</h3>
                            <p>Innovative developers and designers creating the best digital experience for our readers.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="about-section">
                    <h2><i class="fas fa-chart-line"></i> Our Values</h2>
                    <div class="values-grid">
                        <div class="value-item">
                            <i class="fas fa-shield-alt"></i>
                            <h3>Integrity</h3>
                            <p>We maintain the highest ethical standards in all our reporting and operations.</p>
                        </div>
                        <div class="value-item">
                            <i class="fas fa-balance-scale"></i>
                            <h3>Objectivity</h3>
                            <p>We present news without bias, allowing readers to form their own informed opinions.</p>
                        </div>
                        <div class="value-item">
                            <i class="fas fa-clock"></i>
                            <h3>Timeliness</h3>
                            <p>We deliver news as it happens, ensuring our readers stay informed in real-time.</p>
                        </div>
                        <div class="value-item">
                            <i class="fas fa-globe"></i>
                            <h3>Global Perspective</h3>
                            <p>We cover stories from around the world, providing diverse viewpoints and insights.</p>
                        </div>
                    </div>
                </section>

                <section class="about-section">
                    <h2><i class="fas fa-envelope"></i> Contact Information</h2>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h3>Email</h3>
                                <p>info@globalnews.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h3>Phone</h3>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h3>Address</h3>
                                <p>123 News Street, Media City, MC 12345</p>
                            </div>
                        </div>
                    </div>
                    <div class="contact-cta">
                        <p>Have questions or want to get in touch? We'd love to hear from you!</p>
                        <a href="contact.php" class="btn btn-primary">Contact Us</a>
                    </div>
                </section>
            </div>
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