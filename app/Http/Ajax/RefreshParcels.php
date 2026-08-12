<?php

namespace App\Http\Ajax;

use App\Board;
use App\Exceptions\UserException;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class RefreshParcels
{
    public function handle($params)
    {

        $board = Board::find($params['board_id']);
        foreach ($board->parcels as $i => $parcel) {
            $parcel->points = null;
            $parcel->save();
            try {
                (new FindParcel())->handle(['parcel_id' => $parcel->id]);
                sleep(0.3);
            } catch (UserException $e) {
            }
            return;
        }
    }

}