<?php
namespace middlewares;

class LoggerMiddleware extends BaseMiddleware {

    

    public function __invoke($request, $response, $next)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $headers = $request->getHeaders();
        $parsedBody = $request->getParsedBody();
        static::$app->logger->info($method . ':' . $uri . "\r\nheader:" .json_encode($headers) . "\r\nbody:" . json_encode($parsedBody));
        $response = $next($request, $response);
        return $response;
    }
}