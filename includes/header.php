<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Arix Linux</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Arix Linux installation guide - fresh installation from bootable media">
    <meta name="author" content="Arix Linux">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php
$bgImages = [
    '/pictures/_ (5).jpeg',
    '/pictures/alexandre-debieve-FO7JIlwjOtU-unsplash.jpg',
    '/pictures/bermix-studio-bCrM2e1M0a4-unsplash.jpg',
    '/pictures/daniel-falcao-Pt27b3dRdVA-unsplash.jpg',
    '/pictures/dima-solomin-inx88HqItBU-unsplash.jpg',
    '/pictures/enrique-alarcon-lg9mt2IgR7I-unsplash.jpg',
    '/pictures/ethan-da-silva-50iz5FOQDqM-unsplash.jpg',
    '/pictures/eury-escudero-TSt0b5MHBz8-unsplash.jpg',
    '/pictures/luis-villasmil-S2qA7JhjI6Y-unsplash.jpg',
    '/pictures/milad-fakurian-saOPkvma85M-unsplash.jpg',
    '/pictures/muha-ajjan-sL2BRR1cuvM-unsplash.jpg',
    '/pictures/pawel-nolbert-4u2U8EO9OzY-unsplash.jpg',
    '/pictures/philipp-katzenberger-iIJrUoeRoCQ-unsplash.jpg',
    '/pictures/vasilis-chatzopoulos-JcUQoTOeW3s-unsplash.jpg',
    '/pictures/vimal-s-ZhVUAUc8V4s-unsplash.jpg',
    '/pictures/xt7-core-H6qAK5Wg3C0-unsplash.jpg',
    '/pictures/yousef-samuil-hsFbf2QGMsk-unsplash.jpg'
];
$headerBg = $bgImages[array_rand($bgImages)];
?>
    <header id="site-header" data-bg="<?= $headerBg ?>">
        <div class="header-content">
            <div class="logo">Arix<span>Linux</span></div>
            <div class="tagline">Lightweight, systemd-free Linux distribution</div>
        </div>
    </header>

    <nav class="main-nav">
        <div class="nav-inner">
            <a href="/" class="<?= $page === 'home' ? 'active' : '' ?>">Home</a>
            <a href="/installation" class="<?= $page === 'installation' ? 'active' : '' ?>">Installation</a>
            <a href="/wiki.php" class="<?= $page === 'wiki' ? 'active' : '' ?>">Wiki</a>
            <a href="/forum.php" class="<?= $page === 'forum' ? 'active' : '' ?>">Forum</a>
        </div>
    </nav>

<?php if ($page !== 'home'): ?>
    <div class="container">
        <div class="content">
<?php endif; ?>
