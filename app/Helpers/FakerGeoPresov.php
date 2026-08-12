<?php

namespace App\Helpers;

use App\Helpers\Faker\Geo;

class FakerGeoPresov extends Geo
{
    public static function bounds()
    {
        return [
            ['48.97571019275402', '21.211509704589844'],
            ['49.016087782645684', '21.28026008605957'],
        ];
    }
}
