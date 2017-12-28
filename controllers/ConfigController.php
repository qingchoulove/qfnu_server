<?php
namespace controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 设备获取服务端设置
 */
class ConfigController extends BaseController
{
    /**
     * 设置详情
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function detail(Request $request, Response $response):Response
    {
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => false
        ]);
    }
}
