<?php
namespace controllers;

use common\Util;

class HomeController extends BaseController
{
    
    public function index () {
        $news = $this->newsService->getNewsByType(0, 0, 10);
        $news = $this->newsService->getNewsById('405b9b6cdbfd11e694c60242ac1100021');
        Util::Dump($news);
    }
}
