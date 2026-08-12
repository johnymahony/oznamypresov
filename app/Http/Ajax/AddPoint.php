<?php

namespace App\Http\Ajax;

use App\Board;
use App\Log;
use App\Parcel;
use App\Street;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class AddPoint
{
    public function handle($params)
    {

        $board = Board::find($params['board_id']);
        if (@$params['street_id']) {
            $street = Street::find($params['street_id']);
            $lat    = $street->latitude;
            $lon    = $street->longitude;
        } else {
            $lat = $params['lat'];
            $lon = $params['lng'];
        }

        return $board->points()->create([
            'latitude'  => $lat,
            'longitude' => $lon,
        ]);
    }
}