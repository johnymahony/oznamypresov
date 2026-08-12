<?php

namespace App;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{


    public $fillable = ['number', 'points'];

    public static function boot()
    {
        parent::boot();
        Parcel::creating(function (Parcel $parcel) {
            if ( ! preg_match("/[a-zA-Z]/", $parcel->number)) {
                $parcel->number = "C {$parcel->number}";
            }
        });
        Parcel::created(function (Parcel $parcel) {
            $parcel->board->resolveMarker();
        });
        Parcel::deleted(function (Parcel $parcel) {
            $parcel->board->resolveMarker();
        });
        Parcel::updated(function (Parcel $parcel) {
            $parcel->board->resolveMarker();
        });
    }

    /**
     * @param $number
     *
     * @throws UserException
     */
    public static function checkNumber($number)
    {
        Parcel::normalize($number);
        if (is_numeric($number)) {
            $number = "C $number";
        }

        if (preg_match("/^(\d+)(\s*)\/(\s*)(\d+)/", $number, $matches)) {
            $number = "C $matches[1]/$matches[4]";
        }
        //                      1           2   3    4 5
        //                      -c       1234      /    45
        if (preg_match("/^(.[^0-9]*)\s*(\d+)(\s*)\/(\s*)(\d+)/", $number, $matches)) {
            $number = "$matches[2]/$matches[5]";
            if (empty($matches[1])) {
                $matches[1] = 'C';
            } elseif (preg_match("/c/i", $matches[1])) {
                $matches[1] = 'C';
            } elseif (preg_match("/e/i", $matches[1])) {
                $matches[1] = 'E';
            }
            $number = "$matches[1] $number";
        }
        if ( ! preg_match("/[cCeE]\s[0-9]/", $number)) {
            throw new UserException('Neznamy format parcely: ' . $number);
        }

        return $number;
    }

    public static function normalize($number)
    {
        $number = trim($number);
        $number = trim($number, "/");
        $number = str_replace('C-KN', 'C', $number);
        $number = str_replace('C - KN', 'C', $number);
        $number = str_replace('KNC', 'C', $number);
        $number = str_replace('KN-C', 'C', $number);
        $number = str_replace('KN - C', 'C', $number);

        $number = str_replace('E-KN', 'E', $number);
        $number = str_replace('E - KN', 'E', $number);
        $number = str_replace('KNE', 'E', $number);
        $number = str_replace('KN-E', 'E', $number);
        $number = str_replace('KN - E', 'E', $number);

        return $number;
    }

    public function resolveCssClass()
    {
        if (empty($this->points)) {
            return 'parcel-search';
        }
        if ($this->points == 'NA') {
            return 'not-found';
        }
        if ($this->points == 'E') {
            return 'error-found';
        }

        return 'parcel-found';
    }

    public function scopeForClass($query, $status)
    {
        switch ($status) {
            case 'parcel-search':
                $query->whereNull('points');
                break;
            case 'not-found':
                $query->where('points', 'NA');
                break;
            case 'error-found':
                $query->where('points', 'E');
                break;
            case 'parcel-found':
                $query->whereNotNull('points');
                break;
            default:
                throw new \Exception("Unable to resolve sql where for parcel status: " . $status);
        }

        return $query;
    }

    public function points()
    {
        return json_decode($this->points, 1);
    }

    public function board()
    {
        return $this->belongsTo('App\Board');
    }
}
