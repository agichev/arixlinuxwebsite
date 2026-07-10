<?php
require_once __DIR__ . '/config.php';

$wikiDir = __DIR__ . '/wiki';
$files = glob($wikiDir . '/*.md');
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

if (empty($files)) {
    echo '<p style="color: #888; text-align: center; padding: 30px 0;">No wiki articles yet.</p>';
    exit;
}

echo '<ul class="wiki-list">';
foreach ($files as $file) {
    $name = pathinfo($file, PATHINFO_BASENAME);
    $displayName = pathinfo($file, PATHINFO_FILENAME);
    echo '<li data-name="' . escape(strtolower($displayName)) . '">';
    echo '<a href="/wiki.php?article=' . escape(rawurlencode($name)) . '">';
    echo '<span class="wiki-icon">&#128196;</span>';
    echo '<span class="wiki-name">' . escape($displayName) . '</span>';
    echo '</a></li>';
}
echo '</ul>';
