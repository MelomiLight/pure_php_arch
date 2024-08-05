<?php

namespace bootstrap;

use Dotenv\Dotenv;
use PDO;
use Routes\Router;

class App
{
    public function __construct()
    {
        $this->loadEnvironmentVariables();
        $this->configureDatabase();
        $this->initializeRouter();
    }

    protected function loadEnvironmentVariables(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();
    }

    protected function configureDatabase(): void
    {
        $dbConfig = require __DIR__ . '/../config/database.php';

        try {
            $dsn = "{$dbConfig['connection']}:host={$dbConfig['host']};dbname={$dbConfig['database']}";
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], array(
                PDO::ATTR_PERSISTENT => true
            ));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, );

            $GLOBALS['pdo'] = $pdo;
        } catch (\PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    protected function initializeRouter(): void
    {
        require_once __DIR__ . '/../routes/routes.php';
    }

    public function start(): void
    {
        Router::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    }
}

