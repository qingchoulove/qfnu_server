<?php
namespace controllers;

use validate\IDMustBePositiveInt;
use validators\MyValidator;

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

    public function text($request, $response)
    {
        $data = [
            'name' => 'badguy',
            'email' => 'kang_hui1314126.com'
        ];

        $validator = (new MyValidator())->scene('my');
        if (!$validator->validate($data)) {
            //抛出异常
            return $response->withJson($validator->getError());
        }
        return $response->withJson($validator->getAvailableAttributes());
    }
}
