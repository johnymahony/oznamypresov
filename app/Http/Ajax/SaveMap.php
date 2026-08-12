<?php

namespace App\Http\Ajax;

use App\Cityzen;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class SaveMap
{
    public function handle($params)
    {
        $cityzen            = Cityzen::find($params['cityzen_id']);
        $cityzen->radius = $params['radius'];
        $cityzen->latitude = $params['latitude'];
        $cityzen->longitude = $params['longitude'];
        $cityzen->step      = 'emails';
        $cityzen->save();

        $tags = preg_replace("/,+/", ',', @$params['streets_selected']);
        if (empty($tags)) {
            $cityzen->untag();
        } else {
            $cityzen->retag(explode(',', $tags));
        }

        $cityzen->toJavascript();

        return $cityzen;
    }
}