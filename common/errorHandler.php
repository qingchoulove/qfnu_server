<?php

namespace common;

class errorHandler {

    private static $app;
    private static $displayErrorDetails;

    public function __construct($app) {
        static::$app = $app;
        static::$displayErrorDetails = $app['settings']['displayErrorDetails'];
    }

    public function __invoke($request, $response, $exception) {
        $body = [
            'message' => $exception->getMessage()
        ];
        if (static::$displayErrorDetails) {
            $body = array_merge($body, [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ]);
        }
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($body));
   }
}