<?php

namespace app\Http\Requests;

use app\Contracts\Request;

class UserStoreRequest extends Request
{
    public function rules()
    {
        return [
            'username' => 'required | between:1,255 | unique: users,username',
            'first_name' => 'between:1,255',
            'last_name' => 'between:1,255',
            'email' => 'required | email | unique: users,email',
            'password' => 'required | secure',
        ];
    }

}