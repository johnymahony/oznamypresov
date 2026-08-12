<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraceLog extends Model
{
    public static $contextModelId;
    public static $contextModelName;
    public static $contextComment;

    public static function setContext(Model $model, $comment = null)
    {
        if (!empty(static::$contextComment)) {
            static::$contextComment .= ', ';
        }
        static::$contextComment .= $comment;
        static::$contextModelId = $model->id;
        static::$contextModelName = getModelName($model);
    }
}
