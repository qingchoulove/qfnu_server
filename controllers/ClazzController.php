<?php

namespace controllers;

use common\exceptions\FieldNotValidException;
use services\ClazzService;
use Slim\Http\Request;
use Slim\Http\Response;
use validators\ClazzValidator;

/**
 * Class ClazzController
 * @package controllers
 * @property ClazzService clazzService
 */
class ClazzController extends BaseController
{
    /**
     * 查询分班
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws FieldNotValidException
     */
    public function getClazz(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $validator = new ClazzValidator($params);
        if (!$validator->validate()) {
            throw new FieldNotValidException('参数错误', $validator->getErrors());
        }
        $data = $this->clazzService->getClazzByCodeAndName($params['code'], $params['name']);
        if (!empty($data)) {
            //身份证出生日期填充*
            $start = substr($data['code'], 0, 6);
            $end = substr($data['code'], -4);
            $data['code'] = $start . '********' . $end;
        }
        return $response->withJson([
            'status' => true,
            'message' => '获取成功',
            'data' => $data
        ]);
    }
}
