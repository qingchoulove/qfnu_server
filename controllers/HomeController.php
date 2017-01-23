<?php
namespace controllers;

use common\Util;

class HomeController extends BaseController
{

    public function index () {

        $res = $this->urpService->getAllGrade('2012416747');
    }
}
