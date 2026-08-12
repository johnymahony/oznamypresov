<?php

namespace App\Http\Ajax;

use App\Cityzen;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class SaveEmails
{
    public function handle($params)
    {
        $cityzen             = Cityzen::find($params['cityzen']['id']);
        $cityzen->min_emails = $params['cityzen']['min_emails'];
        $cityzen->day_emails = $params['cityzen']['day_emails'];
        $cityzen->step       = 'end';
        $cityzen->first_time       = 0;
        $cityzen->save();

        $cityzen->toJavascript();

        return $cityzen;
    }
}