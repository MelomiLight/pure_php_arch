<?php

require_once __DIR__ . '/../../vendor/autoload.php'; // Adjust path as per your project setup

use Bootstrap\MigrationManager;

function up(): void
{
    try {
        $migrationManager = new MigrationManager();
        $migrationManager->runMigrations();
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function down(): void
{
    try {
        $migrationManager = new MigrationManager();
        $migrationManager->rollbackMigrations();
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function create(string $tableName): void
{
    try {
        $migrationManager = new MigrationManager();
        $migrationManager->createMigration($tableName);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Main entry point
$command = $argv[1] ?? null;

switch ($command) {
    case 'up':
        up();
        break;
    case 'down':
        down();
        break;
    case 'create':
        $tableName = $argv[2] ?? null;
        if (!$tableName) {
            echo "Please provide a table name for the create command." . PHP_EOL;
            exit(1);
        }
        create($tableName);
        break;
    default:
        echo "Unknown command: {$command}" . PHP_EOL;
        echo "Usage: php migrate.php [up|down|create] [tableName]" . PHP_EOL;
        exit(1);
}
