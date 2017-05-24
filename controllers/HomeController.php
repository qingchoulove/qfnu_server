<?php
namespace controllers;

use validate\IDMustBePositiveInt;

class HomeController extends BaseController
{

    public function index($request, $response)
    {
        $result = [
            'status' => true,
            'message' => 'hello world'
        ];
        return $response->withJson($result);
    }

    public function text()
    {
        $result = (new IDMustBePositiveInt())->goCheck();
        if($result) {
            return '验证成功';
        } else {
            return "验证失败";
        }
    }
}
