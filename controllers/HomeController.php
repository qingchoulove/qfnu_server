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
        // return $response->withJson($result);
        $result = $this->urpService->getUserInfo('2012416747');
        return $response->withJson($result);
    }
}
