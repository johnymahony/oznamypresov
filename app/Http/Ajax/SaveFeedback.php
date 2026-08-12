<?php

namespace App\Http\Ajax;

use App\Board;
use App\Cityzen;
use App\Log;
use App\Parcel;
use App\Street;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class SaveFeedback
{
    public function handle($params)
    {
        /** @var Cityzen $cityzen */
        $cityzen = Cityzen::where(['email' => config('app.admin_email')])->first();
        if ($cityzen) {
            $cityzen->sendEmail('ui_feedback', [
                'submit' => $params['feedback'],
            ]);
        }
    }
}