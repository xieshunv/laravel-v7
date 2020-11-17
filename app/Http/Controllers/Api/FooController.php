<?php
/**
 * ==============================================
 * demo
 * ----------------------------------------------
 * PHP version 7 Api
 * ==============================================
 * @category：  PHP
 * @author：    xieshunv <xieshun@163.com>
 * @copyright： @2020 http://www.febcms.com/
 * @version：   v1.0.0
 * @date:       2020-07-25 15:20
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\FooService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ApiException;

class FooController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var FooService
     */
    private $fooService;

    public function __construct(Request $request, FooService $fooService)
    {

        $this->request = $request;
        $this->fooService = $fooService;
    }

    public function bar()
    {
        //记录请求参数
        Log::debug('[FooController] bar request param', ['params' => $this->request->all()]);
        //获取参数
        $params = $this->request->only(
            'user_id'
        );
        //验证规则
        $rules = [
            'user_id' => 'required|int',
        ];
        //提示信息
        $messages = [
            'user_id.*' => '参数学生ID错误',
        ];
        //开始验证
        $this->apiValidate($params, $rules, $messages);

        try {
            $ret = $this->fooService->getBar($params);
            return $this->jsonSuccess(['data'=>$ret]);
        } catch (ApiException $e) {
            Log::info('[FooController] bar error', [
                'messages' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->jsonFail([
                'code' => $e->getCode(),
                'messages' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * @param string $method
     * @param array $ages
     * @return mixed|string
     */
    public function __call($method, $ages)
    {
        $ret = [
            'code' => 404,
            'messages' => 'Method:' . $method . ' does not exist !',
            'data' => []
        ];
        return $this->jsonFail($ret);
    }
}
