<?php

namespace app\Http\Requests;

use app\Contracts\Request;

class UserLoginRequest extends Request
{
    public function rules()
    {
        return [
            'email' => 'required | email | exists: users,email',
            'password' => 'required | secure',
        ];
    }
}