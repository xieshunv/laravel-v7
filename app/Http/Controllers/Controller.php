<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Msgcode\ApiCode;
use phpDocumentor\Reflection\Types\Integer;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Api参数验证
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return mixed
     * @throws ApiException
     */
    public function apiValidate($data = [], $rules = [], $messages = [])
    {
        try {
            $validator = Validator::make($data, $rules, $messages);
            $validator->validate();
            return $validator;
        } catch (ValidationException $e) {
            $ret = [
                'code' => ApiCode::PARAMS_ERROR,
                'messages' => $validator->errors()->all()[0],
                'data' => $e->validator->errors()->messages()
            ];

            return $this->jsonFail($ret);
        }
    }

    /**
     * 异常
     * @param array $data
     * @param int $code
     * @param String $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonFail(array $data)
    {
        $data['code'] = $data['code'] ?? ApiCode::FAIL_CODE;
        $data['messages'] = $data['messages'] ?? 'Fail';
        $data['data'] = $data['data'] ?? [];

        return $this->buildResponse($data);
    }

    /**
     * 成功
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonSuccess(array $data)
    {
        $data['code'] = $data['code'] ?? ApiCode::SUCCESS_CODE;
        $data['messages'] = $data['messages'] ?? 'Success';
        $data['data'] = $data['data'] ?? [];

        return $this->buildResponse($data);
    }

    /**
     * @description  返回客户端数据 生成json并输出
     * @param array $data
     * @param int $code
     * @param string $message
     * @author xieshun <2020-02-07>
     */
    public function buildResponse(array $data)
    {
        $jsonData = array(
            'code' => intval($data['code']),
            'messages' => strval($data['messages']),
            'data' => isset($data['data']) ? $data['data'] : []
        );

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
