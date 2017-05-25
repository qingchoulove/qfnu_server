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

}
