<?php

namespace db\Migrations;

use PDO;

class UsersTableMigration
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up(): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $this->pdo->exec($sql);
        echo "Table 'users' created successfully." . PHP_EOL;
    }

    public function down(): void
    {
        $sql = "DROP TABLE IF EXISTS users";
        $this->pdo->exec($sql);
        echo "Table 'users' dropped successfully." . PHP_EOL;
    }
}