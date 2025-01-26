<?php

// Check for required PHP extensions
if (!extension_loaded('pdo')) {
    die("ERROR: PDO extension is not loaded.\n" .
        "Please enable it in your php.ini file or install it using:\n" .
        "- For Windows XAMPP: Uncomment 'extension=pdo' in php.ini\n" .
        "- For Linux: sudo apt-get install php-pdo\n" .
        "- For macOS: Should be installed by default with PHP\n");
}

if (!extension_loaded('pdo_mysql')) {
    die("ERROR: PDO MySQL driver is not loaded.\n" .
        "Please enable it in your php.ini file or install it using:\n" .
        "- For Windows XAMPP: Uncomment 'extension=pdo_mysql' in php.ini\n" .
        "- For Linux: sudo apt-get install php-mysql\n" .
        "- For macOS: Should be installed by default with PHP\n");
}

try {
    require_once __DIR__ . "/config/database.php";

    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `username` tinytext NOT NULL,
        `password` tinytext NOT NULL,
        `email` tinytext,
        `lastName` tinytext,
        `firstName` tinytext,
        `role` enum('user','admin') DEFAULT 'user',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    // Create consultations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `consultations` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `creator_id` INT UNSIGNED NOT NULL,
        `start_date` DATETIME NOT NULL,
        `end_date` DATETIME NOT NULL,
        `status` ENUM('draft', 'active', 'closed') DEFAULT 'draft',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`creator_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    // Create choices table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `choices` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `consultation_id` INT UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`consultation_id`) REFERENCES `consultations`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    // Create votes table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `votes` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `consultation_id` INT UNSIGNED NOT NULL,
        `voter_id` INT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`consultation_id`) REFERENCES `consultations`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`voter_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        UNIQUE KEY `unique_vote` (`consultation_id`, `voter_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    // Create vote_details table for Condorcet method
    $pdo->exec("CREATE TABLE IF NOT EXISTS `vote_details` (
        `vote_id` INT UNSIGNED NOT NULL,
        `winner_choice_id` INT UNSIGNED NOT NULL,
        `loser_choice_id` INT UNSIGNED NOT NULL,
        FOREIGN KEY (`vote_id`) REFERENCES `votes`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`winner_choice_id`) REFERENCES `choices`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`loser_choice_id`) REFERENCES `choices`(`id`) ON DELETE CASCADE,
        PRIMARY KEY (`vote_id`, `winner_choice_id`, `loser_choice_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    // Create default admin user if it doesn't exist
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, firstName, lastName, role) 
                          SELECT 'admin', :password, 'admin@example.com', 'Admin', 'User', 'admin'
                          WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin')");
    $stmt->execute(['password' => $adminPassword]);

    echo "Database installation completed successfully!\n";
    echo "Default admin user created:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "Please change these credentials after first login.\n";

} catch (PDOException $e) {
    die("Database installation failed: " . $e->getMessage() . "\n");
}
?> 