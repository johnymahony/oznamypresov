<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoardPoint extends Model
{
    public $fillable = ['latitude', 'longitude'];
    public $timestamps = false;


    public static function boot()
    {
        parent::boot();
        BoardPoint::created(function (BoardPoint $BoardPoint) {
            $BoardPoint->board->resolveMarker();
        });
        BoardPoint::deleted(function (BoardPoint $BoardPoint) {
            $BoardPoint->board->resolveMarker();
        });
        BoardPoint::updated(function (BoardPoint $BoardPoint) {
            $BoardPoint->board->resolveMarker();
        });
    }

    public function board()
    {
        return $this->belongsTo('App\Board');
    }
}
