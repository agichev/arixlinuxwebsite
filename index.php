<?php
define('ROUTER_ACTIVE', true);
require_once 'config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

$page = 'home';

switch (true) {
    case $uri === '' || $uri === '/':
        $page = 'home';
        break;
    case preg_match('#^/wiki/([^/]+)$#', $uri, $m):
        $page = 'wiki';
        $_GET['article'] = $m[1];
        break;
    case $uri === '/wiki':
        $page = 'wiki';
        break;
    case $uri === '/forum':
        $page = 'forum';
        break;
    case preg_match('#^/forum/([a-z0-9-]+)$#', $uri, $m):
        $page = 'forum';
        $_GET['board'] = $m[1];
        break;
    case $uri === '/newpost':
        $page = 'newpost';
        break;
    case preg_match('#^/newpost/([a-z0-9-]+)$#', $uri, $m):
        $page = 'newpost';
        $_GET['board'] = $m[1];
        break;
    case preg_match('#^/post/(\d+)$#', $uri, $m):
        $page = 'post';
        $_GET['id'] = (int)$m[1];
        break;
    case isset($_GET['page']) && in_array($_GET['page'], ['home','wiki','forum','post','newpost']):
        $page = $_GET['page'];
        break;
}

include 'includes/header.php';

switch ($page) {
    case 'wiki': include 'wiki.php'; break;
    case 'forum': include 'forum.php'; break;
    case 'post': include 'post.php'; break;
    case 'newpost': include 'newpost.php'; break;
    default: include 'pages/home.php';
}

include 'includes/footer.php';
