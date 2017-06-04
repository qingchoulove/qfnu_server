<?php
namespace controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use services\AccountService;
use services\CasService;
use common\Util;
use validators\LoginValidator;

/**
 * 首页控制器
 * @property AccountService $accountService
 * @property CasService $casService
 */
class HomeController extends BaseController
{
    /**
     * 工程欢迎页
     * @param Request $request
     * @param Response $response
     * @return Response
     */
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
     * @return Response
     */
    public function login(Request $request, Response $response)
    {


        $data = $request->getParsedBody();
        //验证提交的登录信息
        $validator = (new LoginValidator($data));

        if (!$validator->validate()) {
            $result = [
                'status' => false,
                'message' => $validator->getError()
            ];
            return $response->withJson($result);
        }
        $data = $validator->getAvailableAttribute();
        //测试是否验证成功  请打开注释
        //return $response->withJson($data);


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
                'data' => $token
            ];
        }
        return $response->withJson($result);
    }

    /**
     * 查询是否需要验证码
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function captcha(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $result['is_need'] = $this->casService->needCaptcha($data['user_id']);
        if ($result['is_need']) {
            $result['captcha'] = $this->casService->getCaptcha($data['user_id']);
        }
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $result
        ]);
    }
}

