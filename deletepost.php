<?php
require_once __DIR__ . '/config.php';

if (!$pdo) {
    jsonResponse(['ok' => false, 'error' => 'Database not connected.'], 500);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['ok' => false, 'error' => 'Method not allowed.'], 405);
}

$postId = (int)($_POST['post_id'] ?? 0);
$token = $_POST['token'] ?? '';

if (!$postId || !$token) {
    jsonResponse(['ok' => false, 'error' => 'Missing parameters.'], 400);
}

if (!verifyEditToken($pdo, $postId, $token)) {
    jsonResponse(['ok' => false, 'error' => 'Invalid or expired token.'], 403);
}

$stmt = $pdo->prepare('SELECT board_id FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    jsonResponse(['ok' => false, 'error' => 'Post not found.'], 404);
}

$boardId = $post['board_id'];
$pdo->beginTransaction();
try {
    deleteEditToken($pdo, $postId, $token);
    $stmt = $pdo->prepare('DELETE FROM replies WHERE post_id = ?');
    $stmt->execute([$postId]);
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$postId]);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(['ok' => false, 'error' => 'Delete failed.'], 500);
}

$stmt = $pdo->prepare('SELECT slug FROM boards WHERE id = ?');
$stmt->execute([$boardId]);
$board = $stmt->fetch();

jsonResponse(['ok' => true, 'redirect' => '/forum.php' . ($board ? '?board=' . $board['slug'] : '')]);
