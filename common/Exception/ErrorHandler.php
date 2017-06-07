<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 7:24
 */

namespace common\exception;


use common\Component;

class ErrorHandler extends Component
{
    private $httpCode;
    private $message;
    private $code;

    public function __invoke($request, $response, \Exception $exception)
    {

        $logger = $this->get('logger');
        $displayErrorDetails = $this->get('settings')['displayErrorDetails'];

        if ($exception instanceof BaseException) {
            //如果是自定义异常，动态返回信息
            $this->httpCode = $exception->httpCode;
            $this->message = $exception->message;
            $this->code = $exception->code;
        } else {
            //如果是系统异常，返回统一码
            $this->httpCode = 500;
            $this->message = '服务器内部错误，不想告诉你。';
            $this->code = 9999;
        }
        $result = [
            'message' => $this->message,
            'code' => $this->code
        ];
        if (!empty($exception->detail)) {
            $result['detail'] = $exception->detail;
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