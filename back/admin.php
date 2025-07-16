<?php
require_once './config.php';
require_once './get_articles.php';
$newsManager = new NewsManager();
// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_article':
                try {
                    $slug = strtolower(str_replace(' ', '-', $_POST['title']));
                    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
                    $stmt = $pdo->prepare("
                        INSERT INTO articles (title, slug, content, category_id, author_id, is_featured, is_breaking , published_date)
                        VALUES (?, ?, ?, ?, NULL, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['title'],
                        $slug,
                        $_POST['content'],
                        $_POST['category_id'],
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
                    $stmt = $pdo->prepare("
                        UPDATE articles 
                        SET title = ?, , content = ?, category_id = ?,  published_date = CURRENT_TIMESTAMP
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['content'],
                        $_POST['category_id'],
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
        }
    }
}
// Get all articles for admin view
$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, u.username as author_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = $newsManager->getCategories();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Global News Network</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-nav h1 {
            font-size: 1.5rem;
        }

        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .admin-nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .admin-content {
            padding: 2rem 0;
        }

        .admin-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            padding: 1rem 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .section-content {
            padding: 2rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .articles-table {
            width: 100%;
            border-collapse: collapse;
        }

        .articles-table th,
        .articles-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .articles-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .articles-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-featured {
            background-color: #28a745;
            color: white;
        }

        .status-breaking {
            background-color: #dc3545;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <div class="admin-container">
            <nav class="admin-nav">
                <h1><i class="fas fa-cog"></i> Admin Panel</h1>
                <div>
                    <a href="index.php"><i class="fas fa-home"></i> View Site</a>
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

            <!-- Create New Article -->
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-plus"></i> Create New Article</h2>
                </div>
                <div class="section-content">
                    <form method="POST">
                        <input type="hidden" name="action" value="create_article">

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea id="content" name="content" required></textarea>
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
                    <table class="articles-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($article['title']); ?></td>
                                    <td><?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td><?php echo htmlspecialchars($article['author_name'] ?? 'Unknown'); ?></td>
                                    <td>
                                        <?php if ($article['is_featured']): ?>
                                            <span class="status-badge status-featured">Featured</span>
                                        <?php endif; ?>
                                        <?php if ($article['is_breaking']): ?>
                                            <span class="status-badge status-breaking">Breaking</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($article['published_date'])); ?></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-primary"
                                            onclick="editArticle(<?php echo $article['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this article?')">
                                            <input type="hidden" name="action" value="delete_article">
                                            <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
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
    </main>

    <!-- Edit Article Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Article</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="action" value="update_article">
                <input type="hidden" name="article_id" id="edit_article_id">

                <div class="form-group">
                    <label for="edit_title">Title</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="edit_content">Content</label>
                    <textarea id="edit_content" name="content" required></textarea>
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

    <script>
        // Modal functionality
        const modal = document.getElementById('editModal');
        const closeBtn = document.getElementsByClassName('close')[0];

        closeBtn.onclick = function () {
            modal.style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function editArticle(articleId) {

            modal.style.display = 'block';
        }
    </script>
</body>

</html>