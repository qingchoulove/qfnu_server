<?php

namespace middlewares;

use common\Constants;

/**
 * 鉴权中间件
 */
class AuthMiddleware extends BaseMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $authorization = $request->getHeaderLine('Authorization');
        if (empty($authorization) ||
            !$this->cache->exists(Constants::AUTH_PREFIX . $authorization)) {
            return $response->withStatus(400)
                ->withJson(['status' => false, 'message' => '无权访问']);
        }
        $account = $this->cache->get(Constants::AUTH_PREFIX . $authorization);
        $this->set('session', unserialize($account));
        $response = $next($request, $response);
        return $response;
    }
}
