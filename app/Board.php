<?php

namespace App;

use Conner\Tagging\Taggable;
use \App\Traits\TraceLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Board extends Model
{
    use HasFactory, Taggable, TraceLog;
    const STATUS_NEW = 'new';
    const STATUS_DONE = 'done';
    const STATUS_READY = 'ready';
    const MATCH_STREET = 'street_match';
    const MATCH_CITY = 'whole_city';
    const MATCH_POINT = 'point_match';
    const MATCH_POINT_NOT_SET_UP = 'point_not_set_up';

    protected $casts = [
        'ready_at' => 'datetime'
    ];

    public $fillable = [
        'published_at',
        'description',
        'title',
        'category_id',
        'link',
        'status',
        'po_id',
        'sent',
        'ready_at',
        'cadaster_number',
        'comment',
        'whole_city',
        'not_important',
        'slug',
        'published_uet_until',
        'iniciative_type',
        'address',
        'solution',
        'person',
        'solution_state',
        'solution_date',
        'project_id',
        'latitude',
        'longitude',
        'attachment_path',
    ];

    public function attachments()
    {
        return $this->hasMany('App\Attachment');
    }

    public function points()
    {
        return $this->hasMany('App\BoardPoint');
    }

    public function parcels()
    {
        return $this->hasMany('App\Parcel');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function scopeByStreet($query, $cityzen)
    {
        $query
            ->join('tagging_tagged', function ($query) {
                $query->on('tagging_tagged.taggable_type', 'like', DB::raw("'%Board'"))
                    ->on('tagging_tagged.taggable_id', '=', 'boards.id');
            })
            ->whereIn('category_id', $cityzen->categories->pluck('id'))
            ->whereIn('tagging_tagged.tag_name', $cityzen->tags->pluck('name'));

        return $query;
    }

    public function scopeFormFilter($query)
    {
        if (request()->rok && request()->rok !== 'cele-obdobie') {
            $query->where(DB::raw("YEAR(published_at)"), request()->rok);
        }
        if (request()->kategoria) {
            $category = Category::whereSlug(request()->kategoria)->first();
            $query->where('category_id', $category->id);
        }
        $query->where('deleted', false);
    }

    public function scopeNotForWholeCity($query)
    {
        $query
            ->where('boards.whole_city', false);

        return $query;
    }

    public function scopeForWholeCity($query)
    {
        $query
            ->where('boards.whole_city', true);

        return $query;
    }

    public function scopeNotSent($query, $type)
    {
        $query
            ->where('boards.status', Board::STATUS_DONE)
            ->where('boards.ready_at', '<=', dateNow() . ' 23:59:59')
            ->where('boards.' . $type, false)
            ->orderBy('boards.category_id')
            ->orderBy('boards.published_at')
            ->groupBy('boards.id');

        return $query;
    }

    public function cityzens()
    {
        return $this->belongsToMany('App\Cityzen', 'cityzen_boards');
    }

    public function addToSitemap()
    {
        dispatchJob(new \App\Jobs\AddToSitemap($this->url(), $this->created_at));
    }

    public function url($params = false)
    {
        if ( ! auth()->guest()) {

            return url('/') . '/edit/' . $this->id;
        }

        return url('/') . '/oznam/' . $this->id . '/' . $this->slug . ($params ? '?' . http_build_query($params) : '');
    }

    public function toJavascript()
    {
        $this->points;
        $this->parcels;
        $this->parcels->each(function (Parcel $parcel) {
            $parcel->cssClass = $parcel->resolveCssClass();
        });
        $this->selected_streets = $this->tags->pluck('name')->toArray();

        return $this;
    }

    public function resolveMarker()
    {
        $point = $this->points()->first();
        if ($point) {
            $this->update([
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
            ]);

            return;
        }

        /** @var Parcel $parcel */
        $parcel = $this->parcels()->first();
        if ($parcel && $parcel->points()) {
            $point = $parcel->points()[0];
            $this->update([
                'latitude' => $point[0],
                'longitude' => $point[1],
            ]);

            return;
        }
        $this->update([
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    /**
     * @return Board
     */
    public function setWholeCity(): Board
    {
        $this->update([
            'whole_city' => 1,
            'status' => Board::STATUS_DONE,
            'ready_at' => dateTimeNow(),
        ]);

        return $this;
    }
}
