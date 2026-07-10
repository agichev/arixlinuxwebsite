<?php
$isStandalone = !defined('ROUTER_ACTIVE');
if ($isStandalone) {
    require_once __DIR__ . '/config.php';
    $page = 'forum';
    include __DIR__ . '/includes/header.php';
}

if (!$pdo) {
    echo '<div class="alert">Database not connected.</div>';
    if ($isStandalone) include __DIR__ . '/includes/footer.php';
    return;
}

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!$postId || !$token || !verifyEditToken($pdo, $postId, $token)) {
    echo '<div class="alert">Invalid or expired edit token. You can only edit within 20 minutes of creating the post.</div>';
    if ($isStandalone) include __DIR__ . '/includes/footer.php';
    return;
}

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    echo '<div class="alert">Post not found.</div>';
    if ($isStandalone) include __DIR__ . '/includes/footer.php';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        die('CSRF validation failed.');
    }

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $imageUrl = $post['image_url'];

    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } elseif (mb_strlen($title) > 255) {
        $error = 'Title is too long (max 255 characters).';
    } else {
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $error = 'Invalid image type.';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'Image too large (max 5MB).';
            } else {
                $imageData = file_get_contents($_FILES['image']['tmp_name']);
                $newUrl = uploadToImgBB($imageData);
                if (is_string($newUrl) && substr($newUrl, 0, 4) === 'ERR_') {
                    $error = 'Image upload failed: ' . $newUrl;
                } elseif ($newUrl) {
                    $imageUrl = $newUrl;
                }
            }
        }

        if (!isset($error)) {
            $stmt = $pdo->prepare('UPDATE posts SET title = ?, content = ?, image_url = ? WHERE id = ?');
            $stmt->execute([$title, $content, $imageUrl, $postId]);
            deleteEditToken($pdo, $postId, $token);
            header('Location: /post.php?id=' . $postId);
            exit;
        }
    }
}
?>
<h1>Edit Post</h1>

<?php if (isset($error)): ?>
    <div class="alert"><?= escape($error) ?></div>
<?php endif; ?>

<form method="post" action="/editpost.php?id=<?= $postId ?>&token=<?= escape($token) ?>" enctype="multipart/form-data" class="forum-newpost-form">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" value="<?= escape($_POST['title'] ?? $post['title']) ?>" required maxlength="255">
    </div>

    <div class="form-group">
        <label>Content</label>
        <textarea name="content" required><?= escape($_POST['content'] ?? $post['content']) ?></textarea>
    </div>

    <?php if ($post['image_url']): ?>
        <p style="color:#888;font-size:13px;">Current image: <a href="<?= escape($post['image_url']) ?>" target="_blank">view</a></p>
    <?php endif; ?>

    <div class="form-group">
        <label>Change image (optional)</label>
        <div class="file-input-wrapper">
            <input type="file" name="image" accept="image/png,image/jpeg,image/gif,image/webp" id="edit-image">
            <span class="file-input-label">Choose file</span>
            <span class="file-input-name" id="edit-image-name">No file chosen</span>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="/post.php?id=<?= $postId ?>" class="btn" style="margin-left:8px;">Cancel</a>
    </div>
</form>

<script>
document.getElementById('edit-image').addEventListener('change', function() {
    document.getElementById('edit-image-name').textContent = this.files.length ? this.files[0].name : 'No file chosen';
});
</script>
<?php
if ($isStandalone) include __DIR__ . '/includes/footer.php';
