<?php

namespace App\Http\Ajax;

use App\Log;
use App\Parcel;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class DeleteParcel
{
    public function handle($params)
    {
        Parcel::find($params['parcel_id'])->delete();
    }
}