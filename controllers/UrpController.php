<?php
namespace controllers;

use services\AccountService;
use services\CasService;
use services\UrpService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UrpController
 * @property AccountService $accountService
 * @property CasService $casService
 * @property UrpService $urpService
 */
class UrpController extends BaseController
{
    /**
     * 获取全部成绩
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getGrade(Request $request, Response $response):Response
    {
        $data = $request->getParsedBody();
        $grades = $this->urpService->getAllGrade($data['user_id']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $grades
        ]);
    }

    /**
     * 获取当前成绩
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCurrentGrade(Request $request, Response $response):Response
    {
        $data = $request->getParsedBody();
        $grades = $this->urpService->getCurrentGrade($data['user_id']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $grades
        ]);
    }

    /**
     * 获取不及格成绩
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getFailGrade(Request $request, Response $response):Response
    {
        $data = $request->getParsedBody();
        $grades = $this->urpService->getFailingGrade($data['user_id']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $grades
        ]);
    }

    /**
     * 获取课表
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCurriculum(Request $request, Response $response):Response
    {
        $data = $request->getParsedBody();
        $curriculum = $this->urpService->getCurriculum($data['user_id']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $curriculum
        ]);
    }
}

