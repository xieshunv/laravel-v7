<?php
namespace App\Services\Api;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ApiException;

class UserService
{

    /**
     * @param array $params
     * @return array
     */
    public function getUserList(array $params):array
    {
        $userList = [];
        if (empty($params)) {
            return $userList;
        }

        $userList['now_time'] = Carbon::now()->toDateTimeString();
        $userList['server_timew'] = date('Y-m-d H:i:s');

        return $userList;

    }
}
