<?php

namespace App\Http\Ajax;

use App\Board;
use App\Helpers\TextToParcels;
use App\Parcel;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class AddParcelNumberFromString
{
    /**
     * @param $params
     * @return mixed
     * @throws \App\Exceptions\UserException
     * @throws \Exception
     */
    public function handle($params)
    {
        $parcelNumbers = (new TextToParcels())->resolve($params['string']);
        $board = Board::find($params['board_id']);
        $parcels = [];
        foreach ($parcelNumbers as $parcelNumber) {
            $exists = Parcel::where([
                'board_id' => $board->id,
                'number' => $parcelNumber
            ])->first();
            if (!empty($exists)) {
                continue;
            }
            $parcel = $board->parcels()->create([
                'number' => $parcelNumber
            ]);
            (new FindParcel())->handle([
                'parcel_id' => $parcel->id
            ]);
            $parcels[] = $parcels;
        }
        return $parcels;
    }
}