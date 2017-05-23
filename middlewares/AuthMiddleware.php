<?php

namespace middlewares;

/**
 * 鉴权中间件
 */
class AuthMiddleware extends BaseMiddleware
{
    const AUTH_PREFIX = 'auth_';

    public function __invoke($request, $response, $next)
    {
        $authorization = $request->getHeaderLine('Authorization');
        if (empty($authorization) ||
            !$this->cache->exists(self::AUTH_PREFIX . $authorization)) {
            return $response->withStatus(400)
                ->withJson(['status' => false, 'message' => '无权访问']);
        }
        $response = $next($request, $response);
        return $response;
    }
}
