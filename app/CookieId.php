<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CookieId extends Model
{
    public $guarded = [];

    public static function setCityzen($cityzenId)
    {
        $id = CookieId::find(cookieId());
        if ( ! $id) {
            return;
        }
        if (empty($id->cityzen_id)) {
            $id->update([
                'cityzen_id' => $cityzenId
            ]);
        }
    }
}
