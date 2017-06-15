<?php
namespace middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 日志中间件
 * 在请求到达时记录请求信息
 */
class LoggerMiddleware extends BaseMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $headers = $request->getHeaders();
        $parsedBody = $request->getParsedBody();
        $this->logger->info($method . ':' . $uri . "\r\nbody:" . json_encode($parsedBody));
        $response = $next($request, $response);
        return $response;
    }
}
