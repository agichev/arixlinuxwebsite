<?php
$isStandalone = !defined('ROUTER_ACTIVE');
if ($isStandalone) {
    require_once __DIR__ . '/config.php';
    $page = 'wiki';
    include __DIR__ . '/includes/header.php';
}

$article = isset($_GET['article']) ? $_GET['article'] : '';

if ($article) {
    require_once __DIR__ . '/lib/Parsedown.php';
    $safeName = basename($article);
    $filePath = __DIR__ . '/wiki/' . $safeName;
    if (!file_exists($filePath) || !str_ends_with($safeName, '.md')) {
        echo '<div class="alert">Article not found.</div>';
        if ($isStandalone) include __DIR__ . '/includes/footer.php';
        return;
    }
    $content = file_get_contents($filePath);
    $parsedown = new Parsedown();
    $html = $parsedown->text($content);
    $title = pathinfo($safeName, PATHINFO_FILENAME);
    ?>
    <a href="/wiki.php" class="wiki-back">&larr; Back to wiki</a>
    <div class="wiki-article"><?= $html ?></div>
    <?php
} else {
    ?>
    <h1>Wiki</h1>
    <p class="page-desc">Documentation and guides for Arix Linux</p>

    <div class="wiki-search">
        <input type="text" id="wiki-search" placeholder="Search articles by name…" autofocus>
    </div>

    <div id="wiki-list-container">
        <div class="loading">Loading wiki articles…</div>
    </div>
    <?php
}

if ($isStandalone) include __DIR__ . '/includes/footer.php';
