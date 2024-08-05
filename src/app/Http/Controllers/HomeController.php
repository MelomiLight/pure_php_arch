<?php

namespace app\Http\Controllers;

use app\Contracts\Request;

class HomeController
{
    public function index(Request $request)
    {
        $user = $request->user();
        dd($user->id);
    }
}