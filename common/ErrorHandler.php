<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 7:24
 */

namespace common;


class ErrorHandler extends Component
{
    private $code;
    private $msg;
    private $errorCode;

    public function __invoke($request, $response, $exception)
    {

        $logger = $this->get('logger');
        $displayErrorDetails = $this->get('settings')['displayErrorDetails'];

        if ($exception instanceof BaseException) {
            //如果是自定义异常，动态返回信息
            $this->code = $exception->code;
            $this->msg = $exception->msg;
            $this->errorCode = $exception->errorCode;
        } else {
            //如果是系统异常
            if ($displayErrorDetails) {
                //开启调试情况下，打印错误堆栈信息，不做日志处理
                throw $exception;
            } else {
                //未开启调试情况下，返回统一码，并且记录日志
                $body = [
                    'message' => $exception->getMessage()
                ];
                $detail = [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => explode("\n", $exception->getTraceAsString())
                ];
                $this->code = 500;
                $this->msg = '服务器内部错误，不想告诉你。';
                $this->errorCode = 9999;
                $logger->error(json_encode(array_merge($body, $detail)));
            }
        }

        $request = $request->getUri();

        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->getBaseUrl() . $request->getPath()
        ];
        return $response
            ->withStatus($this->code)
            ->withJson($result);
    }
}