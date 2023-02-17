<?php

namespace App\Http\Middleware;

use Closure;

class GzipMiddleware
{
    public function handle($request, Closure $next): mixed
    {
        $response = $next($request);
        $content = $response->content();
        $data = gzencode($content, 9);

        return response($data)->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET',
            'Content-type' => 'application/json; charset=utf-8',
            //'Content-Length' => strlen($data),
            'Content-Encoding' => 'gzip',
        ]);
    }
}
