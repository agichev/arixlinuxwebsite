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

if ($boardSlug) {
    $stmt = $pdo->prepare('SELECT * FROM boards WHERE slug = ?');
    $stmt->execute([$boardSlug]);
    $board = $stmt->fetch();

    if (!$board) {
        echo '<div class="alert">Board not found.</div>';
        if ($isStandalone) include __DIR__ . '/includes/footer.php';
        return;
    }

    $perPage = 20;
    $pageNum = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    $offset = ($pageNum - 1) * $perPage;

    $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE board_id = ?');
    $totalStmt->execute([$board['id']]);
    $totalPosts = (int)$totalStmt->fetchColumn();
    $totalPages = max(1, ceil($totalPosts / $perPage));

    $stmt = $pdo->prepare('SELECT p.*, (SELECT COUNT(*) FROM replies WHERE post_id = p.id) AS reply_count FROM posts p WHERE board_id = ? ORDER BY p.created_at DESC LIMIT ? OFFSET ?');
    $stmt->execute([$board['id'], $perPage, $offset]);
    $posts = $stmt->fetchAll();
    ?>
    <div class="breadcrumb">
        <a href="/forum.php">Forum</a>
        <span class="sep">/</span>
        <span><?= escape($board['name']) ?></span>
    </div>

    <div class="forum-actions">
        <a href="/newpost.php?board=<?= escape($board['slug']) ?>" class="btn btn-primary">+ New post</a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="forum-empty">No posts yet. Be the first!</div>
    <?php else: ?>
        <table class="forum-post-list">
            <thead>
                <tr>
                    <th>Topic</th>
                    <th style="width:80px;text-align:center;">Replies</th>
                    <th style="width:160px;">Posted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <a href="/post.php?id=<?= $post['id'] ?>" class="post-title"><?= escape($post['title']) ?></a>
                        </td>
                        <td class="post-replies"><?= $post['reply_count'] ?></td>
                        <td class="post-meta"><?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $pageNum): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="/forum.php?board=<?= escape($board['slug']) ?>&p=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php
} else {
    $boards = $pdo->query('SELECT b.*, (SELECT COUNT(*) FROM posts WHERE board_id = b.id) AS post_count FROM boards b ORDER BY b.sort_order')->fetchAll();
    ?>
    <h1>Forum</h1>
    <p class="page-desc">Community discussions about Arix Linux</p>

    <div class="forum-boards">
        <?php foreach ($boards as $board): ?>
            <div class="forum-board">
                <a href="/forum.php?board=<?= escape($board['slug']) ?>">
                    <div class="board-name"><?= escape($board['name']) ?></div>
                    <div class="board-desc"><?= escape($board['description']) ?></div>
                    <div class="board-stats"><?= $board['post_count'] ?> topics</div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

if ($isStandalone) include __DIR__ . '/includes/footer.php';
