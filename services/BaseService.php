<?php
namespace services;

use common\Component;

class BaseService extends Component
{
    public function __get($name)
    {
        return $this->get($name);
    }
}
