<?php
require_once '../back/config.php';
require_once '../back/get_articles.php';
require_once '../back/user_auth.php';

// Check if user is logged in and is admin
if (!is_logged_in()) {
    header('Location: ../front/login.php');
    exit;
}

$pdo = getDBConnection();
$newsManager = new NewsManager();

// Check if user is admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    header('Location: ../front/index.php');
    exit;
}

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_article':
                try {
                    $slug = strtolower(str_replace(' ', '-', $_POST['title']));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO articles (title, slug, content, category_id, author_id, is_featured, is_breaking, published_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['title'],
                        $slug,
                        $_POST['content'],
                        $_POST['category_id'],
                        $_SESSION['user_id'],
                        isset($_POST['is_featured']) ? 1 : 0,
                        isset($_POST['is_breaking']) ? 1 : 0,
                        date('Y-m-d H:i:s')
                    ]);
                    $message = "Article created successfully!";
                } catch (Exception $e) {
                    $error = "Error creating article: " . $e->getMessage();
                }
                break;

            case 'update_article':
                try {
                    $slug = strtolower(str_replace(' ', '-', $_POST['title']));
                    
                    $stmt = $pdo->prepare("
                        UPDATE articles 
                        SET title = ?, slug = ?, content = ?, category_id = ?, is_featured = ?, is_breaking = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['title'],
                        $slug,
                        $_POST['content'],
                        $_POST['category_id'],
                        isset($_POST['is_featured']) ? 1 : 0,
                        isset($_POST['is_breaking']) ? 1 : 0,
                        $_POST['article_id']
                    ]);
                    $message = "Article updated successfully!";
                } catch (Exception $e) {
                    $error = "Error updating article: " . $e->getMessage();
                }
                break;

            case 'delete_article':
                try {
                    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
                    $stmt->execute([$_POST['article_id']]);
                    $message = "Article deleted successfully!";
                } catch (Exception $e) {
                    $error = "Error deleting article: " . $e->getMessage();
                }
                break;

            case 'create_category':
                try {
                    $slug = strtolower(str_replace(' ', '-', $_POST['name']));
                    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon) VALUES (?, ?, ?)");
                    $stmt->execute([$_POST['name'], $slug, $_POST['icon']]);
                    $message = "Category created successfully!";
                } catch (Exception $e) {
                    $error = "Error creating category: " . $e->getMessage();
                }
                break;

            case 'update_category':
                try {
                    $slug = strtolower(str_replace(' ', '-', $_POST['name']));
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, icon = ? WHERE id = ?");
                    $stmt->execute([$_POST['name'], $slug, $_POST['icon'], $_POST['category_id']]);
                    $message = "Category updated successfully!";
                } catch (Exception $e) {
                    $error = "Error updating category: " . $e->getMessage();
                }
                break;

            case 'delete_category':
                try {
                    // Check if category has articles
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
                    $stmt->execute([$_POST['category_id']]);
                    $articleCount = $stmt->fetchColumn();

                    if ($articleCount > 0) {
                        $error = "Cannot delete category: It has {$articleCount} article(s). Please reassign or delete the articles first.";
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                        $stmt->execute([$_POST['category_id']]);
                        $message = "Category deleted successfully!";
                    }
                } catch (Exception $e) {
                    $error = "Error deleting category: " . $e->getMessage();
                }
                break;

            case 'delete_user':
                try {
                    if ($_POST['user_id'] != $_SESSION['user_id']) {
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$_POST['user_id']]);
                        $message = "User deleted successfully!";
                    } else {
                        $error = "You cannot delete your own account!";
                    }
                } catch (Exception $e) {
                    $error = "Error deleting user: " . $e->getMessage();
                }
                break;
        }
    }
}

