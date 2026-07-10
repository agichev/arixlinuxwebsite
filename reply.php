<?php
require_once __DIR__ . '/config.php';

if (!$pdo) {
    die('Database not connected.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /forum.php');
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    die('CSRF validation failed.');
}

$postId = (int)($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$imageUrl = null;

if (empty($content)) {
    header('Location: /post.php?id=' . $postId . '&error=' . urlencode('Reply content is required.'));
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM posts WHERE id = ?');
$stmt->execute([$postId]);
if (!$stmt->fetch()) {
    header('Location: /forum.php');
    exit;
}

if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        header('Location: /post.php?id=' . $postId . '&error=' . urlencode('Invalid image type.'));
        exit;
    }
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        header('Location: /post.php?id=' . $postId . '&error=' . urlencode('Image too large (max 5MB).'));
        exit;
    }
    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    $imageUrl = uploadToImgBB($imageData);
    if (!$imageUrl || (is_string($imageUrl) && substr($imageUrl, 0, 4) === 'ERR_')) {
        header('Location: /post.php?id=' . $postId . '&error=' . urlencode('Image upload failed: ' . $imageUrl));
        exit;
    }
}

$stmt = $pdo->prepare('INSERT INTO replies (post_id, content, image_url) VALUES (?, ?, ?)');
$stmt->execute([$postId, $content, $imageUrl]);

header('Location: /post.php?id=' . $postId);
exit;
