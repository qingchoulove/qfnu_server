<?php

namespace controllers;

use services\LibService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LibraryController
 * @package controllers
 * @property LibService $libService
 */
class LibraryController extends BaseController
{

    /**
     * 查询图书借阅信息
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getBorrowBooks(Request $request, Response $response):Response
    {
        $userInfo = $this->get('session');
        $books = $this->libService->getBorrowBooks($userInfo['user_id']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $books
        ]);
    }

    /**
     * 图书检索
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function searchBook(Request $request, Response $response):Response
    {
        $userInfo = $this->get('session');
        $data = $request->getParsedBody();
        $books = $this->libService->searchBook($userInfo['user_id'], $data['keyword'], $data['page']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $books
        ]);
    }
}