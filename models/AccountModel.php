<?php

namespace models;
use Illuminate\Database\Eloquent\Model;

/**
* 账号Model
*/
class AccountModel extends Model
{
    protected $table = 'accounts';
    protected $dateFormat = 'U';
}