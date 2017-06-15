<?php
namespace common\exceptions;

use Slim\Http\Request;
use Slim\Http\Response;

class RouteErrorHandler
{
    public function __invoke(Request $request, Response $response, $method = null)
    {
        if ($method != null) {
            throw new NotFoundException("接口只允许" . implode(',', $method));
        }
        throw new NotFoundException();
    }
}