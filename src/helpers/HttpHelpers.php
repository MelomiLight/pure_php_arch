<?php

namespace helpers;

use JetBrains\PhpStorm\NoReturn;

class HttpHelpers
{
    #[NoReturn] public static function responseJson(array $message, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($message);
        exit();
    }
}