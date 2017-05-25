<?php
namespace controllers;

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
