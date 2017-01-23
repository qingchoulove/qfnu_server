<?php
namespace controllers;

use common\Component;

class BaseController extends Component {

    public function __get($name) {
        return $this->get($name);
    }
}
