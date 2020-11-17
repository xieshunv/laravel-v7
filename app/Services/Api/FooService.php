<?php
namespace App\Services\Api;

use Illuminate\Support\Facades\Log;
use App\Exceptions\ApiException;

class FooService
{
    const preUserId = 100;

    /**
     * @param array $params
     * @return array
     */
    public function getBar(array $params):array
    {
        $userId = $params['user_id'] ?? 0;
        if ($userId < self::preUserId) {
            $errMgs = '不能使用预保留Id';
            throw  new ApiException($errMgs);
        }

        return $data = [
            'userName' => 'xieshunv',
            'age'=>27,
            'city'=>'Beijing'
        ];
    }
}
