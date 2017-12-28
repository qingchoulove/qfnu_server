<?php

namespace controllers;

use common\exceptions\FieldNotValidException;
use common\Util;
use validators\NoticeValidator;
use Slim\Http\Request;
use Slim\Http\Response;

class NoticeController extends BaseController
{
    /**
     * 添加通知
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function addNotice(Request $request, Response $response):Response
    {
        $params = $request->getParsedBody();
        $validator = new NoticeValidator($params, 'add');
        if (!$validator->validate()) {
            throw new FieldNotValidException('参数错误', $validator->getErrors());
        }
        $params = $validator->getAvailableAttribute();
        $params['apply_time'] = time();
        $newId = $this->newsService->addNew($params);
        return $response->withJson([
            'status' => true,
            'message' => '添加成功',
            'data' => [
                'new_id' => $newId
            ]
        ]);
    }

    /**
     * 更新通知
     *
     * @param Requset $request
     * @param Response $response
     * @return Response
     */
    public function updateNotice(Request $request, Response $response):Response
    {
        $params = $request->getParsedBody();
        $validator = new NoticeValidator($params, 'update');
        if (!$validator->validate()) {
            throw new FieldNotValidException('参数错误', $validator->getErrors());
        }
        $this->newsService->updateNew($validator->getAvailableAttribute());
        return $response->withJson([
            'status' => true,
            'message' => '更新成功',
            'data' => []
        ]);
    }

    /**
     * 删除通知
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function deleteNotice(Request $request, Response $response):Response
    {
        $params = $request->getParsedBody();
        $validator = new NoticeValidator($params, 'delete');
        if (!$validator->validate()) {
            throw new FieldNotValidException('参数错误', $validator->getErrors());
        }
        $params = $validator->getAvailableAttribute();
        $this->newsService->deleteNew($params['new_id']);
        return $response->withJson([
            'status' => true,
            'message' => '删除成功',
            'data' => []
        ]);
    }

    /**
     * 获取通知列表
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getNoticeList(Request $request, Response $response):Response
    {
        $params = $request->getParsedBody();
        $validator = new NoticeValidator($params, 'list');
        if (!$validator->validate()) {
            throw new FieldNotValidException('参数错误', $validator->getErrors());
        }
        $params = $validator->getAvailableAttribute();
        $limit = 20;
        $offset = ($params['page'] - 1) * $limit;
        $list = $this->newsService->getNewsByType(0, $offset, $limit);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $list
        ]);
    }
}
