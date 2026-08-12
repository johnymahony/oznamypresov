<?php

namespace App\Http\Ajax;

use App\Board;
use App\Log;
use App\Parcel;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class AddParcel
{
    public function handle($params)
    {
        $board = Board::find($params['board_id']);
        $coords = [];
        foreach ($params['coords'] as $coord) {
            $coords[] = [$coord['lat'], $coord['lng']];
        }
        return $board->parcels()->create([
            'points' => json_encode($coords),
            'number' => $params['board_id'] . '-' . ($board->parcels()->count() + 1)
        ]);
    }

    function GetCenterFromDegrees($data)
    {
        if (!is_array($data)) return FALSE;

        $num_coords = count($data);

        $X = 0.0;
        $Y = 0.0;
        $Z = 0.0;

        foreach ($data as $coord)
        {
            $lat = $coord[0] * pi() / 180;
            $lon = $coord[1] * pi() / 180;

            $a = cos($lat) * cos($lon);
            $b = cos($lat) * sin($lon);
            $c = sin($lat);

            $X += $a;
            $Y += $b;
            $Z += $c;
        }

        $X /= $num_coords;
        $Y /= $num_coords;
        $Z /= $num_coords;

        $lon = atan2($Y, $X);
        $hyp = sqrt($X * $X + $Y * $Y);
        $lat = atan2($Z, $hyp);

        return array($lat * 180 / pi(), $lon * 180 / pi());
    }
}