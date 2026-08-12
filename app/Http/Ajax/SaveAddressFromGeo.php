<?php

namespace App\Http\Ajax;

use App\Cityzen;
use App\Street;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class SaveAddressFromGeo
{
    public function handle($params)
    {
        $street = Street::find($params['number']);

        $cityzen            = Cityzen::find($params['cityzen_id']);
        $cityzen->street_id = $street->id;
        $cityzen->latitude = $street->latitude;
        $cityzen->longitude = $street->longitude;
        $cityzen->step      = 'categories';
        $cityzen->save();
        $cityzen->toJavascript();

        return $cityzen;
    }
}