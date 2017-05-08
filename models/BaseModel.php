<?php

namespace models;

use common\Util;
use Illuminate\Database\Eloquent\Model;

/**
* model基类
*/
class BaseModel extends Model
{
    protected $dateFormat = 'U';
    protected $rules = [];
    protected $errors = [];

    function __construct($attributes = []) {
        parent::__construct($attributes);
    }
}