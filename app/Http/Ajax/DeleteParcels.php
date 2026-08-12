<?php

namespace App\Http\Ajax;

use App\Parcel;


/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class DeleteParcels
{
    public function handle($params)
    {
        $query = Parcel::where('board_id', $params['board_id'])->forClass($params['status'])->delete();
    }
}