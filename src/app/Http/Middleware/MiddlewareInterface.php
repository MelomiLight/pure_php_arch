<?php

namespace app\Http\Middleware;

interface MiddlewareInterface
{
    public function handle($request, $next);
}