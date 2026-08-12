<?php

namespace App\Http\Ajax;

use App\Cityzen;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class SaveCategories
{
    public function handle($params)
    {
        /** @var Cityzen $cityzen */
        $cityzen = Cityzen::find($params['cityzen_id']);
        $cityzen->categories()->sync($params['categories_selected']);
        $cityzen->step = 'map';
        $cityzen->save();

        $cityzen->toJavascript();

        return $cityzen;
    }
}