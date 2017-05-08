<?php
namespace controllers;

use common\Util;

class HomeController extends BaseController
{

    public function index($request, $response)
    {
        // $result = [
        //     'status' => true,
        //     'message' => 'hello world'
        // ];
        // return $response->withJson($result);
        $result = $this->libService->getBorrowBooks('2012416747');
        Util::Dump($result);
    }
}
