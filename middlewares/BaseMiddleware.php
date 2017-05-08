<?php
namespace middlewares;

use common\Component;

class BaseMiddleware extends Component
{

    public function __construct($c)
    {
        $this->setContainer($c);
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}
