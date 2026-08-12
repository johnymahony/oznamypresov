<?php

namespace App\Http\Ajax;

use App\Board;
use App\Cityzen;
use App\Log;
use App\Parcel;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class Help
{
    /**
     * @param $params
     *
     * @throws \Exception
     */
    public function handle($params)
    {
        $board   = Board::find($params['board_id']);
        /** @var Cityzen $cityzen */
        $cityzen = Cityzen::where(['email' => config('app.admin_email')])->first();
        if (empty($cityzen)) {
            throw new \Exception("Chyba: adresát neznámy. Správa nebola odoslaná.");
        }
        $cityzen->sendEmail('help', [
            'board'   => $board,
            'message' => $params['message'],
        ]);
    }

}