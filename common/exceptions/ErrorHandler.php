<?php

namespace common\exceptions;

use common\Component;
use Slim\Http\Request;
use Slim\Http\Response;
use Exception;

class ErrorHandler extends Component
{
    public function __invoke(Request $request, Response $response, Exception $exception)
    {
        $logger = $this->get('logger');
        $displayErrorDetails = $this->get('settings')['displayErrorDetails'];
        $httpCode = 500;
        $body = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
        $data = [];
        if ($exception instanceof FieldNotValidException) {
            $body['errors'] = $exception->getErrorInfo();
            $httpCode = $exception->getHttpCode();
        } elseif ($exception instanceof BaseException) {
            $httpCode = $exception->getHttpCode();
        }

        $detail = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ];
        $logger->error(json_encode(array_merge($body, $detail)));
        if ($displayErrorDetails) {
            $body = array_merge($body, $detail);
        }
        return $response
            ->withStatus($httpCode)
            ->withJson($body);
    }
}
