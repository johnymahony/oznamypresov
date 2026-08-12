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
class LoadPopup
{
    public function handle($params)
    {
        return view('partial.board-popup', [
            'board' => Board::find($params['id'])
        ])->render();
    }
}