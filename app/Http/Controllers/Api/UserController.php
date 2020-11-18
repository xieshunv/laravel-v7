<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Api\UserService;

class UserController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var
     */
    private $userService;

    public function __construct(Request $request,UserService $userService)
    {
        $this->request = $request;
        $this->userService = $userService;
    }

    public function index()
    {
        $params = ['xieshunv'=>'test'];
        $ret = $this->userService->getUserList($params);
        return $this->jsonSuccess(['data'=>$ret]);
    }

}
