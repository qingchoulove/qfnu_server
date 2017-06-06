<?php

namespace common;

use Slim\Http\Request;
use Slim\Http\Response;
use Exception;

class ErrorHandler extends Component
{
    public function __invoke(Request $request, Response $response, $exception)
    {
        $logger = $this->get('logger');
        $displayErrorDetails = $this->get('settings')['displayErrorDetails'];
        // TODO: 待优化
        $body = [
            'message' => $exception->getMessage()
        ];
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
            ->withStatus(500)
            ->withJson($body);
    }
}
