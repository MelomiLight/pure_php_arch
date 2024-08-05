<?php

namespace app\Traits;

use Firebase\JWT\JWT;

trait Tokenable
{
    public static function createToken($user_id)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        $key = $_ENV['APP_KEY'];
        $payload = array(
            "user_id" => $user_id,
            "iat" => $issuedAt,
            "exp" => $expirationTime
        );

//        $GLOBALS['pdo']->beginTransaction();
//        $stmt = $GLOBALS['pdo']->prepare("INSERT INTO tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
//        $stmt->execute([
//            "user_id" => $user_id,
//            "token" => $jwt,
//            "expires_at" => date("Y-m-d H:i:s", $expirationTime)
//        ]);
//        $GLOBALS['pdo']->commit();

        return JWT::encode($payload, $key, 'HS256');
    }
}