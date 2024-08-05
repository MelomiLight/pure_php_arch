<?php

namespace app\Http\Middleware;

use app\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use helpers\HttpHelpers;

class JWTMiddleware implements MiddlewareInterface
{
    public function handle($request, $next)
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            HttpHelpers::responseJson(['error' => 'Unauthorized'], 401);
        }

        try {
            $header = trim($_SERVER['HTTP_AUTHORIZATION']);
            $token = $this->getBearerToken($header);
            $decoded = JWT::decode($token, new Key($_ENV['APP_KEY'], 'HS256'));
            $user = User::find($decoded->user_id);
            $_SERVER['user'] = $user;
        } catch (\Exception $e) {
            HttpHelpers::responseJson(['error' => $e->getMessage()], 400);
        }

        return $next($request);
    }

    private function getBearerToken($header)
    {
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
