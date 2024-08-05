<?php

namespace bootstrap;

use helpers\GetClassNameFromFile;
use PDO;

class MigrationManager
{
    protected PDO $pdo;
    protected GetClassNameFromFile $gcnfHelper;

    public function __construct()
    {
        $dbConfig = require __DIR__ . '/../config/database.php';
        $dsn = "{$dbConfig['connection']}:host={$dbConfig['host']};dbname={$dbConfig['database']}";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], array(
            PDO::ATTR_PERSISTENT => true
        ));
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo = $pdo;

        $this->gcnfHelper = new GetClassNameFromFile();
    }

    public function runMigrations(): void
    {
        $migrationFiles = glob(__DIR__ . '/../db/migrations/*.php');

        foreach ($migrationFiles as $file) {
            require_once $file;

            $className = $this->gcnfHelper->getClassNameFromFile($file);

            if ($className !== null && class_exists($className)) {
                $migration = new $className($this->pdo);
                $migration->up();
            }

            echo "Ran migration: $className" . PHP_EOL;
        }
    }

    public function rollbackMigrations(): void
    {
        $migrationFiles = array_reverse(glob(__DIR__ . '/../db/migrations/*.php'));

        foreach ($migrationFiles as $file) {
            require_once $file;

            $className = $this->gcnfHelper->getClassNameFromFile($file);

            if ($className !== null && class_exists($className)) {
                $migration = new $className($this->pdo);
                $migration->down();
            }
            echo "Rolled back migration: $className" . PHP_EOL;
        }
    }

    public
    function createMigration($tableName): void
    {
        $timestamp = date('YmdHis');
        $className = ucfirst(camel_case($tableName)) . 'TableMigration';
        $filename = "{$timestamp}_{$tableName}.php";
        $filepath = __DIR__ . '/../db/migrations/' . $filename;

        $content = <<<PHP
<?php

namespace db\Migrations;

use PDO;

class {$className}
{
    protected PDO \$pdo;

    public function __construct(PDO \$pdo)
    {
        \$this->pdo = \$pdo;
    }

    public function up(): void
    {
        \$sql = "
        CREATE TABLE IF NOT EXISTS {$tableName} (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        \$this->pdo->exec(\$sql);
        echo "Table '{$tableName}' created successfully." . PHP_EOL;
    }

    public function down(): void
    {
        \$sql = "DROP TABLE IF EXISTS {$tableName}";
        \$this->pdo->exec(\$sql);
        echo "Table '{$tableName}' dropped successfully." . PHP_EOL;
    }
}
PHP;

        if (file_put_contents($filepath, $content)) {
            echo "Migration file '{$filename}' created successfully." . PHP_EOL;
        } else {
            echo "Failed to create migration file." . PHP_EOL;
        }
    }
}

function camel_case($str): string
{
    $str = ucwords(str_replace('_', ' ', $str));
    $str = str_replace(' ', '', $str);
    return lcfirst($str);
}
