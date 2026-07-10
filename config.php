<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

$dbError = '';

function env($key, $default = null) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    $val = getenv($key);
    if ($val !== false) return $val;
    return $default;
}

$dotenv = __DIR__ . '/.env';
if (file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $k = trim($parts[0]);
            $v = trim($parts[1]);
            $_ENV[$k] = $v;
            $_SERVER[$k] = $v;
            putenv("$k=$v");
        }
    }
}

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        env('DB_HOST', 'localhost'),
        env('DB_PORT', '3306'),
        env('DB_NAME', 'arix_forum')
    );
    $pdo = new PDO($dsn, env('DB_USER', 'root'), env('DB_PASS', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    $pdo = null;
    $dbError = $e->getMessage();
}

if ($pdo) {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS boards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            board_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            image_url VARCHAR(500) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS replies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            content TEXT NOT NULL,
            image_url VARCHAR(500) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS edit_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            INDEX idx_token (token),
            INDEX idx_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $count = $pdo->query("SELECT COUNT(*) FROM boards")->fetchColumn();
        if ((int)$count === 0) {
            $stmt = $pdo->prepare("INSERT INTO boards (name, description, slug, sort_order) VALUES (?, ?, ?, ?)");
            $boards = [
                ['General Questions', 'Questions about Arix Linux and general discussions', 'general', 1],
                ['System Installation', 'Help with installing Arix Linux', 'installation', 2],
                ['Configuration', 'System configuration, tweaking, dotfiles', 'configuration', 3],
                ['Software & Packages', 'Software, packages, repositories discussion', 'software', 4],
                ['Hardware & Drivers', 'Hardware compatibility, drivers, kernel', 'hardware', 5],
                ['Network & Security', 'Network setup, firewalls, security', 'network', 6],
                ['Scripts & Terminal', 'Bash scripting, automation, CLI tools', 'scripts', 7],
                ['Offtopic', 'Everything else', 'offtopic', 8],
            ];
            foreach ($boards as $b) {
                $stmt->execute($b);
            }
        }

        $pdo->exec("DELETE FROM edit_tokens WHERE expires_at < NOW()");
    } catch (PDOException $e) {
        // silent fail
    }
}

function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function truncate($str, $len = 100) {
    if (mb_strlen($str) <= $len) return $str;
    return mb_substr($str, 0, $len) . '…';
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateEditToken($pdo, $postId) {
    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("INSERT INTO edit_tokens (post_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 20 MINUTE))");
    $stmt->execute([$postId, $token]);
    return $token;
}

function verifyEditToken($pdo, $postId, $token) {
    $stmt = $pdo->prepare("SELECT id FROM edit_tokens WHERE post_id = ? AND token = ? AND expires_at > NOW()");
    $stmt->execute([$postId, $token]);
    return $stmt->fetch() !== false;
}

function deleteEditToken($pdo, $postId, $token) {
    $stmt = $pdo->prepare("DELETE FROM edit_tokens WHERE post_id = ? AND token = ?");
    $stmt->execute([$postId, $token]);
}

function uploadToImgBB($imageData) {
    $apiKey = env('IMGBB_API_KEY', '');
    if (empty($apiKey)) return 'ERR_NOKEY';

    $boundary = '----FormBoundary' . uniqid();
    $body = "--{$boundary}\r\n"
        . "Content-Disposition: form-data; name=\"image\"\r\n\r\n"
        . base64_encode($imageData) . "\r\n"
        . "--{$boundary}--\r\n";
    $url = 'https://api.imgbb.com/1/upload?key=' . $apiKey;

    $ctx = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: multipart/form-data; boundary={$boundary}\r\n",
        'content' => $body,
        'timeout' => 30,
    ]]);
    $response = @file_get_contents($url, false, $ctx);
    if ($response === false) return 'ERR_NETWORK';

    $data = json_decode($response, true);
    if (!$data || !isset($data['data'])) return 'ERR_RESP:' . substr($response, 0, 300);

    $d = $data['data'];

    if (!empty($d['url']) && preg_match('#^https?://#i', $d['url'])) return $d['url'];

    // Try other fields
    foreach (['display_url', 'image.url', 'thumb.url', 'medium.url'] as $f) {
        $parts = explode('.', $f);
        $v = $d;
        foreach ($parts as $p) {
            if (!isset($v[$p])) { $v = null; break; }
            $v = $v[$p];
        }
        if ($v && preg_match('#^https?://#i', $v)) return $v;
    }

    // Maybe url field is just a path like "527b184dd919.jpg" — prepend domain
    if (!empty($d['url']) && is_string($d['url'])) {
        return 'https://i.ibb.co/' . ltrim($d['url'], '/');
    }

    // Try constructing from id + image.filename
    if (!empty($d['id']) && !empty($d['image']['filename'])) {
        return 'https://i.ibb.co/' . $d['id'] . '/' . $d['image']['filename'];
    }

    return 'ERR_API:' . substr($response, 0, 200);
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
