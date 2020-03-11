<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Formatted success response helper
     *
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess($data = [], $code = 200)
    {
        return response()
            ->json(['code' => $code,
                'success' => true,
                'data' => $data]);
    }

    /**
     * Formatted failed response helper
     *
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseFail($data = [], $code = 500)
    {
        return response()
            ->json(['code' => $code,
                'success' => false,
                'data' => $data]);
    }
}
