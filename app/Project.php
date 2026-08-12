<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    public $fillable = ['name', 'user_id', 'phase_id'];

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    public function attachments()
    {
        return $this->hasManyThrough(Attachment::class, Board::class);
    }

    /**(pani Gresova na investicnom)
     * (mestske veci)
     *
     * -
     *
     * 1. Zaciatok EIA - posudzovanie vplyvov na zivotne prostredie
     * 2. Koniec EIA - posudzovanie vplyvov na zivotne prostredie
     * 3. Zaciatok Uzemne rozhodovanie
     * 4. Koniec Uzemne rozhodovanie
     * 5 .Zaciatok Stavebne povolovanie -
     * 6. Koniec Stavebne povolovanie -
     * 7. Zaciatok Kolaudacne konanie
     * 8. Koniec Kolaudacne konanie
     *
     */
}
