<?php
$isStandalone = !defined('ROUTER_ACTIVE');
if ($isStandalone) {
    require_once __DIR__ . '/config.php';
    $page = 'forum';
    include __DIR__ . '/includes/header.php';
}

if (!$pdo) {
    echo '<div class="alert">Database not connected.</div>';
    if (!empty($dbError)) echo '<div class="alert alert-info" style="font-size:13px;">' . escape($dbError) . '</div>';
    if ($isStandalone) include __DIR__ . '/includes/footer.php';
    return;
}

$boardSlug = isset($_GET['board']) ? $_GET['board'] : '';
$editToken = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        die('CSRF validation failed.');
    }

    $boardId = (int)($_POST['board_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $imageUrl = null;

    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } elseif (mb_strlen($title) > 255) {
        $error = 'Title is too long (max 255 characters).';
    } else {
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $error = 'Invalid image type. Allowed: JPEG, PNG, GIF, WebP.';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'Image too large (max 5MB).';
            } else {
                $imageData = file_get_contents($_FILES['image']['tmp_name']);
                $imageUrl = uploadToImgBB($imageData);
                if (is_string($imageUrl) && substr($imageUrl, 0, 4) === 'ERR_') {
                    $error = 'Image upload failed: ' . $imageUrl;
                } elseif (!$imageUrl) {
                    $error = 'Image upload failed for an unknown reason.';
                }
            }
        }

        if (!isset($error)) {
            $stmt = $pdo->prepare('INSERT INTO posts (board_id, title, content, image_url) VALUES (?, ?, ?, ?)');
            $stmt->execute([$boardId, $title, $content, $imageUrl]);
            $postId = $pdo->lastInsertId();

            $editToken = generateEditToken($pdo, $postId);

            header('Content-Type: text/html; charset=utf-8');
            ?>
            <!DOCTYPE html>
            <html>
            <head><meta charset="utf-8"><title>Redirecting…</title></head>
            <body>
            <script>
            localStorage.setItem('edit_token_<?= $postId ?>', '<?= $editToken ?>');
            window.location.href = '/post.php?id=<?= $postId ?>';
            </script>
            </body>
            </html>
            <?php
            exit;
        }
    }
}

$stmt = $pdo->query('SELECT id, name, slug FROM boards ORDER BY sort_order');
$boards = $stmt->fetchAll();
$selectedBoardId = '';
foreach ($boards as $b) {
    if ($b['slug'] === $boardSlug) {
        $selectedBoardId = $b['id'];
        break;
    }
}
?>
<h1>New Post</h1>

<?php if (isset($error)): ?>
    <div class="alert"><?= escape($error) ?></div>
<?php endif; ?>

<form method="post" action="/newpost.php<?= $boardSlug ? '?board=' . escape($boardSlug) : '' ?>" enctype="multipart/form-data" class="forum-newpost-form">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <div class="form-group">
        <label>Board</label>
        <select name="board_id" required>
            <option value="">— Select board —</option>
            <?php foreach ($boards as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $selectedBoardId == $b['id'] ? 'selected' : '' ?>><?= escape($b['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" placeholder="Post title" value="<?= escape($_POST['title'] ?? '') ?>" required maxlength="255">
    </div>

    <div class="form-group">
        <label>Content</label>
        <textarea name="content" placeholder="Write your post…" required><?= escape($_POST['content'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label>Attach image (optional)</label>
        <div class="file-input-wrapper">
            <input type="file" name="image" accept="image/png,image/jpeg,image/gif,image/webp" id="newpost-image">
            <span class="file-input-label">Choose file</span>
            <span class="file-input-name" id="newpost-image-name">No file chosen</span>
        </div>
        <p style="color:#666;font-size:12px;margin-top:4px;">Max 5MB. JPEG, PNG, GIF, WebP.</p>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create post</button>
        <a href="/forum.php" class="btn" style="margin-left:8px;">Cancel</a>
    </div>
</form>

<script>
document.getElementById('newpost-image').addEventListener('change', function() {
    document.getElementById('newpost-image-name').textContent = this.files.length ? this.files[0].name : 'No file chosen';
});
</script>
<?php
if ($isStandalone) include __DIR__ . '/includes/footer.php';
