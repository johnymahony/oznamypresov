<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static firstOrCreate(array $array)
 */
class StreetName extends Model
{
    public $fillable = ['po_id', 'name', 'coords'];
    public $timestamps = false;
}
