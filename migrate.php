<?php
require_once 'config.php';

echo "Arix Linux — Database Migration\n";
echo str_repeat('-', 40) . "\n";

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS boards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Table 'boards' ready.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        board_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        image_url VARCHAR(500) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Table 'posts' ready.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS replies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        content TEXT NOT NULL,
        image_url VARCHAR(500) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Table 'replies' ready.\n";

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
        echo "[OK] Default boards seeded.\n";
    } else {
        echo "[SKIP] Boards already exist.\n";
    }

    echo str_repeat('-', 40) . "\n";
    echo "Migration completed successfully.\n";

} catch (PDOException $e) {
    die("[FAIL] " . $e->getMessage() . "\n");
}
