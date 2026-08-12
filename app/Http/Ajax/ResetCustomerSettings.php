<?php

namespace App\Http\Ajax;

use App\Cityzen;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class ResetCustomerSettings
{
    public function handle($params)
    {
        $cityzen       = Cityzen::find($params['cityzen_id']);
        $cityzen->step = '';
        $cityzen->save();
    }
}