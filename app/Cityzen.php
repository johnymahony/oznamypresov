<?php

namespace App;

use App\Traits\Emailable;
use App\Traits\TraceLog;
use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cityzen extends Model
{
    use HasFactory, Taggable, Emailable, TraceLog;
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    public $fillable = [
        'email',
        'hash_until',
        'hash',
        'receive_type',
        'latitude',
        'longitude',
        'radius',
        'street_id',
        'first_time',
        'day_emails',
        'min_emails',
    ];


    public function categories()
    {
        return $this->belongsToMany('App\Category', 'cityzen_categories');
    }

    public function street()
    {
        return $this->belongsTo('App\Street');
    }

    public function isSetUp()
    {
        return ! $this->first_time;
    }

    public function boards()
    {
        return $this->belongsToMany('App\Board', 'cityzen_boards');
    }

    public function toJavascript()
    {
        $this->street              = Street::find($this->street_id);
        $this->categories_selected = $this->categories->pluck('id')->toArray();
        if (empty($this->categories_selected)) {
            $this->categories_selected = Category::all()->pluck('id')->toArray();
        }
        $this->streets_selected = implode(',', $this->tags->pluck('name')->toArray());
    }
}
