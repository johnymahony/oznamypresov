<?php

namespace App\Observers;

use App\Board;
use App\Category;
use App\Cityzen;
use App\Log;
use App\Parcel;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 6. 8. 2018
 * Time: 14:31
 */
class ParcelObserver
{
    public function created(Parcel $parcel)
    {
        Log::debug("Parcel created " . $parcel->number);
        /** @var Board $board */
        $board = $parcel->board;
        $board->addManualTrace([
            'parcel' => ['from' => '', 'to' => __('Parcel added').': '.$parcel->number.' ('.$parcel->id.')']]);
    }

    public function deleted(Parcel $parcel)
    {
        Log::debug("Parcel deleted " . $parcel->number);
        /** @var Board $board */
        $board = $parcel->board;
        $board->addManualTrace([
            'parcel' => ['from' => '', 'to' => __('Parcel deleted').': '.$parcel->number.' ('.$parcel->id.') : '.$parcel->points.'']]);
    }
}