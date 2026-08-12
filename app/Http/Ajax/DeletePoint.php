<?php

namespace App\Http\Ajax;

use App\BoardPoint;
use App\Log;
use App\Parcel;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class DeletePoint
{
    public function handle($params)
    {
        BoardPoint::find($params['point_id'])->delete();
    }
}