<?php

namespace middlewares;

use common\Constants;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 鉴权中间件
 */
class AuthMiddleware extends BaseMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $authorization = $request->getHeaderLine('Authorization');
        if (empty($authorization) ||
            !$this->cache->exists(Constants::AUTH_PREFIX . $authorization)) {
            return $response->withStatus(400)
                ->withJson(['status' => false, 'message' => '无权访问']);
        }
        $response = $next($request, $response);
        return $response;
    }
}
