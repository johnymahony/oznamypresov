<?php

namespace App\Observers;

use App\Board;
use App\BoardPoint;
use App\Log;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 6. 8. 2018
 * Time: 14:31
 */
class BoardPointObserver
{
    public function created(BoardPoint $point)
    {
        Log::debug("Point created " . $point->board_id);
        /** @var Board $board */
        $board = $point->board;
        $board->addManualTrace([
            'point' => [
                'from' => '',
                'to'   => __('Point added') . ': (' . $point->id . ') ' . $point->latitude . ' ' . $point->longitude
            ]
        ]);
    }

    public function deleted(BoardPoint $point)
    {
        Log::debug("Parcel deleted " . $point->board_id);
        /** @var Board $board */
        $board = $point->board;
        $board->addManualTrace([
            'point' => [
                'from' => '',
                'to'   => __('Point deleted') . ': (' . $point->id . ') ' . $point->latitude . ' ' . $point->longitude
            ]
        ]);
    }
}