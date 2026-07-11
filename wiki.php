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

    // Transform fenced code blocks (<pre><code>) into command-wrapper structure
    $html = preg_replace_callback(
        '/<pre><code(?:\s+class="([^"]*)")?>([\s\S]*?)<\/code><\/pre>/',
        function ($matches) {
            $class = $matches[1] ?? '';
            $code = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $lines = explode("\n", $code);
            $commandLines = '';
            $isUser = strpos($class, 'language-user') !== false;
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '') continue;
                $cls = 'command-line' . ($isUser ? ' user' : '');
                $commandLines .= '<span class="' . $cls . '">' . htmlspecialchars($trimmed, ENT_QUOTES | ENT_HTML5, 'UTF-8') . "</span>\n";
            }
            return '<div class="command-wrapper">'
                . '<button class="copy-btn">[Copy]</button>'
                . '<div class="command-block">'
                . $commandLines
                . '</div>'
                . '</div>';
        },
        $html
    );

    // Transform inline code patterns like "<p>root # <code>cmd</code></p>" and "<p>user $ <code>cmd</code></p>"
    $html = preg_replace_callback(
        '/<p>(root #|user \$) <code>([^<]+)<\/code><\/p>/',
        function ($matches) {
            $isUser = $matches[1] === 'user $';
            $cmd = htmlspecialchars($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cls = 'command-line' . ($isUser ? ' user' : '');
            return '<div class="command-wrapper">'
                . '<button class="copy-btn">[Copy]</button>'
                . '<div class="command-block">'
                . '<span class="' . $cls . '">' . $cmd . '</span>'
                . '</div>'
                . '</div>';
        },
        $html
    );

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
