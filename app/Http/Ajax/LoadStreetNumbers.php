<?php

namespace App\Http\Ajax;

use App\Board;
use App\Log;
use App\Parcel;
use App\Street;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class LoadStreetNumbers
{
    public function handle($params)
    {
        return Street::select('id', 'cs', 'co', 'latitude', 'longitude')->where('name', $params['street'])
                                                                        ->orderBy(DB::raw('CONVERT(co,UNSIGNED INTEGER)'))
                                                                        ->orderBy(DB::raw('CONVERT(cs,UNSIGNED INTEGER)'))
                                                                        ->get();
    }
}