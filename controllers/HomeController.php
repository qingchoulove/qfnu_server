<?php
namespace controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use services\AccountService;
use services\CasService;
use common\Util;

/**
 * 首页控制器
 * @property AccountService $accountService
 * @property CasService $casService
 */
class HomeController extends BaseController
{
    public function index(Request $request, Response $response)
    {
        $result = [
            'status' => true,
            'message' => 'hello world'
        ];
        return $response->withJson($result);
    }

    /**
     * 登录
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        //TODO: validate
        $login = $this->casService->loginCas($data['user_id'], $data['password'], $data['captcha']);
        $result = [
            'status' => false,
            'message' => '登录失败'
        ];
        if ($login) {
            $this->accountService->addAccount($data);
            $token = Util::RandomKey();
            $this->accountService->updateAccountToken($data['user_id'], $token);
            $result = [
                'status' => true,
                'message' => '登录成功',
                'data' => ['token' => $token]
            ];
        }
        return $response->withJson($result);
    }
}
