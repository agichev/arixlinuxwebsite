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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo '<div class="alert">Post not found.</div>';
    if ($isStandalone) include __DIR__ . '/includes/footer.php';
    return;
}

$stmt = $pdo->prepare('SELECT p.*, b.name AS board_name, b.slug AS board_slug FROM posts p JOIN boards b ON p.board_id = b.id WHERE p.id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo '<div class="alert">Post not found.</div>';
    if ($isStandalone) include __DIR__ . '/includes/footer.php';
    return;
}

$stmt = $pdo->prepare('SELECT * FROM replies WHERE post_id = ? ORDER BY created_at ASC');
$stmt->execute([$id]);
$replies = $stmt->fetchAll();
?>
<div class="breadcrumb">
    <a href="/forum.php">Forum</a>
    <span class="sep">/</span>
    <a href="/forum.php?board=<?= escape($post['board_slug']) ?>"><?= escape($post['board_name']) ?></a>
    <span class="sep">/</span>
    <span><?= escape($post['title']) ?></span>
</div>

<div class="post-actions" id="post-actions" style="display:none;">
    <button class="btn" id="edit-post-btn" data-post="<?= $post['id'] ?>">Edit</button>
    <button class="btn btn-danger" id="delete-post-btn" data-post="<?= $post['id'] ?>">Delete</button>
</div>

<div class="forum-topic">
    <div class="forum-post">
        <div class="forum-post-header">
            <span class="post-author">Anonymous</span>
            <span class="post-date"><?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></span>
        </div>
        <div class="forum-post-body">
            <h2 style="margin-top:0;font-size:18px;"><?= escape($post['title']) ?></h2>
            <?= nl2br(escape($post['content'])) ?>
            <?php if ($post['image_url']): ?>
                <br><a href="<?= escape($post['image_url']) ?>" target="_blank"><img src="<?= escape($post['image_url']) ?>" alt="attached image"></a>
            <?php endif; ?>
        </div>
    </div>

    <?php foreach ($replies as $reply): ?>
        <div class="forum-post">
            <div class="forum-post-header">
                <span class="post-author">Anonymous</span>
                <span class="post-date"><?= date('Y-m-d H:i', strtotime($reply['created_at'])) ?></span>
            </div>
            <div class="forum-post-body">
                <?= nl2br(escape($reply['content'])) ?>
                <?php if ($reply['image_url']): ?>
                    <br><a href="<?= escape($reply['image_url']) ?>" target="_blank"><img src="<?= escape($reply['image_url']) ?>" alt="attached image"></a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="forum-reply-form">
    <h3>Reply to this post</h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert"><?= escape($_GET['error']) ?></div>
    <?php endif; ?>
    <form method="post" action="/reply.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <div class="form-group">
            <textarea name="content" placeholder="Write your reply…" required></textarea>
        </div>
        <div class="form-group">
            <label>Attach image (optional)</label>
            <div class="file-input-wrapper">
                <input type="file" name="image" accept="image/png,image/jpeg,image/gif,image/webp" id="reply-image">
                <span class="file-input-label">Choose file</span>
                <span class="file-input-name" id="reply-image-name">No file chosen</span>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Post reply</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var token = localStorage.getItem('edit_token_<?= $post['id'] ?>');
    if (token) {
        document.getElementById('post-actions').style.display = 'flex';
    }

    var fileInput = document.getElementById('reply-image');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            document.getElementById('reply-image-name').textContent = this.files.length ? this.files[0].name : 'No file chosen';
        });
    }
});
</script>
<?php
if ($isStandalone) include __DIR__ . '/includes/footer.php';
