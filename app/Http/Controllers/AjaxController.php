<?php

namespace App\Http\Controllers;

use App\Exceptions\JavascriptHandledException;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function ajax(Request $request, $method)
    {
        $classname = "\App\Http\Ajax\\$method";
        $service = new $classname();
        $result = $error = null;
        try {
            $result = $service->handle($request->all());
        } catch (JavascriptHandledException $e) {
            $error = [
                'code' => $e->getCode(),
                'message' => $e->data
            ];
        } catch (\Exception $e) {
            $error = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
        $response = [
            'error' => $error,
            'result' => $result
        ];
        return json_encode($response);
    }
}
