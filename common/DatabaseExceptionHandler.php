<?php
namespace common;
use Exception;

//待开发

class DatabaseExceptionHandler implements \Illuminate\Contracts\ExceptionHandler
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