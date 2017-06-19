<?php
namespace common\exceptions;

use Exception;

class DBErrorHandler implements \Illuminate\Contracts\Debug\ExceptionHandler
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
