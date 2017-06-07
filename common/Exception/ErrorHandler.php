<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 7:24
 */

namespace common\exception;

use Exception;
use common\Component;

class ErrorHandler extends Component
{
    private $httpCode = 500;

    public function __invoke($request, $response, Exception $exception)
    {

        $logger = $this->get('logger');
        $displayErrorDetails = $this->get('settings')['displayErrorDetails'];

        if ($exception instanceof ParameterException) {
            $result = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'detail' => $exception->getDetail()
            ];
            $this->httpCode = $exception->getHttpCode();
        } else if ($exception instanceof BaseException) {
            //如果是自定义异常，动态返回信息
            $result = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ];
            $this->httpCode = $exception->getHttpCode();
        } else {
            //如果是系统异常，返回统一码
            $result = [
                'code' => 500,
                'message' => $displayErrorDetails ? $exception->getMessage() : '服务器错误'
            ];
            $this->httpCode = 500;
        }

        $other = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ];

        $logger->error(json_encode(array_merge($result, $other)));

        if ($displayErrorDetails) {
            $result = array_merge($result, $other);
        }

        return $response
            ->withStatus($this->httpCode)
            ->withJson($result);
    }
}