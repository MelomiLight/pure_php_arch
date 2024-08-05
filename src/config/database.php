<?php

namespace config;

return [
    'connection' => $_ENV['DB_CONNECTION'] ?? 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database' => $_ENV['DB_DATABASE'] ?? 'php-app',
    'username' => $_ENV['DB_USERNAME'] ?? 'user',
    'password' => $_ENV['DB_PASSWORD'] ?? 'password',
];