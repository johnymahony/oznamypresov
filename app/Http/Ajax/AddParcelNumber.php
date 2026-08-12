<?php

namespace App\Http\Ajax;

use App\Board;
use App\Parcel;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class AddParcelNumber
{
    /**
     * @param $params
     *
     * @return mixed
     * @throws \App\Exceptions\UserException
     */
    public function handle($params)
    {
        $params['number'] = Parcel::checkNumber($params['number']);

        $board = Board::find($params['board_id']);
        if (@$params['cadasterNumber']) {
            $board->update([
                'cadaster_number' => $params['cadasterNumber']
            ]);
        }

        return $board->parcels()->create([
            'number' => $params['number']
        ]);
    }
}