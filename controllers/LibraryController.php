<?php
namespace controllers;

use common\exceptions\FieldNotValidException;
use services\LibService;
use Slim\Http\Request;
use Slim\Http\Response;
use validators\LibraryValidator;

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
     * @throws FieldNotValidException
     */
    public function searchBook(Request $request, Response $response):Response
    {
        $userInfo = $this->get('session');
        $data = $request->getParsedBody();
        $validator = new LibraryValidator($data);
        if (!$validator->validate()) {
            throw new FieldNotValidException('字段验证失败', $validator->getError());
        }
        $books = $this->libService->searchBook($userInfo['user_id'], $data['keyword'], $data['page']);
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $books
        ]);
    }
}
