<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 7:23
 */

namespace common;
use Exception;

class DatabaseExceptionHandler extends ErrorHandler
{
    public function report(Exception $e)
    {
        throw $e;
    }

    public function render($request, Exception $e)
    {
        throw $e;
    }

    public function renderForConsole($output, Exception $e)
    {
        throw $e;
    }

}