// Get statistics
$stats = [];
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM articles");
$stmt->execute();
$stats['total_articles'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
$stmt->execute();
$stats['total_users'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM comments");
$stmt->execute();
$stats['total_comments'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM articles WHERE is_featured = 1");
$stmt->execute();
$stats['featured_articles'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM articles WHERE is_breaking = 1");
$stmt->execute();
$stats['breaking_news'] = $stmt->fetchColumn();

// Get recent articles
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, u.username as author_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    ORDER BY a.published_date DESC
    LIMIT 10
");
$stmt->execute();
$recent_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all articles for management
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, u.username as author_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    ORDER BY a.published_date    DESC
");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$categories = $newsManager->getCategories();

// Get users
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get comments
$stmt = $pdo->prepare("
    SELECT c.*, a.title as article_title, u.username
    FROM comments c
    LEFT JOIN articles a ON c.article_id = a.id
    LEFT JOIN users u ON c.user_id = u.id
    ORDER BY c.timestamp DESC
    LIMIT 20
");
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Global News Network</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <header class="admin-header">
        <div class="admin-container">
            <nav class="admin-nav">
                <h1><i class="fas fa-cog"></i> Admin Dashboard</h1>
                <div>
                    <a href="index.php"><i class="fas fa-home"></i> View Site</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="admin-content">
        <div class="admin-container">
            <?php if (isset($message)): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Dashboard Statistics -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <i class="fas fa-newspaper"></i>
                    <h3><?php echo $stats['total_articles']; ?></h3>
                    <p>Total Articles</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $stats['total_users']; ?></h3>
                    <p>Registered Users</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-comments"></i>
                    <h3><?php echo $stats['total_comments']; ?></h3>
                    <p>Total Comments</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-star"></i>
                    <h3><?php echo $stats['featured_articles']; ?></h3>
                    <p>Featured Articles</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3><?php echo $stats['breaking_news']; ?></h3>
                    <p>Breaking News</p>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-container">
                <div class="tab-buttons">
                    <button class="tab-button active" onclick="showTab('articles')">
                        <i class="fas fa-newspaper"></i> Articles
                    </button>
                    <button class="tab-button" onclick="showTab('users')">
                        <i class="fas fa-users"></i> Users
                    </button>
                    <button class="tab-button" onclick="showTab('comments')">
                        <i class="fas fa-comments"></i> Comments
                    </button>
                    <button class="tab-button" onclick="showTab('categories')">
                        <i class="fas fa-tags"></i> Categories
                    </button>
                </div>

                <!-- Articles Tab -->
                <div id="articles" class="tab-content active">
                    <!-- Create New Article -->
                    <div class="admin-section">
                        <div class="section-header">
                            <h2><i class="fas fa-plus"></i> Create New Article</h2>
                        </div>
                        <div class="section-content">
                            <form method="POST">
                                <input type="hidden" name="action" value="create_article">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" id="title" name="title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">Category</label>
                                        <select id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <textarea id="content" name="content" required
                                        placeholder="Write your article content here..."></textarea>
                                </div>

                                <div class="checkbox-group">
                                    <input type="checkbox" id="is_featured" name="is_featured">
                                    <label for="is_featured">Featured Article</label>
                                </div>

                                <div class="checkbox-group">
                                    <input type="checkbox" id="is_breaking" name="is_breaking">
                                    <label for="is_breaking">Breaking News</label>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Article
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Manage Articles -->
                    <div class="admin-section">
                        <div class="section-header">
                            <h2><i class="fas fa-newspaper"></i> Manage Articles</h2>
                        </div>
                        <div class="section-content">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($articles as $article): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($article['title']); ?></td>
                                            <td><?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($article['author_name'] ?? 'Unknown'); ?></td>
                                            <td>
                                                <?php if ($article['is_featured']): ?>
                                                    <span class="status-badge status-featured">Featured</span>
                                                <?php endif; ?>
                                                <?php if ($article['is_breaking']): ?>
                                                    <span class="status-badge status-breaking">Breaking</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $article['views'] ?? 0; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($article['published_date'])); ?></td>
                                            <td class="action-buttons">
                                                <button class="btn btn-primary"
                                                    onclick="editArticle(<?php echo $article['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="../front/article.php?id=<?php echo $article['id']; ?>"
                                                    class="btn btn-success" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this article?')">
                                                    <input type="hidden" name="action" value="delete_article">
                                                    <input type="hidden" name="article_id"
                                                        value="<?php echo $article['id']; ?>">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Users Tab -->
                <div id="users" class="tab-content">
                    <div class="admin-section">
                        <div class="section-header">
                            <h2><i class="fas fa-users"></i> Manage Users</h2>
                        </div>
                        <div class="section-content">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="user-role role-<?php echo $user['role']; ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td class="action-buttons">
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <form method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                        <input type="hidden" name="action" value="delete_user">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Current User</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Comments Tab -->
                <div id="comments" class="tab-content">
                    <div class="admin-section">
                        <div class="section-header">
                            <h2><i class="fas fa-comments"></i> Manage Comments</h2>
                        </div>
                        <div class="section-content">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Article</th>
                                        <th>Comment</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($comments as $comment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($comment['username']); ?></td>
                                            <td><?php echo htmlspecialchars($comment['article_title']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($comment['comment_text'], 0, 100)) . '...'; ?>
                                            </td>
                                            <td>
                                                <?php if ($comment['is_approved']): ?>
                                                    <span class="status-badge status-featured">Approved</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($comment['timestamp'])); ?></td>
                                            <td class="action-buttons">
                                                <button class="btn btn-warning">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Categories Tab -->
                <div id="categories" class="tab-content">
                    <div class="admin-section">
                        <div class="section-header">
                            <h2><i class="fas fa-tags"></i> Manage Categories</h2>
                        </div>
                        <div class="section-content">
                            <form method="POST" style="margin-bottom: 2rem;">
                                <input type="hidden" name="action" value="create_category">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="cat_name">Category Name</label>
                                        <input type="text" id="cat_name" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cat_icon">Icon (FontAwesome class)</label>
                                        <input type="text" id="cat_icon" name="icon" placeholder="fas fa-newspaper">
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Category
                                </button>
                            </form>

                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Icon</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                            <td><i class="<?php echo htmlspecialchars($category['icon']); ?>"></i></td>
                                            <td class="action-buttons">
                                                <button class="btn btn-primary"
                                                    onclick="editCategory(<?php echo $category['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                    <input type="hidden" name="action" value="delete_category">
                                                    <input type="hidden" name="category_id"
                                                        value="<?php echo $category['id']; ?>">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Article Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Article</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="action" value="update_article">
                <input type="hidden" name="article_id" id="edit_article_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_title">Title</label>
                        <input type="text" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_category_id">Category</label>
                        <select id="edit_category_id" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_content">Content</label>
                    <textarea id="edit_content" name="content" required></textarea>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="edit_is_featured" name="is_featured">
                    <label for="edit_is_featured">Featured Article</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="edit_is_breaking" name="is_breaking">
                    <label for="edit_is_breaking">Breaking News</label>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Article
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Category</h2>
            <form id="editCategoryForm" method="POST">
                <input type="hidden" name="action" value="update_category">
                <input type="hidden" name="category_id" id="edit_cat_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_cat_name">Category Name</label>
                        <input type="text" id="edit_cat_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_cat_icon">Icon (FontAwesome class)</label>
                        <input type="text" id="edit_cat_icon" name="icon" placeholder="fas fa-newspaper">
                    </div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Category
                </button>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }

        // Modal functionality
        const modal = document.getElementById('editModal');
        const categoryModal = document.getElementById('editCategoryModal');
        const closeBtns = document.getElementsByClassName('close');

        // Close modals when clicking X
        Array.from(closeBtns).forEach(closeBtn => {
            closeBtn.onclick = function () {
                modal.style.display = 'none';
                categoryModal.style.display = 'none';
            }
        });

        // Close modals when clicking outside
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
            if (event.target == categoryModal) {
                categoryModal.style.display = 'none';
            }
        }

        function editArticle(articleId) {
            console.log('Edit article called with ID:', articleId);

            // Show loading state
            modal.style.display = 'block';
            modal.classList.add('loading');

            const url = `get_article.php?action=get_article&id=${articleId}`;
            console.log('Fetching from URL:', url);

            // Fetch article data via AJAX
            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    modal.classList.remove('loading');

                    if (data.error) {
                        alert('Error: ' + data.error);
                        modal.style.display = 'none';
                        return;
                    }

                    // Populate the form fields
                    document.getElementById('edit_article_id').value = data.id;
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_content').value = data.content;
                    document.getElementById('edit_category_id').value = data.category_id;
                    document.getElementById('edit_is_featured').checked = data.is_featured == 1;
                    document.getElementById('edit_is_breaking').checked = data.is_breaking == 1;

                    // Auto-resize textarea
                    const textarea = document.getElementById('edit_content');
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                })
                .catch(error => {
                    modal.classList.remove('loading');
                    console.error('Fetch error:', error);
                    alert('Error fetching article data: ' + error.message);
                    modal.style.display = 'none';
                });
        }

        function editCategory(categoryId) {
            console.log('Edit category called with ID:', categoryId);

            // Show loading state
            categoryModal.style.display = 'block';
            categoryModal.classList.add('loading');

            const url = `get_article.php?action=get_category&id=${categoryId}`;
            console.log('Fetching from URL:', url);

            // Fetch category data via AJAX
            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Received category data:', data);
                    categoryModal.classList.remove('loading');

                    if (data.error) {
                        alert('Error: ' + data.error);
                        categoryModal.style.display = 'none';
                        return;
                    }

                    // Populate the form fields
                    document.getElementById('edit_cat_id').value = data.id;
                    document.getElementById('edit_cat_name').value = data.name;
                    document.getElementById('edit_cat_icon').value = data.icon;
                })
                .catch(error => {
                    categoryModal.classList.remove('loading');
                    console.error('Fetch error:', error);
                    alert('Error fetching category data: ' + error.message);
                    categoryModal.style.display = 'none';
                });
        }

        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#dc3545';
                    } else {
                        field.style.borderColor = '#e9ecef';
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        });
    </script>
</body>

</html>