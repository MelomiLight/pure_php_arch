<?php

namespace app\Models;

use app\Traits\Crudable;
use app\Traits\Tokenable;

class User
{
    use Crudable, Tokenable;

    private static string $table_name = 'users';
    private static array $fields = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
    ];


}