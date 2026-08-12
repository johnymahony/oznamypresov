<?php

use Illuminate\Http\Request;
if (config('app.env') != 'testing') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization, X-Socket-ID');
}
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::get('/', function () {
    return "<pre>
POST user/addproject
Params: user_id, name
RESPONSE:
{
    \"user_id\": \"1\",
    \"name\": \"Nejaky projekt\",
    \"updated_at\": \"2019-10-05 14:39:21\",
    \"created_at\": \"2019-10-05 14:39:21\",
    \"id\": 3
}
------------------------------------------
POST project/addphase
Params: project_id, name
RESPONSE:
------------------------------------------
POST user/getprojects
Params: user_id
RESPONSE:
[
    {
        \"id\": 1,
        \"created_at\": \"2019-10-05 14:37:41\",
        \"updated_at\": \"2019-10-05 14:37:41\",
        \"name\": \"dfsfdf\",
        \"user_id\": 1,
        \"phase_id\": 1,
        \"phases\": [
            {
                \"id\": 1,
                \"created_at\": \"2019-10-05 14:38:12\",
                \"updated_at\": \"2019-10-05 14:38:12\",
                \"name\": \"Prva faza\",
                \"project_id\": 1
            },
            {
                \"id\": 2,
                \"created_at\": \"2019-10-05 14:38:46\",
                \"updated_at\": \"2019-10-05 14:38:46\",
                \"name\": \"Driuha nejaka faza\",
                \"project_id\": 1
            }
        ]
    },
    {
        \"id\": 2,
        \"created_at\": \"2019-10-05 14:37:51\",
        \"updated_at\": \"2019-10-05 14:37:51\",
        \"name\": \"Testovaci projekt\",
        \"user_id\": 1,
        \"phase_id\": null,
        \"phases\": []
    },
    {
        \"id\": 3,
        \"created_at\": \"2019-10-05 14:39:21\",
        \"updated_at\": \"2019-10-05 14:39:21\",
        \"name\": \"Driuha nejaka faza\",
        \"user_id\": 1,
        \"phase_id\": null,
        \"phases\": []
    }
]
------------------------------------------
POST project/setphase
Params: project_id, phase_id
RESPONSE:
------------------------------------------
</pre>";
});
Route::any('/{module}/{method}', "ApiController@handle");
