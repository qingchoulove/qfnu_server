<?php
namespace common;

class Logger {

    public function __invoke($request, $response, $next)
    {
        // $response->getBody()->write('BEFORE');
        $response = $next($request, $response);
        // $response->getBody()->write('AFTER');
        return $response;
    }
}