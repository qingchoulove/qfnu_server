<?php
namespace controllers;

use EasyWeChat\Core\Exception;
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

    public function text($request, $response)
    {
        $data = (new IDMustBePositiveInt())->scene("text")->validate();
        if(!$data) {
            throw new Exception('失败');
        }
        //TODO 作相应处理
        return $response->withJson($data);

    }
}
