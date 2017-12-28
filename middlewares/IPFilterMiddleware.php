<?php
namespace middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use common\Util;

class IPFilterMiddleware extends BaseMiddleware
{
    private $path = [];
    private $allow = [];
    private $deny = [];

    /**
     * 构造函数
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $path = $config['path'];
        if (!empty($path)) {
            $this->path = is_string($path) ? [$path] : $path;
        }
        $allow = $config['allow'];
        if (!empty($allow)) {
            $this->allow = is_string($allow) ? [$allow] : $allow;
        }
        $deny = $config['deny'];
        if (!empty($deny)) {
            $this->deny = is_string($deny) ? [$deny] : $deny;
        }
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];
        $clinetIp = $forwarded ?? $remote;
        if (empty($clinetIp)) {
            return $response->withStatus(403)->withJson(['status' => false, 'message' => '爬虫嫌疑']);
        }
        foreach ($this->path as $key => $value) {
            if ($value !== $path) {
                continue;
            }
            if (!empty($this->allow) && !in_array($clinetIp, $this->allow)) {
                return $response->withStatus(403)->withJson(['status' => false, 'message' => '不属于白名单']);
            }
            if (!empty($this->deny) && in_array($clinetIp, $this->deny)) {
                return $response->withStatus(403)->withJson(['status' => false, 'message' => '黑名单限制']);
            }
        }
        $response = $next($request, $response);
        return $response;
    }
}
