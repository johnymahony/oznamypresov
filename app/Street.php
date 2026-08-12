<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Street extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public static function toJavascript()
    {
        return Street::select(DB::raw('distinct(name)'))->orderBy('name')->get();
    }
}
