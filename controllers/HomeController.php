<?php
namespace controllers;

use common\Util;
use forms\TestForm;

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
