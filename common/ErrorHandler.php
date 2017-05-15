<?php

namespace common;

class ErrorHandler
{

    private static $app;
    private static $displayErrorDetails;

    public function __construct($app)
    {
        static::$app = $app;
        static::$displayErrorDetails = $app['settings']['displayErrorDetails'];
    }

    public function __invoke($request, $response, $exception)
    {
        $logger = static::$app->get('logger');
        $body = [
            'message' => $exception->getMessage()
        ];
        $detail = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ];
        if (static::$displayErrorDetails) {
            $body = array_merge($body, $detail);
        }
        $logger->error(json_encode(array_merge($body, $detail)));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($body));
    }
}
