<?php
namespace controllers;

use common\Util;

class HomeController extends BaseController
{

    public function index () {

        // $res = $this->urpService->getFreeRoom('2012416747', 1, 1, 1, 1);
        $res = $this->urpService->getEvaluationList('2012416747');
        Util::Dump($res);
    }
}
