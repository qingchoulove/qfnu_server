<?php
namespace middlewares;

class BaseMiddleware {
    
    protected static $app;

    public function __construct($c) {
        static::$app = $c;
    }
}