<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_DONE = 'done';
    public $guarded = [];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * @param $text
     * @param null $boardId
     * @param null $meta
     *
     * @return Problem
     */
    public static function make($text, $boardId = null, $meta = null)
    {
        return Problem::create([
            'text' => $text,
            'board_id' => $boardId,
            'meta' => $meta,
            'status' => Problem::STATUS_NEW
        ]);
    }

}
