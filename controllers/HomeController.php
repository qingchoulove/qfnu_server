<?php
namespace controllers;

use common\Util;

class HomeController extends BaseController
{
    
    public function index () {
        $res = $this->casService->login('2012416747', '930528');
        Util::Dump($res);
    }
}
