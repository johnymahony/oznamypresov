<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
    public function handle($module, $method)
    {
        $class = resolve("\App\Api\\" . ucfirst($module) . '\\' . ucfirst($method));
        try {
            return $class->handle(request()->post());
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code'  => $e->getCode()
            ];
        }
    }
}
