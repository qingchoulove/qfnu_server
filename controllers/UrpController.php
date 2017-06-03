<?php
namespace controllers;

use services\UrpService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 教务接口
 * Class UrpController
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
    public function getGrade(Request $request, Response $response)
    {
        $userInfo = $this->get('session');
        $grades = $this->urpService->getAllGrade($userInfo['user_id']);
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
    public function getCurrentGrade(Request $request, Response $response)
    {
        $userInfo = $this->get('session');
        $grades = $this->urpService->getCurrentGrade($userInfo['user_id']);
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
    public function getFailGrade(Request $request, Response $response)
    {
        $userInfo = $this->get('session');
        $grades = $this->urpService->getFailingGrade($userInfo['user_id']);
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
    public function getCurriculum(Request $request, Response $response)
    {
        $userInfo = $this->get('session');
        $curriculum = $this->urpService->getCurriculum($userInfo['user_id']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $curriculum
        ]);
    }

    /**
     * 获取空闲教室
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getFreeRoom(Request $request, Response $response):Response
    {
        $data = $request->getParsedBody();
        $userInfo = $this->get('session');
        $rooms = $this->urpService->getFreeRoom($userInfo['user_id'], $data['campus'],
            $data['building'], $data['week'], $data['time'], $data['session']);

        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $rooms
        ]);
    }
}
